<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.jooplaan.com/
 * @since      1.0.0
 *
 * @package    Wp_Gloss
 * @subpackage Wp_Gloss/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Gloss
 * @subpackage Wp_Gloss/public
 * @author     Joop Laan <wp-gloss@interconnecting.systems>
 */
class Wp_Gloss_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The term.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $term    The current tern.
	 */
	private $term;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->term = '';
	}

	/**
	 * Main method to add the tooltips to the content.
	 *
	 * @since    1.0.0
	 * @param string $content       The post content.
	 */
	public function add_tooltips_to_content( $content ) {
		if ( ( is_singular() ) && ( is_main_query() ) ) {
			$terms = $this->get_ordered_term_list();

			foreach ( $terms as $key => $term ) {
				$this->term = $term;
				$pattern = "/\b$key\b/i";
				$content = preg_replace_callback(
					$pattern,
					function( $match ) {
						$replacement = '<a href="' . $this->term['link'] . '" aria-labelledby="tip-' . $this->term['id'] . '" class="wp-gloss-tooltip-wrapper wp-gloss-tooltip-trigger">';
						$replacement .= $match[0] . '<span aria-hidden="true" class="wp-gloss-tooltip" id="tip-' . $this->term['id'] . '"><strong>' . $this->term['term'] . '</strong><br>' . $this->term['excerpt'] . '</span></a>';
						return $replacement;
					},
					$content,
					1
				);
			}
		}
		return $content;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Gloss_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Gloss_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-gloss-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Gloss_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Gloss_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-gloss-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Get ordered term list.
	 *
	 * @since 0.1.0
	 */
	private function get_ordered_term_list() {
		$terms_arr = array();
		$synonyms_arr = array();
		$terms = $this->get_glossary_terms();
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				$term_key = $term['term'];
				$id = mt_getrandmax();
				$terms_arr[ $term_key ]['id'] = mt_getrandmax();
				$terms_arr[ $term_key ]['term'] = $term_key;
				$terms_arr[ $term_key ]['link'] = $term['link'];
				$terms_arr[ $term_key ]['excerpt'] = $term['excerpt'];
				$terms_arr[ $term_key ]['syonyms'] = $term['syonyms'];
				// Get the synonyms to.
				if ( count( $term['syonyms'] ) > 0 ) {
					foreach ( $term['syonyms'] as $synonym ) {
						$synonyms_arr[ $synonym ]['id'] = mt_getrandmax();
						$synonyms_arr[ $synonym ]['term'] = $term_key;
						$synonyms_arr[ $synonym ]['link'] = $term['link'];
						$synonyms_arr[ $synonym ]['excerpt'] = $term['excerpt'];
					}
				}
			}
		}
		return array_merge( $terms_arr, $synonyms_arr );
	}

	/**
	 * Get Glossary posts.
	 *
	 * @since 0.1.0
	 */
	private function get_glossary_terms() {
		$terms = array();

		// Set up query.
		$args = array(
			'post_type' => 'glossary-term',
			'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );
		$posts = $query->posts;
		foreach ( $posts as $key => $post ) {
			$id = $post->ID;
			$syonyms = array();
			$text_syonyms = get_post_meta( $id, 'wp-gloss-synonym', true );
			if ( ! empty( $text_syonyms ) ) {
				$syonyms = array_map( 'trim', explode( ',', $text_syonyms ) );
			}
			$terms[ $id ]['id'] = $id;
			$terms[ $id ]['term'] = trim( $post->post_title );
			$terms[ $id ]['link'] = get_the_permalink( $post );
			$terms[ $id ]['excerpt'] = $post->post_excerpt;
			$terms[ $id ]['syonyms'] = $syonyms;
		}
		/* Restore original Post Data */
		wp_reset_postdata();

		return $terms;
	}
}
