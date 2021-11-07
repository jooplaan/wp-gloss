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
			$wp_gloss_sensitivity = ! isset( $meta['wp-gloss-sensitivity'][0] ) ? 0 : $meta['wp-gloss-sensitivity'][0];
			$wp_gloss_term_preferred = ! isset( $meta['wp-gloss-term-preferred'][0] ) ? 0 : $meta['wp-gloss-term-preferred'][0];
			$wp_gloss_term_non_preferred = ! isset( $meta['wp-gloss-term-non-preferred'][0] ) ? 0 : $meta['wp-gloss-term-non-preferred'][0];

			wp_nonce_field( basename( __FILE__ ), 'glossary-term-meta-nonce' );
			?>

			<table class="form-table form-table-wp-gloss-admin" role="presentation">
				<tr>
					<td class="team_meta_box_td" colspan="2">
						<label for="wp-gloss-synonym"><?php esc_html_e( 'Synonyms', 'wp-gloss' ); ?></label>
					</td>
					<td colspan="4">
						<input type="text" name="wp-gloss-synonym" class="regular-text" value="<?php echo esc_html( $wp_gloss_synonym ); ?>">
						<p><small>Seperate multiple synonyms with a comma.</small></p>
					</td>
				</tr>

				<tr>
					<td class="team_meta_box_td" colspan="2">
						<label for="wp-gloss-synonym"><?php esc_html_e( 'Preferred term', 'wp-gloss' ); ?></label>
					</td>
					<td colspan="4">
						<?php
							print( wp_kses(
								$this->build_term_select( 'wp-gloss-term-preferred', $post->ID, $wp_gloss_term_preferred ),
								array(
									'select' => array(
										'name' => array(),
									),
									'option' => array(
										'name' => array(),
										'selected' => array(),
										'value' => array(),
									),
								)
							)
							);
						?>
					</td>
				</tr>
				<tr>
					<td class="team_meta_box_td" colspan="2">
						<label for="wp-gloss-synonym"><?php esc_html_e( 'Non-Preferred term', 'wp-gloss' ); ?></label>
					</td>
					<td colspan="4">
						<?php
							print( wp_kses(
								$this->build_term_select( 'wp-gloss-term-non-preferred', $post->ID, $wp_gloss_term_non_preferred ),
								array(
									'select' => array(
										'name' => array(),
									),
									'option' => array(
										'name' => array(),
										'selected' => array(),
										'value' => array(),
									),
								)
							)
							);
						?>
					</td>
				</tr>
				<tr>
					<td class="team_meta_box_td" colspan="2">
						<label for="wp-gloss-sensitivity"><?php esc_html_e( 'Sensitivity', 'wp-gloss' ); ?></label>
					</td>
					<td colspan="4">
						<select name="wp-gloss-sensitivity">
							<option value="0"
								<?php
								if ( '0' == $wp_gloss_sensitivity ) {
									echo ' selected';
								}
								?>
							><?php esc_html_e( 'Neutral', 'wp-gloss' ); ?></option>
							<option value="1"
								<?php
								if ( '1' == $wp_gloss_sensitivity ) {
									echo ' selected';
								}
								?>
							><?php esc_html_e( 'Sensitive term', 'wp-gloss' ); ?></option>
							<option value="2"
								<?php
								if ( '2' == $wp_gloss_sensitivity ) {
									echo ' selected';
								}
								?>
							><?php esc_html_e( 'Do not use', 'wp-gloss' ); ?></option>
							<option value="3"
								<?php
								if ( '3' == $wp_gloss_sensitivity ) {
									echo ' selected';
								}
								?>
							><?php esc_html_e( 'Disputed term', 'wp-gloss' ); ?></option>
						</select>
					</td>
				</tr>
			</table>

			<?php
		}

		/**
		 * Save metaboxes.
		 *
		 * @param integer $post_id The post ID.
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
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			// Get the values from the $_POST object.
			$meta['wp-gloss-synonym'] = ( isset( $_POST['wp-gloss-synonym'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-gloss-synonym'] ) ) : '' );
			$meta['wp-gloss-sensitivity'] = ( isset( $_POST['wp-gloss-sensitivity'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-gloss-sensitivity'] ) ) : '' );
			$meta['wp-gloss-term-preferred'] = ( isset( $_POST['wp-gloss-term-preferred'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-gloss-term-preferred'] ) ) : '' );
			$meta['wp-gloss-term-non-preferred'] = ( isset( $_POST['wp-gloss-term-non-preferred'] ) ? sanitize_text_field( wp_unslash( $_POST['wp-gloss-term-non-preferred'] ) ) : '' );

			foreach ( $meta as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}


		/**
		 * Build select form element for glossary terms..
		 *
		 * @param string  $name     The name of the SELECT element.
		 * @param integer $post_id  The post ID.
		 * @param integer $selected The selected.
		 *
		 * @since 0.1.0
		 */
		private function build_term_select( $name, $post_id, $selected = 0 ) {
			$terms = $this->get_glossary_terms();

			$html = '<select name="' . $name . '">';
			$html .= '<option value="0"';
			if ( 0 == $selected ) {
				$html .= ' selected="selected"';
			}
			$html .= '>' . __( 'None', 'wp-gloss' ) . '</option>';

			foreach ( $terms as $key => $value ) {
				$html .= '<option value="' . $key . '"';
				if ( $key == $selected ) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . $value['term'] . '</option>';
			}
			$html .= '</select>';
			return $html;
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
			);
			$the_query = new WP_Query( $args );
			// The Loop.
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$id = get_the_ID();
					$terms[ $id ]['term'] = get_the_title();
				}
			}

			/* Restore original Post Data */
			wp_reset_postdata();

			return $terms;
		}

	}
	$post_type_metaboxes = new Wp_Gloss_Meta_Boxes();
	$post_type_metaboxes->init();
}
