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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Main method to add the tooltips to the content.
	 *
	 * @since    1.0.0
	 * @param string $content       The post content.
	 */
	public function add_tooltips_to_content( $content ) {
		if ( ( is_singular() ) && ( is_main_query() ) ) {
			$string = $content;
			$word = 'trainman';
			$link = 'https://awintranet.local/glossary/autism/';
			$id = 399;
			$tooltip_text = 'Autism, or autism spectrum disorder (ASD), refers to a broad range of conditions characterized by challenges with social skills, repetitive behaviors, speech, and non-speaking communication.';
			$pattern = '#' . $word . '#s';

			$replacement = '<a href="' . $link . '" aria-labelledby="tip-' . $id . '" class="wp-gloss-tooltip-wrapper wp-gloss-tooltip-trigger">';
			$replacement .= $word . '<span aria-hidden="true" class="wp-gloss-tooltip" id="tip-' . $id . '">' . $tooltip_text . '</span></a>';
			$content = preg_replace( $pattern, $replacement, $string );
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

}
