jQuery( document ).ready(function($) {

    // Auto-switch preferences
    const seconds_until_timeout     = 120;        // wait this many seconds before going back to kiosk mode
    const seconds_between_stories   = 15;        // when in kiosk mode, wait this long before auto-changing stories
    // autoscroll array entries MUST be surrounded by forward slashes. they must match the string window.location.pathname to work.
    const autoscroll_to_pages       = ['/marketing-kiosk/vestibule-screen/'];  // after timeout, autoscroll these pages (in the order specified)
    const autoscroll_within_pages   = ['/marketing-kiosk/vestibule-screen/']; // if we are on these pages, it will first autoscroll inner elements until the end, then go to the next autoscroll page.

    var current_article = 1; // since nav buttons are hidden, keep track of the current visible article in js.
    /**
     * This disabled *ALL* touch interactions. It is a brute-force attempt to disable
     * pinch-zoom on the page, as the kiosk browser doesn't appear to have the
     * ability to disable this within the browser preferences.
     */

    // Turn twitter feed items into a masonry grid.
    // @TODO load more when user scrolls down.
    $('.grid').masonry({
        itemSelector: '.grid-item',
        columnWidth: 100,
        horizontalOrder: true,
        gutter: 50
    });

    $(".fancybox-tour").fancybox({
            'width' : 1800,
            'height' : 1600,
            'type' : 'iframe',
            'autoSize' : false,
            'autoDimensions' : false,
            'minHeight' : '1600',
            'maxHeight' : '1600'
        });

    $(".fancybox-tv").fancybox({
         'width' : 1600,
         'height' : 1000,
         'autoSize' : false,
         'type' : 'iframe'
    });

    // Show previous and next article on news page when user clicks the button.
    $('.arrow-prev, .arrow-next').click(function(e){
        e.preventDefault();

        var article_desired = get_article_array_number($(this).data('article-desired'));

        $('section.container article:visible').fadeOut(400, function() {
            // This function is called after the fadeOut completes, to prevent a visual jump
            $('section.container article[data-article-number="' + article_desired + '"]').fadeIn(400);
        });

    });



    /**
     * Given a desired array number, it returns the same number, or it returns either '1' or max_articles if the desired number needs to loop to the end or start
     * @param article_desired
     * @returns int
     */
    function get_article_array_number(article_desired){
        var max_articles = $('section.container article').length;
        if (article_desired < 1) {
            // loop to last when clicking 'previous' on the first article
            return max_articles;

        } else if (article_desired > max_articles) {
            // loop to first when clicking 'next' on last article
            return 1;
        } else {
            // other than the edge cases, return the article number they passed in
            return article_desired;
        }
        return 1;
    }

    /**
     * Prevent pinch-zoom.
     */
    window.addEventListener("touchstart", touchHandler, false);
    function touchHandler(event){
        if(event.touches.length > 1){
            //the event is multi-touch
            //you can then prevent the behavior
            event.preventDefault()
        }
    }



    /******
     * START Auto-switch news code.
     *
     * This automatically fades between news articles.
     * When a user interacts with the screen, this code is
     * temporarily suspended until the user has stopped
     * interacting for more than a minute.
     *
     */

    // when the page first loads, if it comes from an auto switch event, set the user interaction time to 60 seconds in the past
    var milliseconds_since_last_interaction = (new Date).getTime() - (seconds_until_timeout * 1000);


    /**
     * When the user input times out, automatically slide news articles every so often.
     *
     */
    function automation(){
        // before actually autoswitching, check every time that the
        // user hasn't interacted with the screen recently.
        var current_millisecond = (new Date).getTime();

        if ((current_millisecond - (seconds_until_timeout * 1000)) >= milliseconds_since_last_interaction){
            // if more than seconds_until_timeout seconds have passed since the last interaction,
            // we are free to run our automation code.

            // either scroll within the page, or go to the next page
            if (autoscroll_within_pages.indexOf(window.location.pathname) !== -1){
                // if the current page has autoscroll_within articles, scroll through those (the scroll_within will go to the
                // next page when it reaches the end of the within loop)
                autoslide_items();
            } else {
                // this page doesn't have scroll_within items. just go to the next page.
                go_to_next_page();
            }
        }
    }

    /**
     * Auto slide items within a page. When it reaches the last article, it won't loop but will instead go to the next page.
     */
    function autoslide_items(){

        var article_desired = get_article_array_number($('section.container article:visible .arrow-next').data('article-desired'));
        if (article_desired <= 1) {
            // this function will only be called during an auto trigger. since the page loads the first article on initial load,
            // the only time the article_desired will be set to '1' is when the news has ended and is trying to loop back to the
            // start. therefore, when the next article is '1', we have reached the end of the news, and we can continue on
            // to loading the next tab rather than looping the news.
            go_to_next_page();
        } else {
            $('section.container article:visible .arrow-next').trigger('click');
        }
    }

    /**
     * Redirects the browser to the next page in the array defined.
     */
    function go_to_next_page(){
        var autoscroll_parameter_name = 'autoscrollevent=true'; // when this is set in the url, the page knows not to wait for a user timeout when the page loads.
        var current_page_index = autoscroll_to_pages.indexOf(window.location.pathname);
        if ((current_page_index == -1) || ((current_page_index + 1) >= autoscroll_to_pages.length)){
            // page is not listed within the autoscroll_to_pages array. just go to the first page in the array.
            // Or, current page is the last page in the array. loop to the first page.
            document.location.href = autoscroll_to_pages[0] + "?" + autoscroll_parameter_name;
        } else {
            // go to the next page
            document.location.href = autoscroll_to_pages[current_page_index + 1] + "?" + autoscroll_parameter_name;
        }
    }

    // intervalID is saved in case we want to clearInterval to stop the loop.
    var intervalID = setInterval(automation ,(seconds_between_stories * 1000)); // Execute autoslide_news every 10 seconds.

    /******
     * END Auto-switch news code
     */
});
