<?php
/**
 * The main template file
 *
 * @package WordPress
 * @subpackage wp-slidebars
 * @since WP Slidebars 1.0
 */
global $wp_slidebars;
get_header(); ?>

<!-- main content -->
<div id="sb-site">
	
	<?php
		if ( have_posts() ) :
			// Start the Loop.
			while ( have_posts() ) : the_post();

				/*
				 * Include the post format-specific template for the content. If you want to
				 * use this in a child theme, then include a file called called content-___.php
				 * (where ___ is the post format) and that will be used instead.
				 */
				get_template_part( 'content', get_post_format() );
				

			endwhile;


		else :
			// If no content, include the "No posts found" template.
			echo '<p>No content found.</p>';

		endif;
	?>

</div>
<!-- /main content -->

<?php

if( in_array( $wp_slidebars['opt-layout'], array(2,4) ) ) get_sidebar('left');

if( in_array( $wp_slidebars['opt-layout'], array(3,4) ) ) get_sidebar('right');

get_footer();
