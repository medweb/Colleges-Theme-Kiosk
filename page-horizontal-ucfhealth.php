<?php
/* Template Name: Horizontal News Feed UCF Health */
get_header();
wp_dequeue_script('ucf_com_screen_engine');
wp_enqueue_script('ucf_com_screen_engine_non_interactive_ucf_health');

?>

<section class="container">

	<div class="overlay-black-fade">&nbsp;</div>

	<section class="main-content">

		<h2 class="display-1 pt-3 text-uppercase font-weight-black letter-spacing-0 ucfh-horiz-h2"><span>News, Trials & </span>Highlights</h2>
                
    <?php get_template_part( 'loop-post-ucfhealth' ); ?>

	</section>

</section>

<?php get_footer(); ?>
