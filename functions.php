<?php

get_template_part('includes/header-functions');


/**
 * Enqueues scripts and styles (javascript and css) used by the theme on every page.
 */
add_action( 'wp_enqueue_scripts', 'com_child_theme_scripts');

get_template_part('acf-fields'); //add all theme ACF settings (side & top nav)

function com_child_theme_scripts() {
	// Theme engine
	wp_enqueue_script(
		'com_child_theme_screen_engine',
		get_stylesheet_directory_uri() . '/js/engine.js',
		array('jquery'),
		filemtime( get_stylesheet_directory() . '/js/engine.js' ), // force cache invalidate if md5 changes
		true // load in footer
	);

	// masonry javascript for grid layouts
	wp_enqueue_script(
		'masonry',
		'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js',
		array(),
		null,
		true
	);

	$parent_style = 'parent-style';
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' ); // using get_TEMPLATE_directory_uri to force loading parent theme styles
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		filemtime(get_stylesheet_directory() . '/style.css' )
	);

    // fancybox
    wp_enqueue_script(
        'fancybox-style-script',
        get_stylesheet_directory_uri() . '/js/jquery.fancybox.pack.js',
        array('jquery'),
        filemtime( get_stylesheet_directory() . '/js/jquery.fancybox.pack.js' ), // force cache invalidate if md5 changes
        true // load in footer
    );
    wp_enqueue_style(
        'fancybox-style',
        get_stylesheet_directory_uri() . '/css/fancybox.css',
        null,
        filemtime(get_stylesheet_directory() . '/css/fancybox.css' )
    );


    // Register the vestibule js, but don't enqueue it. Let the template page enqueue it so it only loads on those pages.
    wp_register_script(
        'com_child_theme_screen_engine_non_interactive',
        get_stylesheet_directory_uri() . '/js/engine-non-interactive.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/engine-non-interactive.js'), // force cache invalidate if md5 changes
        true // load in footer
    );

    // Register the vestibule js, but don't enqueue it. Let the template page enqueue it so it only loads on those pages.
    wp_register_script(
        'com_child_theme_screen_engine_non_interactive_ucf_health',
        get_stylesheet_directory_uri() . '/js/engine-non-interactive-ucf-health.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/engine-non-interactive-ucf-health.js'), // force cache invalidate if md5 changes
        true // load in footer
    );

    // Register the vestibule js, but don't enqueue it. Let the template page enqueue it so it only loads on those pages.
    wp_register_script(
        'com_child_theme_screen_engine_non_interactive_nurs',
        get_stylesheet_directory_uri() . '/js/engine-non-interactive-nurs.js',
        array(),
        filemtime( get_stylesheet_directory() . '/js/engine-non-interactive-nurs.js'), // force cache invalidate if md5 changes
        true // load in footer
    );

}

// Custom nav classes for the footer touch nav using athena
function add_link_atts($atts) {
  $atts['class'] = "btn btn-primary";
  return $atts;
}
add_filter( 'nav_menu_link_attributes', 'add_link_atts');

// Custom body class for page-name and site-name
add_filter( 'body_class', 'body_class_for_pages' );

function body_class_for_pages( $classes ) {

	global $post;

	$sitename = get_bloginfo('name');
	// strip out all whitespace
	$sitename = preg_replace('/\s*/', '', $sitename);
	// convert the string to all lowercase
	$sitename_clean = strtolower($sitename);

	$classes[] = 'page-' . ($post ? ($post->post_name ?? '') : '');
	$classes[] = 'site-' . $sitename_clean ?? '';

	return $classes;

}

// Custom editor stylesheet
add_action( 'after_setup_theme', 'com_gutenberg_css' );

function com_gutenberg_css(){

	add_theme_support( 'editor-styles' ); // if you don't add this line, your stylesheet won't be added
	add_editor_style( 'editor-style.css' ); // tries to include style-editor.css directly from your theme folder

}

// Custom login screen
add_action( 'login_head', 'custom_login_style' );
function custom_login_style() {
	?>
    <style type="text/css">
        html, body {
            background: #222 !impor
            tant;
        }
        h1 a {
            display: none !important;
        }
        a:hover {
            color: #fff !important;
        }
        input[type=text]:focus,
        input[type=password]:focus,
        input[type=checkbox]:focus {
            border-color: #666 !important;
            box-shadow: 0 0 2px #ffae00 !important;
        }
        .button-primary {
            background: #ffcc00 !important;
            box-shadow: 0 1px 0 #ffae00 !important;
            border-color: #ffae00 !important;
            text-shadow: 0 -1px 1px #ffae00, 1px 0 1px #ffae00, 0 1px 1px #ffae00, -1px 0 1px #ffae00 !important;
        }
        .message {
            border-left-color: #ffcc00 !important;
        }
    </style>
	<?php
}


// Custom excerpt length for copy
add_filter( 'excerpt_length', 'new_excerpt_length' );
function new_excerpt_length( ) {
	return 25;
}

// Custom excerpt ellipses
add_filter( 'excerpt_more', 'new_excerpt_more' );
function new_excerpt_more( ) {
	return '...';
}

add_filter( "single_template", "get_custom_single_template" ) ;

?>
