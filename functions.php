<?php

///////////////////////////////////////////////////////////////////////////////////////////
// Define Constants
///////////////////////////////////////////////////////////////////////////////////////////

define( 'THEMEROOT', get_stylesheet_directory_uri() );
define( 'IMAGES', THEMEROOT . '/images' );
define( 'SCRIPTS', THEMEROOT . '/js' );
define( 'FRAMEWORK', get_template_directory() . '/framework' );

///////////////////////////////////////////////////////////////////////////////////////////
// Load framework
///////////////////////////////////////////////////////////////////////////////////////////

require_once( FRAMEWORK . '/init.php' );

///////////////////////////////////////////////////////////////////////////////////////////
// Set Up Content Width Value
///////////////////////////////////////////////////////////////////////////////////////////

if ( ! isset( $content_width ) ) {
    $content_width = 1100;
}

///////////////////////////////////////////////////////////////////////////////////////////
// Set up theme default and register various supported features
///////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////
// Register Custom Menu Function
//////////////////////////////////////////////////////////////////////

register_nav_menus( array(
    'main-nav' => __( 'Main Navigation', 'wft' ),
) );

//////////////////////////////////////////////////////////////////////
// Default Main Nav Function
//////////////////////////////////////////////////////////////////////

if ( ! function_exists( 'default_main_nav' ) ) {
    function default_main_nav() {
        echo '<ul id="main-nav" class="main-nav row">';
           wp_list_pages( 'title_li=' );
        echo '</ul>';
    }
}

/////////////////////////////////////////////////////////////////////
// Enable WordPress feature image
/////////////////////////////////////////////////////////////////////

add_theme_support( 'post-thumbnails');

///////////////////////////////////////////////////////////////////////////////////////////
// Support post formats
///////////////////////////////////////////////////////////////////////////////////////////

add_theme_support( 'post-formats',
    array(
        'gallery',
        'link',
        'image',
        'quote',
        'audio',
        'video'
    )
);

/////////////////////////////////////////////////////////////////////
// Enable automatic feed links
/////////////////////////////////////////////////////////////////////

add_theme_support( 'automatic-feed-links' );

///////////////////////////////////////
// Register Widgets
///////////////////////////////////////

if ( function_exists( 'register_sidebar' ) ) {
    register_sidebar( array(
        'name' => 'Sidebar',
        'id' => 'sidebar',
        'before_widget' => '<div id="%1$s" class="widget sidebar-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h1 class="widget-title">',
        'after_title' => '</h1>',
    ) );

    register_sidebar( array(
        'name' => 'Footer',
        'id' => 'footer',
        'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h1 class="widget-title">',
        'after_title' => '</h1>',
    ) );
}

///////////////////////////////////////
// Custom Theme Comment List Markup
///////////////////////////////////////

if ( ! function_exists( 'custom_theme_comment' ) ) {
    function custom_theme_comment($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment; 
       ?>

        <li id="comment-<?php comment_ID() ?>" <?php comment_class(); ?>>
        <p class="comment-author"> 
            <?php echo get_avatar($comment,$size='48'); ?> <?php printf( '<cite>%s</cite>', get_comment_author_link() ) ?><br />
            <small class="comment-time"><strong>
            <?php comment_date( 'M d, Y' ); ?>
            </strong> @
            <?php comment_time( 'H:i:s' ); ?>
            <?php edit_comment_link( __( 'Edit', 'wft' ),' [',']' ) ?>
            </small>
        </p>
        <div class="commententry">
            <?php if ( $comment->comment_approved == '0' ) : ?>
            <p>
                <em><?php _e( 'Your comment is awaiting moderation.', 'wft' ) ?></em>
            </p>
            <?php endif; ?>
            <?php comment_text() ?>
        </div>
        <p class="reply">
            <?php comment_reply_link( array_merge( $args, array( 'add_below' => 'comment', 'depth' => $depth, 'reply_text' => __( 'Reply', 'wft' ), 'max_depth' => $args['max_depth'] ) ) ) ?>
        </p>    
    <?php }
}

//////////////////////////////////////////////////////////
// Make theme available for translation
//////////////////////////////////////////////////////////

$lang_dir = THEMEROOT . '/languages';
load_theme_textdomain( 'wft', $lang_dir );

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Page navigation
/////////////////////////////////////////////////////////////////////////////////////////////////////

if ( ! function_exists('wft_pagenav') ) {
    function wft_pagenav($before = '', $after = '') {
        global $wpdb, $wp_query;

        $request = $wp_query->request;
        $posts_per_page = intval(get_query_var('posts_per_page'));
        $paged = intval(get_query_var('paged'));
        $numposts = $wp_query->found_posts;
        $max_page = $wp_query->max_num_pages;

        if(empty($paged) || $paged == 0) {
            $paged = 1;
        }
        $pages_to_show = apply_filters('wft_filter_pages_to_show', 8);
        $pages_to_show_minus_1 = $pages_to_show-1;
        $half_page_start = floor($pages_to_show_minus_1/2);
        $half_page_end = ceil($pages_to_show_minus_1/2);
        $start_page = $paged - $half_page_start;
        if($start_page <= 0) {
            $start_page = 1;
        }
        $end_page = $paged + $half_page_end;
        if(($end_page - $start_page) != $pages_to_show_minus_1) {
            $end_page = $start_page + $pages_to_show_minus_1;
        }
        if($end_page > $max_page) {
            $start_page = $max_page - $pages_to_show_minus_1;
            $end_page = $max_page;
        }
        if($start_page <= 0) {
            $start_page = 1;
        }

        if ($max_page > 1) {
            echo $before.'<div class="page-nav">';
            if ($start_page >= 2 && $pages_to_show < $max_page) {
                $first_page_text = "&laquo;";
                echo '<a href="'.get_pagenum_link().'" title="'.$first_page_text.'" class="number">'.$first_page_text.'</a>';
            }
            //previous_posts_link('&lt;');
            for($i = $start_page; $i  <= $end_page; $i++) {
                if($i == $paged) {
                    echo ' <span class="number current">'.$i.'</span> ';
                } else {
                    echo ' <a href="'.get_pagenum_link($i).'" class="number">'.$i.'</a> ';
                }
            }
            //next_posts_link('&gt;');
            if ($end_page < $max_page) {
                $last_page_text = "&raquo;";
                echo '<a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'" class="number">'.$last_page_text.'</a>';
            }
            echo '</div>'.$after;
        }
    }
}