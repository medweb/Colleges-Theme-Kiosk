<?php
/**
 * Template Name: Basic
 * Template Post Type: degree
 */
?>
<?php get_header(); the_post(); ?>

<section class="container main-container-1 p-5">

<h2 class="display-1 pt-3 text-uppercase font-weight-black letter-spacing-0">News</h2>
                
    <?php get_template_part( 'loop-post-1' ); ?>

</section>

<?php get_footer(); ?>
