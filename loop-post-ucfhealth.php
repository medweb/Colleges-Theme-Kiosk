<?php

namespace ucf_com_main_screen;
// pull in clinical trials from blog 1, and pull in specially tagged articles from external ucfhealth rss feed.
// mix them together here.
require_once( 'includes/simple_html_dom.php' );

$max_articles_per_source = 5; // multiply this by the number of sources to get the max total articles displayed

// must get these fields from the current page before switching blogs for all the rest of the data
$limit_trials_listing_to_specified_categories = get_field('limit_trials_listing_to_specified_categories');
$include_specified_categories = get_field('include_specified_categories');

switch_to_blog ( '1' );

###### Source 1
// Get rss feed posts.
$array_source_ucfhcrosspost_posts = [];
$external_posts = external_site_query( "https://ucfhealth.com/feed/?post_type=news&news_category=tv-news-rotation", $max_articles_per_source );

// Print out the rss news articles
if ( count($external_posts) > 0 ) {
    for ($i = 0; $i < count($external_posts); $i++ ) {

        $content = $external_posts[$i]['piece'];
        $title = $external_posts[$i]['title'];
        $image = $external_posts[$i]['image'];
        if (!($image)) {
            $image = "https://med.ucf.edu/media/2021/02/med-center-exterior-shot-for-marketing-1024x683.jpg";
        }

        $pre_title_html = "<button type='button' class='btn btn-secondary'>UCF Health News</button>";
        $post_content_html = "<p><strong>Read more news like this at ucfhealth.com/news</strong></p>";

        $array_source_ucfhcrosspost_posts[] = [
            'pre_title_html' => $pre_title_html,
            'image_url' => $image,
            'title' => $title,
            'content' => $content,
            'post_content_html' => $post_content_html
        ];

    }
} else {
    // no news from ucf-health rss feed found
}

###### Source 2
$array_source_clinical_posts = [];
$meta_query_open = array(
    // limit to trials that are either marked as open, or are marked with a date range encompassing today's date
    array(
        'relation'		=> 'OR',
        array(
            'key'		=> 'enrollment_status',
            'compare'	=> '=',
            'value'		=> 'open'
        ),
        array(
            'relation' => 'AND',
            array(
                'key'		=> 'enrollment_start_date',
                'compare'	=> '<=',
                'value'		=> date('Y-m-d'),
                'type'      => 'DATE'
            ),
            array(
                'key'		=> 'enrollment_last_date',
                'compare'	=> '>=',
                'value'		=> date('Y-m-d'),
                'type'      => 'DATE'
            ),
        ),
    ),
);

$main_query_open = array(
    'post_type'      => 'clinical-trials',
    'posts_per_page' => $max_articles_per_source,
    'meta_query' => $meta_query_open,
);

// if this page has specified certain categories only, then limit trials to just those categories
if ($limit_trials_listing_to_specified_categories && $include_specified_categories) {
    $tax_query = array(
        array(
            'taxonomy' => "clinical-trial-category",
            'terms' => $include_specified_categories,
        )
    );
    $main_query_open['tax_query'] = $tax_query;

}

// get all posts that are open for enrollment
$clinical_query = new \WP_Query($main_query_open);

if ( $clinical_query->have_posts() ) {
    while ( $clinical_query->have_posts() ) {
        $clinical_query->the_post();

        $preview = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        $image = $preview[0];

        if( get_field('short_title') ) {
            $title = get_field('short_title');
        }else{
            $title = get_the_title();
        }

        $content = get_the_content();

        $pre_title_html = "<button type='button' class='btn btn-secondary'>Clinical Trial</button>";
        $post_content_html = "
          <p><strong>For more information go to med.ucf.edu/clinical-trials</strong></p>
        ";

        $array_source_clinical_posts[] = [
            'pre_title_html' => $pre_title_html,
            'image_url' => $image,
            'title' => $title,
            'content' => $content,
            'post_content_html' => $post_content_html
        ];

    }

    /* Restore original Post Data */
    wp_reset_postdata();
} else {
    // no clinical trial posts found
}

###### Source 3 - people
$array_source_people_posts = [];

$main_query_people = array(
    'post_type'      => 'person',
    'posts_per_page' => $max_articles_per_source,
    'tax_query' => array(
        array(
            'taxonomy' => 'people_group',
            'field' => 'slug',
            'terms' => 'ucf-health-featured'
        )
    ),
);

// get all posts that are open for enrollment
$people_query = new \WP_Query($main_query_people);

if ( $people_query->have_posts() ) {
    while ( $people_query->have_posts() ) {
        $people_query->the_post();

        $preview = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        $image = $preview[0];

        if( get_field('short_title') ) {
            $title = get_field('short_title');
        }else{
            $title = get_the_title();
        }

        $content = get_the_content();

        $pre_title_html = "<button type='button' class='btn btn-secondary'>Featured Doctor</button>";

        $post_content_html = "
              <p><strong>View all of our doctors at ucfhealth.com/doctors</strong></p>
        ";

        $array_source_people_posts[] = [
            'pre_title_html' => $pre_title_html,
            'image_url' => $image,
            'title' => $title,
            'content' => $content,
            'post_content_html' => $post_content_html
        ];
    }

    /* Restore original Post Data */
    wp_reset_postdata();
} else {
    // no clinical trial posts found
}

