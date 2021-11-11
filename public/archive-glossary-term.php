<?php
/**
 * The template for displaying Glossary Term overview.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Wp_Gloss
 * @subpackage Wp_Gloss/public
 * @since 1.0.0
 */

get_header();

global $wp_query;

$mypost = array(
	'post_type' => 'glossary-term',
	'nopaging' => true,
	'posts_per_page' => -1,
	'order' => 'ASC',
	'orderby' => 'title',
);


/**
 * Method to check if we have content for a char..
 *
 * @since        1.0.0
 * @param string $char       The alphabet char.
 * @param array  $items      Array with the glossary terms.
 */
function wp_gloss_have_content_with_char( $char, $items ) {
	foreach ( $items as $key => $item ) {
		if ( $char === $key ) {
			return true;
		}
	}
	return false;
}
// Get the program post for the loop.
$loop = new WP_Query( $mypost );
?>

<main id="site-content" role="main">

	<header class="archive-header has-text-align-center header-footer-group">

		<div class="archive-header-inner section-inner medium">
			<h1 class="entry-title"><?php echo wp_kses_post( __( 'Glossary', 'wp-gloss' ) ); ?></h1>
		</div><!-- .archive-header-inner -->

	</header><!-- .archive-header -->

	<div class="post-inner  ">

		<div class="entry-content">
	<?php
	$items = $loop->posts;
	$index_arr = array();

	// Build the Alphabet navigation anchors.
	foreach ( $items as $key => $item ) {
		$char = strtoupper( mb_substr( $item->post_title, 0, 1, 'UTF-8' ) );
		$term_id = $item->ID;
		$index_arr[ $char ][ $term_id ]['title'] = $item->post_title;
		$index_arr[ $char ][ $term_id ]['id'] = $item->ID;
		$index_arr[ $char ][ $term_id ]['post_excerpt'] = $item->post_excerpt;
		$index_arr[ $char ][ $term_id ]['post_content'] = $item->post_content;
		$index_arr[ $char ][ $term_id ]['link'] = get_the_permalink( $item );
	}
	$alphabeth = range( 'A', 'Z' );

	// Loop through the alphabet to display the menu.
	echo '<ul class="wp-gloss-list wp-gloss-list-alphabet reset-list-style">';
	foreach ( range( 'A', 'Z' ) as $v ) {
		echo '<li>';
		if ( wp_gloss_have_content_with_char( $v, $index_arr ) ) {
			echo '<a href="#' . $v . '">' . $v . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output
		} else {
			echo $v; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output
		}
		echo '</li>';
	}
	echo '</ul>';

	// Loop through the items to display them all.
	foreach ( $index_arr as $char => $items ) {
		echo '<h2 id="' . wp_kses_post( $char ) . '">';
		echo wp_kses_post( $char ) . '</h2>';
		echo '<ul class="wp-gloss-list wp-gloss-list-items">';
		foreach ( $items as $item ) {
			echo '<li><a href="' . wp_kses_post( $item['link'] ) . '">';
			echo wp_kses_post( $item['title'] ) . '</a></li>';
		}
		echo '</ul>';
	}
	?>

</div>
</div>
</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
