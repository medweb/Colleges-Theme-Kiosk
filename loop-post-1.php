<?php
switch_to_blog ( '1' );

$taxonomy = 'news_category';
if ( ! taxonomy_exists( $taxonomy ) ) {
    // wp_query needs this taxonomy to be registered in the main blog or else the tax_query
    // will fail and render as sql "0 = 1", returning no results.
    // switch_to_blog doesn't automatically register all taxonomies, and our parent theme
    // doesn't include news_category like our old ucf-com-main theme did.

    // this can be a stub. the data exists in the database; wordpress simply needs to be
    // aware of the taxonomy in order to build a sql query.
    register_taxonomy( $taxonomy, null, [] );
}
$args = array(
	'post_type' => 'news',
	'posts_per_page' => 5,
	'tax_query' => array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'news_category',
			'field'    => 'slug',
			'terms'    => 'external-news',
			'operator' => 'NOT IN'
		),
	)
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {
	$article_count = 0;
	while ( $the_query->have_posts() ) {
		$article_count++;
		$the_query->the_post();
        $visibility = "";
        if ($article_count > 1) {
            $visibility = "display: none";
        }

		$preview = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		$image = $preview[0]; 
		$link = "https://www.youtube.com/embed/" . get_field( 'youtube_video_id' ) . '?autoplay=1&modestbranding=1&fs=0&controls=0&cc_load_policy=1&cc_lang_pref=en_US&hl=en_US&rel=0' ?>

		<article style="<?php echo $visibility;?>" data-article-number="<?php echo $article_count;?>" <?php //post_class(); ?>>

			<div class="photo-container" style="background-size: cover; background: #000 url('<?php echo $image; ?>') no-repeat center center;"><!--<img src="<?php echo $image; ?>" class="photo-prev" />--></div>

			<div class="excerpt">
				<h2><?php if( get_field('short_title') ) { echo get_field('short_title'); }else{ the_title(); } ?></h2>
				<p><?php
				$content = get_the_content();
				echo wp_trim_words( strip_shortcodes($content) , '40' ); ?></p>

				<p><strong>Continue reading visit med.ucf.edu/news</strong></p>

			</div>

			<nav class="module-nav">

				<div class="arrow-pagination">
					<a class="arrow-prev" data-article-desired="<?php echo ($article_count - 1); ?>" href="#"><span>Prev</span></a>
					<a class="arrow-next" data-article-desired="<?php echo ($article_count + 1); ?>" href="#"><span>Next</span></a>
				</div>

				<section class="read-go <?php if ( !get_field( 'youtube_video_id' ) ) { ?>no-video<?php } ?>">

					<section class="read-info">
						<h3 class="sub-title"><span>Read Full Article</span></h3>
						 <p><strong class="link"><?php the_permalink(); ?></strong></p>
<!--                        <span class="bitly">--><?php //echo do_shortcode( '[ucf_com_bitly]' . get_permalink() . '[/ucf_com_bitly]' ); ?><!--</span>-->

						<?php //echo do_shortcode( '[short_url_url]' ); ?>

					</section>

				</section>

			</nav>

		</article>

	<?php }

	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
    echo "<b>No posts to display. Please see med.ucf.edu for more.</b>";
}
restore_current_blog();
?>
