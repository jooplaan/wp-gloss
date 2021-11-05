<?php
/**
 * The file that defines the custom post type glossary term.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.jooplaan.com/
 * @since      1.0.0
 *
 * @package    Wp_Gloss
 * @subpackage Wp_Gloss/includes
 */

if ( ! class_exists( 'Wp_Gloss_Custom_Post_Type_Glossary_Term' ) ) {
	/**
	 * The class to create the post type.
	 *
	 * @since      1.0.0
	 * @package    Wp_Gloss
	 * @subpackage Wp_Gloss/includes
	 */
	class Wp_Gloss_Custom_Post_Type_Glossary_Term {

		/**
		 * Constructor. Called when plugin is initialised
		 */
		public function __construct() {
			// Add custom post type.
			add_action(
				'init',
				array( $this, 'wp_gloss_register_custom_post_type' )
			);

			// Add taxonomy for glossary term post type.
			add_action(
				'init',
				array( $this, 'create_wp_gloss_hierarchical_taxonomy' )
			);

		}

		/**
		 * Registers the Custom Post Type called program.
		 */
		public function wp_gloss_register_custom_post_type() {
			register_post_type(
				'glossary-term',
				array(
					'labels' => array(
						'name'               => _x( 'Glossary terms', 'post type general name', 'wp-gloss' ),
						'singular_name'      => _x( 'Glossary term', 'post type singular name', 'wp-gloss' ),
						'menu_name'          => _x( 'Glossary terms', 'admin menu', 'wp-gloss' ),
						'name_admin_bar'     => _x( 'Glossary term', 'add new on admin bar', 'wp-gloss' ),
						'add_new'            => _x( 'Add New', 'Glossary term', 'wp-gloss' ),
						'add_new_item'       => __( 'Add New Glossary term', 'wp-gloss' ),
						'new_item'           => __( 'New Glossary term', 'wp-gloss' ),
						'edit_item'          => __( 'Edit Glossary term', 'wp-gloss' ),
						'view_item'          => __( 'View Glossary term', 'wp-gloss' ),
						'all_items'          => __( 'All Glossary terms', 'wp-gloss' ),
						'search_items'       => __( 'Search Glossary terms', 'wp-gloss' ),
						'parent_item_colon'  => __( 'Parent Glossary terms:', 'wp-gloss' ),
						'not_found'          => __( 'No Glossary terms found.', 'wp-gloss' ),
						'not_found_in_trash' => __( 'No Glossary terms found in Trash.', 'wp-gloss' ),
					),
					'rewrite' => array( 'slug' => 'glossary' ),

					// Frontend.
					'has_archive'        => true,
					'public'             => true,
					'publicly_queryable' => true,

					// Admin.
					'capability_type' => 'post',
					'menu_icon'     => 'dashicons-book-alt',
					'menu_position' => 6,
					'query_var'     => true,
					'show_in_menu'  => true,
					'show_ui'       => true,
					'supports'      => array(
						'title',
						'editor',
						'excerpt',
						'thumbnail',
						// Line below makes wp-gloss available to
						// Gutenberg/Block editor.
						'show_in_rest' => true,
					),
					'taxonomies' => array( 'category-glossary' ),
				)
			);
		}


		/**
		 * Registers a category for Glossary Terms post type.
		 */
		public function create_wp_gloss_hierarchical_taxonomy() {
			$labels = array(
				'name' => _x( 'Glossary categories', 'taxonomy general name', 'wp-gloss' ),
				'singular_name' => _x( 'Glossary category', 'taxonomy singular name', 'wp-gloss' ),
				'search_items' => __( 'Search Glossary categories', 'wp-gloss' ),
				'all_items' => __( 'All Glossary categories', 'wp-gloss' ),
				'parent_item' => __( 'Parent Glossary category', 'wp-gloss' ),
				'parent_item_colon' => __( 'Parent Glossary category:', 'wp-gloss' ),
				'edit_item' => __( 'Edit Glossary category', 'wp-gloss' ),
				'update_item' => __( 'Update Glossary category', 'wp-gloss' ),
				'add_new_item' => __( 'Add New Glossary category', 'wp-gloss' ),
				'new_item_name' => __( 'New Glossary category Name', 'wp-gloss' ),
				'menu_name' => __( 'Glossary categories', 'wp-gloss' ),
			);

			// Now register the taxonomy.
			register_taxonomy(
				'category-glossary',
				array( 'glossary-term' ),
				array(
					'hierarchical' => true,
					'labels' => $labels,
					'show_ui' => true,
					'show_admin_column' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'glossary-terms' ),
				)
			);
		}

	}
	$get_glossary = new Wp_Gloss_Custom_Post_Type_Glossary_Term();
}
