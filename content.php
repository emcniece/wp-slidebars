<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage wp-slidebars
 * @since WP Slidebars 1.0
 */
?>

<div class="container">
	<div class="row">

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<header class="entry-header">
				
				<?php
					if ( is_single() ) :
						the_title( '<h1 class="entry-title">', '</h1>' );
					else :
						the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
					endif;
				?>
			</header><!-- .entry-header -->

			<?php if ( is_search() ) : ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content">
				<?php
					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'wp-slidebars' ) );
				?>
			</div><!-- .entry-content -->
			<?php endif; ?>

			<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
		</article><!-- #post-## -->

	</div> <!-- .row -->
</div> <!-- .container -->