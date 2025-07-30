<?php

namespace ucf_com_main_screen;
// pull in clinical trials from blog 1, and pull in specially tagged articles from external ucfhealth rss feed.
// mix them together here.
require_once( 'includes/simple_html_dom.php' );

$max_articles_per_source = 5; // multiply this by the number of sources to get the max total articles displayed

//switch_to_blog ( '1' );

###### Source 1
// Get rss feed posts.
$array_source_nurscrosspost_posts = [];
$external_posts = external_site_query( "https://nursing.ucf.edu/feed/?category_name=community", $max_articles_per_source );

// Print out the rss news articles
if ( count($external_posts) > 0 ) {
    for ($i = 0; $i < count($external_posts); $i++ ) {

        $content = $external_posts[$i]['piece'];
        $title = $external_posts[$i]['title'];
        $image = $external_posts[$i]['image'];
        if (!($image)) {
            $image = "https://med.ucf.edu/media/2021/02/med-center-exterior-shot-for-marketing-1024x683.jpg";
        }

        $pre_title_html = "<button type='button' class='btn btn-secondary'>College of Nursing News</button>";
        $post_content_html = "<p><strong>Read more news like this at nursing.ucf.edu/about/news-events/</strong></p>";

        $array_source_nurscrosspost_posts[] = [
            'pre_title_html' => $pre_title_html,
            'image_url' => $image,
            'title' => $title,
            'content' => $content,
            'post_content_html' => $post_content_html
        ];

    }
} else {
    // no news from nurs rss feed found
}

###### Print all articles
// combine all news sources.
$array_all_articles = [];
for ($i = 0; $i < $max_articles_per_source; $i++){
    if ($array_source_nurscrosspost_posts[$i]){
        $array_all_articles[] = $array_source_nurscrosspost_posts[$i];
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
//restore_current_blog();

?>
