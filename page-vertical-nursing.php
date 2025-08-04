<?php
/* Template Name: Vertical News Feed Nursing */

get_header();
wp_dequeue_script('com_child_theme_screen_engine');
wp_enqueue_script('com_child_theme_screen_engine_interactive_nurs');
?>

    <section class="container">

        <h1><?php the_title(); ?></h1>

        <section class="main-content">


            <?php get_template_part( 'loop-post-nurs' ); ?>


        </section>

    </section>

<?php get_footer(); ?>
