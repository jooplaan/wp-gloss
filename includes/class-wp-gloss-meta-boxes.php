<?php
/**
 * Main class for custom fields / metaboxes in custom post type glossary term.
 *
 * @link       https://www.jooplaan.com/
 * @since      1.0.0
 *
 * @package    Wp_Gloss
 * @subpackage Wp_Gloss/includes
 */

/**
 * Register metaboxes.
 *
 * @package Wp_Sdb_Import
 */
if ( ! class_exists( 'Wp_Gloss_Meta_Boxes' ) ) {

	/**
	 * The class to create the meta boxes.
	 *
	 * @since      1.0.0
	 * @package    Wp_Gloss
	 * @subpackage Wp_Gloss/includes
	 */
	class Wp_Gloss_Meta_Boxes {

		/**
		 * Constructor. Called when plugin is initialised
		 */
		public function init() {
			add_action( 'add_meta_boxes', array( $this, 'create_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
		}

		/**
		 * Register the metaboxes to be used for the program post type
		 *
		 * @since 0.1.0
		 */
		public function create_meta_boxes() {
			add_meta_box(
				'glossary-term-meta',
				'Glossary term meta data',
				array( $this, 'render_meta_boxes' ),
				'glossary-term',
				'normal',
				'high'
			);
		}

		/**
		 * The HTML for the fields.
		 *
		 * @param object $post The post WordPress object.
		 * @since 0.1.0
		 */
		public function render_meta_boxes( $post ) {

			$meta = get_post_custom( $post->ID );
			$wp_gloss_synonym = ! isset( $meta['wp-gloss-synonym'][0] ) ? '' : $meta['wp-gloss-synonym'][0];

			wp_nonce_field( basename( __FILE__ ), 'glossary-term-meta-nonce' ); ?>

			<table class="form-table">

				<tr>
					<td class="team_meta_box_td" colspan="2">
						<label for="wp-gloss-synonym"><?php esc_html_e( 'Synonym', 'wp-gloss' ); ?></label>
					</td>
					<td colspan="4">
						<input type="text" name="wp-gloss-synonym" class="regular-text" value="<?php echo esc_html( $wp_gloss_synonym ); ?>">
					</td>
				</tr>
			</table>

			<?php
		}

		/**
		 * Save metaboxes.
		 *
		 * @param integet $post_id The post ID.
		 *
		 * @since 0.1.0
		 */
		public function save_meta_boxes( $post_id ) {

			if ( ! isset( $_POST['glossary-term-meta-nonce'] ) ) {
				return $post_id;
			}

			$nonce = sanitize_text_field( wp_unslash( $_POST['glossary-term-meta-nonce'] ) );
			if ( ! isset( $_POST['glossary-term-meta-nonce'] )
				|| ! wp_verify_nonce( $nonce, basename( __FILE__ ) ) ) {
					return $post_id;
			}

			/*
			 * If this is an autosave, or doing ajax or bulk edit
			 * the form has not been submitted,so we don't want to do anything.
			 */
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
				|| ( defined( 'DOING_AJAX' ) && DOING_AJAX )
				|| isset( $_REQUEST['bulk_edit'] ) ) {
				return $post_id;
			}

			// Check permissions.
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return $post_id;
			}

			$meta['wp-gloss-synonym'] = ( isset( $_POST['wp-gloss-synonym'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-gloss-synonym'] ) ) : '' );
			foreach ( $meta as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

	}
	$post_type_metaboxes = new Wp_Gloss_Meta_Boxes();
	$post_type_metaboxes->init();
}
