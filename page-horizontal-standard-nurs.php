<?php
/* Template Name: Horizontal Page Standard Nursing */
get_header();
wp_dequeue_script('com_child_theme_screen_engine');
wp_enqueue_script('com_child_theme_screen_engine_non_interactive_nurs');

?>

<section class="container">

    <div class="overlay-black-fade">&nbsp;</div>

    <section class="main-content">

        <?php if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        endif; ?>


    </section>

</section>

<?php get_footer(); ?>
