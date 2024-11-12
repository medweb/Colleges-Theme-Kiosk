<?php get_header(); ?>

    <section class="container">

        <h1><?php the_title(); ?></h1>

        <section class="main-content">

            <?php if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            endif; ?>


        </section>

    </section>

<?php get_footer(); ?>