###### Print all articles
// combine all news sources.
$array_all_articles = [];
for ($i = 0; $i < $max_articles_per_source; $i++){
    if ($array_source_ucfhcrosspost_posts[$i]){
        $array_all_articles[] = $array_source_ucfhcrosspost_posts[$i];
    }
    if ($array_source_clinical_posts[$i]){
        $array_all_articles[] = $array_source_clinical_posts[$i];
    }
    if ($array_source_people_posts[$i]){
        $array_all_articles[] = $array_source_people_posts[$i];
    }
}

/**
 * Print out all the articles, after they've been mixed.
 */
for ($i = 0; $i < sizeof($array_all_articles); $i++){
    if ($i == 0){
        $visibility = "";
    } else {
        $visibility = "display: none";
    }
    // This echo line is the actual printing of html to the page.
    echo article_html($visibility, $i+1, $array_all_articles[$i]);
}



######
###### Functions

/**
 * Returns a consistent html for content.
 * @param $visibility
 * @param $article_number
 * @param $article_details
 * @return string
 */
function article_html($visibility, $article_number, $article_details) {
    $trimmed_content = wp_trim_words( strip_shortcodes($article_details['content']) , '55' );
    $previous_article = $article_number - 1;
    $next_article = $article_number + 1;
    $article_number = (int) $article_number;
    return "
    <article style='${visibility}' data-article-number='${article_number}' >
        <div class='photo-container' style='background: #000 url(\"${article_details['image_url']}\") no-repeat center center; background-size: cover;'></div>
        <div class='excerpt'>
            ${article_details['pre_title_html']}
            <h2>${article_details['title']}</h2>
            <p>${trimmed_content}</p>
            ${article_details['post_content_html']}
        </div>
        <nav class='module-nav'>
            <div class='arrow-pagination'>
                <a class='arrow-prev' data-article-desired='${previous_article}' href='#'><span>Prev</span></a>
                <a class='arrow-next' data-article-desired='${next_article}' href='#'><span>Next</span></a>
            </div>
        </nav>
    </article>";
}

/**
 * Gets the feed from a url. It disables 'reject_unsafe_urls' to allow the request to continue.
 *
 * @param $url
 *
 * @return mixed
 */
function external_site_query_feed( $url ) {

    add_filter( 'http_request_args', __NAMESPACE__ . '\\disable_safety_filter' );
    add_filter( 'wp_feed_cache_transient_lifetime',  __NAMESPACE__ . '\\external_site_transient_lifetime'); // refresh every 10 minutes

    $feed = fetch_feed( $url );
    remove_filter( 'wp_feed_cache_transient_lifetime',  __NAMESPACE__ . '\\external_site_transient_lifetime' );
    remove_filter( 'http_request_args',  __NAMESPACE__ . '\\disable_safety_filter' );

    return $feed;
}


function external_site_query( string $rss_url, int $max_articles = 5) {
    $news_posts = [];
    $feed       = external_site_query_feed( $rss_url );
    if ( ! is_wp_error( $feed ) ) {
        $max_items  = $feed->get_item_quantity( $max_articles );
        $feed_items = $feed->get_items( 0, $max_items );

        foreach ( $feed_items as $item ) {
            /* @var \SimplePie_Item $item */

            /* get thumbnail */
            $htmlDOM = new simple_html_dom();
            $htmlDOM->load( $item->get_content() );
            $image     = $htmlDOM->find( 'img', 0 );
            $image_url = $image->src;

            $content = $item->get_content();

            $content_minus_image = wp_trim_words( $content, 45, '...' );
            if ( ! isset( $image_url ) ) {
                $image_url = plugins_url("images/default.jpg", __FILE__);
            }

            $UTC         = new \DateTimeZone( "UTC" );
            $timezoneEST = new \DateTimeZone( "America/New_York" );
            $datesort    = new \DateTime( $item->get_date( 'Y-m-d H:i:s' ), $UTC );
            $datesort->setTimezone( $timezoneEST );
            $date = new \DateTime( $item->get_date(), $UTC );
            $date->setTimezone( $timezoneEST );

            $news_posts[] = array(
                'image'     => $image_url,
                'permalink' => $item->get_link(),
                'title'     => $item->get_title(),
                'piece'     => $content_minus_image,
                'datesort'  => $datesort->format( 'Y-m-d H:i:s T' ),
                'date'      => $date->format( 'F d, Y' ),
                'class'     => 'class="news-preview-image"',
                'target'    => 'target="_blank"'
            );
        }
    }
    add_filter( 'the_excerpt_rss',  __NAMESPACE__ . '\\wcs_post_thumbnails_in_feeds' );
    add_filter( 'the_content_feed',  __NAMESPACE__ . '\\wcs_post_thumbnails_in_feeds' );

    return $news_posts;
}


/**
 * Disables the filter that prevents unsafe urls from loading.
 *
 * @param $args
 *
 * @return mixed
 */
function disable_safety_filter( $args ) {
    $args[ 'reject_unsafe_urls' ] = false;

    return $args;
}

/**
 * Returns a transient lifetime of 10 minutes
 * @return int
 */
function external_site_transient_lifetime() {
    return 600;
}
restore_current_blog();

?>
