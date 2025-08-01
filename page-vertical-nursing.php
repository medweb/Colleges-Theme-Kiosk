<?php
/* Template Name: Vertical News Feed Nursing */

get_header();

?>

    <section class="container">

        <h1><?php the_title(); ?></h1>

        <section class="main-content">


            <?php get_template_part( 'loop-post-nurs' ); ?>


        </section>

    </section>

<?php get_footer(); ?>
