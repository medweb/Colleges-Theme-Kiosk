<?php
/* Template Name: Horizontal Page Standard */
get_header();

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
