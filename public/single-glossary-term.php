<?php
/**
 * The template for displaying single Glossary Term posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Wp_Gloss
 * @subpackage Wp_Gloss/public
 * @since 1.0.0
 */

get_header();
?>

<main id="site-content" role="main">

	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			the_post();
			?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<?php
			$entry_header_classes = '';

			if ( is_singular() ) {
				$entry_header_classes .= ' header-footer-group';
			}

			?>

			<header class="entry-header has-text-align-center<?php echo esc_attr( $entry_header_classes ); ?>">

				<div class="entry-header-inner section-inner medium">

					<?php
					/**
					 * Allow child themes and plugins to filter the display of the categories in the entry header.
					 *
					 * @since Twenty Twenty 1.0
					 *
					 * @param bool Whether to show the categories in header. Default true.
					 */
					$show_categories = apply_filters( 'wp-gloss_show_categories_in_entry_header', true );

					if ( true === $show_categories && has_category() ) {
						?>

						<div class="entry-categories">
							<span class="screen-reader-text"><?php esc_html_e( 'Categories', 'wp-gloss' ); ?></span>
							<div class="entry-categories-inner">
								<?php the_category( ' ' ); ?>
							</div><!-- .entry-categories-inner -->
						</div><!-- .entry-categories -->

						<?php
					}

					if ( is_singular() ) {
						the_title( '<h1 class="entry-title">', '</h1>' );
					} else {
						the_title( '<h2 class="entry-title heading-size-1"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
					}

					$intro_text_width = '';

					if ( is_singular() ) {
						$intro_text_width = ' small';
					} else {
						$intro_text_width = ' thin';
					}

					if ( has_excerpt() && is_singular() ) {
						?>

						<div class="intro-text section-inner max-percentage<?php echo $intro_text_width; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?>">
							<?php the_excerpt(); ?>
						</div>

						<?php
					}
					?>

				</div><!-- .entry-header-inner -->

			</header><!-- .entry-header -->
			<div class="post-inner <?php echo is_page_template( 'templates/template-full-width.php' ) ? '' : 'thin'; ?> ">

				<div class="entry-content">

					<?php
					if ( is_search() || ! is_singular() && 'summary' === get_theme_mod( 'blog_content', 'full' ) ) {
						the_excerpt();
					} else {
						the_content( __( 'Continue reading', 'wp-gloss' ) );
					}
					?>

					<h2 class="entry-title heading-size-4"><?php esc_html_e( 'Meta', 'wp-gloss' ); ?></h2>
					<?php
					// Display custom fields.
					$sensitivity_arr = get_post_meta( get_the_ID(), 'wp-gloss-sensitivity', true );
					switch ( $sensitivity_arr[0] ) {
						case 1:
							$sensitivity = __( 'Neutral', 'wp-gloss' );
							break;
						case 2:
							$sensitivity = __( 'Sensitive term', 'wp-gloss' );
							break;
						case 3:
							$sensitivity = __( 'Do not use', 'wp-gloss' );
							break;
						default:
							$sensitivity = __( 'Neutral', 'wp-gloss' );
					}
					?>
					<p><?php esc_html_e( 'Sensitivity', 'wp-gloss' ); ?>: <?php echo esc_html( $sensitivity ); ?></p>
					<?php
					// Display Preferred Term.
					$preferred_term_arr = get_post_meta( get_the_ID(), 'wp-gloss-term-preferred', true );
					if ( 0 == $preferred_term_arr ) {
						$preferred_term = __( 'None', 'wp-gloss' );
					} elseif ( get_the_ID() == $preferred_term_arr ) {
						$preferred_term = get_the_title();
					} else {
						$preferred_term = '<a href="' . esc_attr( esc_url( get_page_link( $preferred_term_arr ) ) ) . '">';
						$preferred_term .= get_the_title( $preferred_term_arr ) . '</a>';
					}
					?>
					<p><?php esc_html_e( 'Preferred term', 'wp-gloss' ); ?>: <?php echo wp_kses( $preferred_term, array( 'a' => array( 'href' => array() ) ) ); ?></p>
					<?php
					// Display Non Preferred Term.
					$non_preferred_term_arr = get_post_meta( get_the_ID(), 'wp-gloss-term-non-preferred', true );
					if ( 0 == $non_preferred_term_arr ) {
						$non_preferred_term = __( 'None', 'wp-gloss' );
					} elseif ( get_the_ID() == $non_preferred_term_arr ) {
						$non_preferred_term = get_the_title();
					} else {
						$non_preferred_term = '<a href="' . esc_attr( esc_url( get_page_link( $non_preferred_term_arr ) ) ) . '">';
						$non_preferred_term .= get_the_title( $non_preferred_term_arr ) . '</a>';
					}
					?>
					<p><?php esc_html_e( 'Non preferred term', 'wp-gloss' ); ?>: <?php echo wp_kses( $non_preferred_term, array( 'a' => array( 'href' => array() ) ) ); ?></p>


					<?php
					// Display synonyms.
					$text_syonyms = get_post_meta( get_the_ID(), 'wp-gloss-synonym', true );
					if ( $text_syonyms ) {
						?>
						<p><?php esc_html_e( 'Synonyms', 'wp-gloss' ); ?>: <?php echo esc_html( $text_syonyms ); ?></p>
						<?php
					}
					?>
				</div><!-- .entry-content -->

			</div><!-- .post-inner -->

			<div class="section-inner">
			<?php
				edit_post_link();
			?>

			</div><!-- .section-inner -->
		</article><!-- .post -->

			<?php
		}
	}

	?>

</main><!-- #site-content -->

<?php get_footer(); ?>
