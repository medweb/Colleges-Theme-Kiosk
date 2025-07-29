<?php
/* Template Name: Horizontal News Feed UCF Health */
get_header();
wp_dequeue_script('com_child_theme_screen_engine');
wp_enqueue_script('com_child_theme_screen_engine_non_interactive_ucf_health');

$classes = get_body_class();

?>

<section class="container">

	<div class="overlay-black-fade">&nbsp;</div>

	<section class="main-content">

		<h2 class="display-1 pt-3 text-uppercase font-weight-black letter-spacing-0 ucfh-horiz-h2"><span>News, Trials & </span>Highlights</h2>
                
    <?php get_template_part( 'loop-post-ucfhealth' ); ?>

	</section>

</section>

<?php if (in_array('page-template-page-horizontal-ucfhealth',$classes) && get_field( 'footer_notice' ) ) { ?>

	<div class="ucfh-notice">

		<?php echo get_field( 'footer_notice' ); ?>

	</div>

<?php } ?>

<?php get_footer(); ?>
