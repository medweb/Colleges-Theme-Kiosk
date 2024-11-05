<?php
/* Template Name: Horizontal News Feed */
get_header();
wp_dequeue_script('ucf_com_screen_engine');
wp_enqueue_script('ucf_com_screen_engine_non_interactive');

?>

<section class="container">

	<div class="overlay-black-fade">&nbsp;</div>

	<section class="main-content">

		<h1><span>Med School</span>News</h1>
                
    <?php get_template_part( 'loop-post-1' ); ?>

	</section>

</section>

<?php get_footer(); ?>
