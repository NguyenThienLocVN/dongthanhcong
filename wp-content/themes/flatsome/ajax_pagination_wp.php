<?php
/*******************
 * Load Post via ajax with Pagination - Query post
 * Author: Le Van Toan - www.levantoan.com
 ********************/

/******************
 * Thêm shortcode ajax_pagination
 ********************/
function ajax_pagination_svl( $atts ){
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 5,
            'paged' => 1,
            'post_type' => 'post'
        ), $atts,'ajax_pagination'
    );
    $posts_per_page = intval($atts['posts_per_page']);
    $paged = intval($atts['paged']);
    $post_type = sanitize_text_field($atts['post_type']);
    $allpost  = '<div id="result_ajaxp">';
    $allpost .= query_ajax_pagination( $post_type, $posts_per_page , $paged );
    $allpost .= '</div>';

    return $allpost;
}
add_shortcode('ajax_pagination', 'ajax_pagination_svl');

function query_ajax_pagination( $post_type = 'post', $posts_per_page = 5, $paged = 1){
    $args_svl = array(
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish'
    );
    $q_svl = new WP_Query( $args_svl );

    /*Tổng bài viết trong query trên*/
    $total_records = $q_svl->found_posts;

    /*Tổng số page*/
    $total_pages = ceil($total_records/$posts_per_page);

    if($q_svl->have_posts()):
        $allpost = '<div class="ajax_pagination" posts_per_page="'.$posts_per_page.'" post_type="'.$post_type.'">';
        while($q_svl->have_posts()):$q_svl->the_post();
            $allpost .= '<div class="ajaxp_list_post">';
            $allpost .= '<a href="'.get_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a> - <span>'.get_the_date().'</span>';
            $allpost .= '</div>';
        endwhile;
        $allpost .= '</div>';
        $allpost .= paginate_function( $posts_per_page, $paged, $total_records, $total_pages);
        $allpost .='<div class="loading_ajaxp"><div id="circularG"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div><div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div><div id="circularG_6" class="circularG"></div><div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div></div></div>';
    endif;wp_reset_query();

    return $allpost;
}

/******************
Function phân trang PHP có dạng 1,2,3 ...
 ********************/
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<ul class="pagination">';

        $right_links = $current_page + 3;
        $previous = $current_page - 3; //previous link
        $next = $current_page + 1; //next link
        $first_link = true; //boolean var to decide our first link

        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; //first link
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
            for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                if($i > 0){
                    $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page'.$i.'">'.$i.'</a></li>';
                }
            }
            $first_link = false; //set first link to false
        }

        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }

        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){
            $next_link = ($i > $total_pages)? $total_pages : $i;
            $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
            $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
        }

        $pagination .= '</ul>';
    }
    return $pagination; //return pagination links
}

/** Xử lý Ajax trong WordPress */
add_action( 'wp_ajax_LoadPostPagination', 'LoadPostPagination_init' );
add_action( 'wp_ajax_nopriv_LoadPostPagination', 'LoadPostPagination_init' );
function LoadPostPagination_init() {
    $posts_per_page = intval($_POST['posts_per_page']);
    $paged = intval($_POST['data_page']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $allpost = query_ajax_pagination( $post_type, $posts_per_page , $paged );
    echo $allpost;
    exit;
}

add_action( 'wp_enqueue_scripts', 'devvn_useAjaxPagination', 1 );
function devvn_useAjaxPagination() {
    /** Thêm js vào website */
    wp_enqueue_script( 'devvn-ajax', esc_url( trailingslashit( get_stylesheet_directory_uri() ) . 'AjaxPagination/ajax_pagination.js' ), array( 'jquery' ), '1.0', true );
    $php_array = array(
        'admin_ajax' => admin_url( 'admin-ajax.php' )
    );
    wp_localize_script( 'devvn-ajax', 'svl_array_ajaxp', $php_array );

    /*Thêm css vào website*/
    wp_enqueue_style( 'ajaxp', esc_url( trailingslashit( get_stylesheet_directory_uri() )) . 'AjaxPagination/Ajax_pagination.css', false);
}