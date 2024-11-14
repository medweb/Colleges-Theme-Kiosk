<?php
/* Template Name: Horizontal News Feed */
get_header();
wp_dequeue_script('com_child_theme_screen_engine');
wp_enqueue_script('com_child_theme_screen_engine_non_interactive');

?>

<section class="container">

	<div class="overlay-black-fade">&nbsp;</div>

	<section class="main-content">

		<h2 class="display-1 pt-3 text-uppercase font-weight-black letter-spacing-0 ucfh-horiz-h2"><span>Med School</span>News</h2>
                
    <?php get_template_part( 'loop-post-1' ); ?>

	</section>

</section>

<?php get_footer(); ?>
