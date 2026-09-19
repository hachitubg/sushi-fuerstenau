<?php
/**
 * Page template.
 *
 * @package Saiyoky
 */

get_header();
?>
<main id="main" class="site-main content-container">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-entry' ); ?>>
			<header class="content-entry__header"><?php the_title( '<h1>', '</h1>' ); ?></header>
			<div class="content-entry__body"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();

