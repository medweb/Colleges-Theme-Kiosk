<?php
/* Template Name: Horizontal News Feed */
get_header();
wp_dequeue_script('com_child_theme_screen_engine');
wp_enqueue_script('com_child_theme_screen_engine_non_interactive');

$classes = get_body_class();

?>

<section class="container">

	<div class="overlay-black-fade">&nbsp;</div>

	<section class="main-content">

		<?php if (in_array('site-marketingkiosk',$classes)) { ?>

			<h2 class="display-1 pt-3 text-uppercase font-weight-black letter-spacing-0 ucfh-horiz-h2"><span>Med School</span>News</h2>

		<?php } ?>

		<?php if (in_array('site-nursingkiosk',$classes)) { ?>

			<h2 class="display-1 pt-3 text-uppercase font-weight-black letter-spacing-0 ucfh-horiz-h2"><span>Nursing</span>News</h2>

		<?php } ?>

		<?php if (in_array('site-marketingkiosk',$classes)) { 

			get_template_part( 'loop-post-1' );

		 } else {

            get_template_part( 'loop-post-nurs' );

		 }?>


	</section>

</section>

<?php get_footer(); ?>
