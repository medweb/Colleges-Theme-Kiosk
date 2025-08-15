<?php
/* Template Name: Horizontal Page Static */
get_header();
wp_dequeue_script('com_child_theme_screen_engine');

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
