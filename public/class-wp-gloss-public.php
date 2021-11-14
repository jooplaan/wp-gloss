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
	 * @var      array    $term    The current term.
	 */
	private $term;

	/**
	 * Therms uses.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $terms_used    Array of terms used in content.
	 */
	private $terms_used;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->term        = array();
		$this->terms_used  = array();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-gloss-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-gloss-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Main method to add the tooltips to the content.
	 * See https://developer.wordpress.org/reference/hooks/the_content/
	 *
	 * @since 1.0.0
	 * @param string $content       The post content.
	 */
	public function add_tooltips_to_content( $content ) {
		if ( ( is_singular() ) && ( is_main_query() ) ) {
			$terms   = $this->get_ordered_term_list();
			$post_id = get_the_ID();
			foreach ( $terms as $key => $term ) {
				$this->term = $term;
				// Don't link content to itself.
				if ( $post_id !== $term['term_id'] ) {
					// Make the tooltip.
					$content = $this->create_tooltip( $content, $key );
				}
			}
			return $this->decode_tooltips_in_content( $content );
		}
		return $content;
	}

	/**
	 * Decode the encoded tooltips.
	 *
	 * @since    1.0.0
	 *
	 * @param string $content   The content.
	 */
	private function decode_tooltips_in_content( $content ) {
		$html = new simple_html_dom( $content );
		$html->load( $content );
		foreach ( $html->find( 'span[class="wp-gloss-tooltip-wrap-code"]' ) as $sp ) {
			$sp->innertext = $this->decode( $sp->innertext );
		}
		return $html;
	}

	/**
	 * Create tooltip.
	 *
	 * @since    1.0.0
	 *
	 * @param string $content   The content.
	 * @param string $key       The term label.
	 */
	private function create_tooltip( $content, $key ) {
		$html = new simple_html_dom( $content );
		$html->load( $content );
		foreach ( $html->find( 'p' ) as $p ) {
			$p->innertext = $this->preg_replace_filter( $p->innertext, $key );
		}
		foreach ( $html->find( 'li' ) as $li ) {
			$li->innertext = $this->preg_replace_filter( $li->innertext, $key );
		}
		foreach ( $html->find( 'td' ) as $td ) {
			$td->innertext = $this->preg_replace_filter( $td->innertext, $key );
		}
		return $html;
	}


	/**
	 * The regular expression to replace the word with a tooltip in the content.
	 *
	 * @since    1.0.0
	 *
	 * @param string $content   The content.
	 * @param string $key       The term label.
	 */
	private function preg_replace_filter( $content, $key ) {
		// Check if we didn't already add this term to the content.
		if ( ! array_key_exists( $this->term['term_id'], $this->terms_used ) ) {
			// Regex to search in html, skipping HTML tags.
			// See https://regex101.com/r/sF4tP4/1 for what inspired this solution.
			$pattern    = "~<[^>]*>(*SKIP)(*F)|\b$key\b~i";
			$html       = preg_replace_callback(
				$pattern,
				function( $match ) {
					// Store found term to allow only one tooltip per term per page.
					$term_id = $this->term['term_id'];
					$this->terms_used[ $term_id ] = $this->term['term'];

					// Return span with encoded tooltip link.
					return $this->create_the_tooltip_code( $match );
				},
				$content,
				1
			);
			return $html;
		}
		return $content;
	}

	/**
	 * Make span with encoded tooltip link.
	 *
	 * @since 0.1.0
	 *
	 * @param array $match       The match array.
	 */
	private function create_the_tooltip_code( $match ) {
		$replacement  = '<a href="' . $this->term['link'] . '" aria-labelledby="tip-' . $this->term['id'] . '" class="wp-gloss-tooltip-wrapper wp-gloss-tooltip-trigger">';
		$replacement .= $match[0] . '<span aria-hidden="true" class="wp-gloss-tooltip" id="tip-' . $this->term['id'] . '"><strong>' . $this->term['term'] . '</strong><br>' . $this->term['excerpt'] . '</span></a>';

		// Encode the link, to avoid creating tooltips inside this tooltip.
		return '<span class="wp-gloss-tooltip-wrap-code">' . $this->encode( $replacement ) . '</span>';
	}

	/**
	 * Encode a string.
	 *
	 * @since 0.1.0
	 *
	 * @param string $string       The string.
	 */
	private function encode( $string ) {
		return rtrim( strtr( base64_encode( $string ), '+/', '-_' ), '=' );
	}

	/**
	 * Decode a string.
	 *
	 * @since 0.1.0
	 *
	 * @param string $string       The string.
	 */
	private function decode( $string ) {
		return base64_decode( str_pad( strtr( $string, '-_', '+/' ), strlen( $string ) % 4, '=', STR_PAD_RIGHT ) );
	}

	/**
	 * Get ordered term list.
	 *
	 * @since 0.1.0
	 */
	private function get_ordered_term_list() {
		$terms_arr    = array();
		$synonyms_arr = array();
		$terms        = $this->get_glossary_terms();
		if ( count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				$term_key                          = $term['term'];
				$term_id                           = $term['id'];
				$id                                = mt_getrandmax();
				$terms_arr[ $term_key ]['id']      = mt_getrandmax();
				$terms_arr[ $term_key ]['term']    = $term_key;
				$terms_arr[ $term_key ]['link']    = $term['link'];
				$terms_arr[ $term_key ]['excerpt'] = $term['excerpt'];
				$terms_arr[ $term_key ]['syonyms'] = $term['syonyms'];
				$terms_arr[ $term_key ]['term_id'] = $term_id;
				// Get the synonyms to.
				if ( count( $term['syonyms'] ) > 0 ) {
					foreach ( $term['syonyms'] as $synonym ) {
						$synonyms_arr[ $synonym ]['id']      = mt_getrandmax();
						$synonyms_arr[ $synonym ]['term']    = $term_key;
						$synonyms_arr[ $synonym ]['link']    = $term['link'];
						$synonyms_arr[ $synonym ]['excerpt'] = $term['excerpt'];
						$synonyms_arr[ $synonym ]['term_id'] = $term_id;
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
		$args  = array(
			'post_type'      => 'glossary-term',
			'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );
		$posts = $query->posts;
		foreach ( $posts as $key => $post ) {
			$id           = $post->ID;
			$syonyms      = array();
			$text_syonyms = get_post_meta( $id, 'wp-gloss-synonym', true );
			if ( ! empty( $text_syonyms ) ) {
				$syonyms = array_map( 'trim', explode( ',', $text_syonyms ) );
			}
			$terms[ $id ]['id']      = $id;
			$terms[ $id ]['term']    = trim( $post->post_title );
			$terms[ $id ]['link']    = get_the_permalink( $post );
			$terms[ $id ]['excerpt'] = $post->post_excerpt;
			$terms[ $id ]['syonyms'] = $syonyms;
		}
		/* Restore original Post Data */
		wp_reset_postdata();

		return $terms;
	}
}
