<?php
/*
 * Author: https://levantoan.com
 * Link https://levantoan.com/xoa-bo-product-category-va-toan-bo-slug-cua-danh-muc-cha-khoi-duong-dan-cua-woocommerce/
 * */
// Remove product cat base
add_filter('term_link', 'devvn_no_term_parents', 1000, 3);
function devvn_no_term_parents($url, $term, $taxonomy) {
    if($taxonomy == 'product_cat'){
        $term_nicename = $term->slug;
        $url = trailingslashit(get_option( 'home' )) . user_trailingslashit( $term_nicename, 'category' );
    }
    return $url;
}

// Add our custom product cat rewrite rules
function devvn_no_product_cat_parents_rewrite_rules($flash = false) {
    $terms = get_terms( array(
        'taxonomy' => 'product_cat',
        'post_type' => 'product',
        'hide_empty' => false,
    ));
    if($terms && !is_wp_error($terms)){
        foreach ($terms as $term){
            $term_slug = $term->slug;
            add_rewrite_rule($term_slug.'/?$', 'index.php?product_cat='.$term_slug,'top');
            add_rewrite_rule($term_slug.'/page/([0-9]{1,})/?$', 'index.php?product_cat='.$term_slug.'&paged=$matches[1]','top');
            add_rewrite_rule($term_slug.'/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?product_cat='.$term_slug.'&feed=$matches[1]','top');
        }
    }
    if ($flash == true)
        flush_rewrite_rules(false);
}
add_action('init', 'devvn_no_product_cat_parents_rewrite_rules');
 
/*Sửa lỗi khi tạo mới taxomony bị 404*/
add_action( 'create_term', 'devvn_new_product_cat_edit_success', 10);
add_action( 'edit_terms', 'devvn_new_product_cat_edit_success', 10);
add_action( 'delete_term', 'devvn_new_product_cat_edit_success', 10);
function devvn_new_product_cat_edit_success( ) {
    devvn_no_product_cat_parents_rewrite_rules(true);
}

/*
* Code Bỏ /product/ hoặc /cua-hang/ hoặc /shop/ ... có hỗ trợ dạng %product_cat%
* Thay /cua-hang/ bằng slug hiện tại của bạn
*/
function devvn_remove_slug( $post_link, $post ) {
    if ( !in_array( get_post_type($post), array( 'product' ) ) || 'publish' != $post->post_status ) {
        return $post_link;
    }
    if('product' == $post->post_type){
        $post_link = str_replace( '/product/', '/', $post_link ); //Thay cua-hang bằng slug hiện tại của bạn
    }else{
        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
    }
    return $post_link;
}
add_filter( 'post_type_link', 'devvn_remove_slug', 10, 2 );
/*Sửa lỗi 404 sau khi đã remove slug product hoặc cua-hang*/
function devvn_woo_product_rewrite_rules($flash = false) {
    global $wp_post_types, $wpdb;
    $siteLink = esc_url(home_url('/'));
    foreach ($wp_post_types as $type=>$custom_post) {
        if($type == 'product'){
            if ($custom_post->_builtin == false) {
                $querystr = "SELECT {$wpdb->posts}.post_name, {$wpdb->posts}.ID
                            FROM {$wpdb->posts} 
                            WHERE {$wpdb->posts}.post_status = 'publish' 
                            AND {$wpdb->posts}.post_type = '{$type}'";
                $posts = $wpdb->get_results($querystr, OBJECT);
                foreach ($posts as $post) {
                    $current_slug = get_permalink($post->ID);
                    $base_product = str_replace($siteLink,'',$current_slug);
                    add_rewrite_rule($base_product.'?$', "index.php?{$custom_post->query_var}={$post->post_name}", 'top');                    
                    add_rewrite_rule($base_product.'comment-page-([0-9]{1,})/?$', 'index.php?'.$custom_post->query_var.'='.$post->post_name.'&cpage=$matches[1]', 'top');
                    add_rewrite_rule($base_product.'(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', 'index.php?'.$custom_post->query_var.'='.$post->post_name.'&feed=$matches[1]','top');
                }
            }
        }
    }
    if ($flash == true)
        flush_rewrite_rules(false);
}
add_action('init', 'devvn_woo_product_rewrite_rules');
/*Fix lỗi khi tạo sản phẩm mới bị 404*/
function devvn_woo_new_product_post_save($post_id){
    global $wp_post_types;
    $post_type = get_post_type($post_id);
    foreach ($wp_post_types as $type=>$custom_post) {
        if ($custom_post->_builtin == false && $type == $post_type) {
            devvn_woo_product_rewrite_rules(true);
        }
    }
}
add_action('wp_insert_post', 'devvn_woo_new_product_post_save');

/*chuyển 0đ thành chữ “Liên hệ”*/
function devvn_wc_custom_get_price_html( $price, $product ) {
    if ( $product->get_price() == 0 ) {
        if ( $product->is_on_sale() && $product->get_regular_price() ) {
            $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
 
            $price = wc_format_price_range( $regular_price, __( 'Free!', 'woocommerce' ) );
        } else {
            $price = '<span class="amount">' . __( 'Liên hệ', 'woocommerce' ) . '</span>';
        }
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'devvn_wc_custom_get_price_html', 10, 2 );

/*Chuyển giá thành “Liên hệ” khi hết hàng*/
function devvn_oft_custom_get_price_html( $price, $product ) {
    if ( !is_admin() && !$product->is_in_stock()) {
       $price = '<span class="amount">' . __( 'Liên hệ', 'woocommerce' ) . '</span>';
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'devvn_oft_custom_get_price_html', 99, 2 );

/**codfe.com thêm .html vào cuối sản phẩm **/
function codfe_product_permastruct_html( $post_type, $args ) {
    if ( $post_type === 'product' )
        add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%.html", $args->rewrite );
}
 
add_action( 'registered_post_type', 'codfe_product_permastruct_html', 10, 2 );

/*
 * Code bài viết liên quan
*/
function bvlq() {
	global $post;
	$tags = wp_get_post_tags($post->ID);
	if ($tags){
		$output = '<div class="relatedcat">';
		$first_tag = $tags[0]->term_id;
		
		$args=array(
			'tag__in' => array($first_tag),
			'post__not_in' => array($post->ID),
			'posts_per_page'=>3,
			'caller_get_posts'=>1
		);
		$my_query = new wp_query($args);
		
		if( $my_query->have_posts() ):
			$output .= '<p>Bài viết liên quan:</p><ul class="row related-post">';
			while ($my_query->have_posts()):$my_query->the_post();
			$output .= 
				'<li class="col large-4">
								<a href="'.get_the_permalink().'" title="'.get_the_title().'">
									<div class="feature">
										<div class="image" style="background-image:url('. get_the_post_thumbnail_url() .');"></div>
									</div>                            
								</a>
								<div class="related-title"><a href="'.get_the_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></div>
							</li>';
			endwhile;
			$output .= '</ul>';
		endif; wp_reset_query();
		$output .= '</div>';
		return $output;
	}
	else return;
}
add_shortcode('bvlq', 'bvlq');

function bvlq_danh_muc() {
    $output = '';
    if (is_single()) {
      global $post;
      $categories = get_the_category($post->ID);
      if ($categories) {
        $category_ids = array();
        foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
        $args=array(
          'category__in' => $category_ids,
          'post__not_in' => array($post->ID),
          'posts_per_page'=>3,
          'caller_get_posts'=>1
        );
        
        $my_query = new wp_query( $args );
        if( $my_query->have_posts() ):
            $output .= '<div class="relatedcat">';
                $output .= '<h4><strong>Bài viết liên quan:</trong></h4><div class="row related-post">';
                    while ($my_query->have_posts()):$my_query->the_post();
                    $output .= 
                        '<div class="col large-4">
                            <a href="'.get_the_permalink().'" title="'.get_the_title().'">
                                <div class="feature">
                                    <div class="image" style="background-image:url('. get_the_post_thumbnail_url() .');"></div>
                                </div>                            
                            </a>
                            <div class="related-title"><a href="'.get_the_permalink().'" title="'.get_the_title().'">'.get_the_title().'</a></div>
                        </div>';
                    endwhile;
                $output .= '</div>';
            $output .= '</div>';
        endif;   //End if.
      wp_reset_query();
    }
    return $output;
  }
}
add_shortcode('bvlq_danh_muc','bvlq_danh_muc');

function get_flatsome_blog_breadcrumbs() {
    $delimiter = '<span class="divider">&#47;</span>';
    $home = 'Trang chủ';
    $before = '';
    $after = '';
    if ( !is_home() && !is_front_page() || is_paged() ) {
        echo '<div class="page-title shop-page-title product-page-title"><div class="page-title-inner flex-row medium-flex-wrap container"><div class="flex-col flex-grow medium-text-center"><div class="is-medium">';
        echo '<nav class="breadcrumbs">';
        global $post;
        $homeLink = get_bloginfo('url');
        echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
        if ( is_category() ) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
            echo $before . single_cat_title('', false) . $after;
        } elseif ( is_day() ) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;
        } elseif ( is_month() ) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;
        } elseif ( is_year() ) {
            echo $before . get_the_time('Y') . $after;
        } elseif ( is_single() && !is_attachment() ) {
            if ( get_post_type() != 'post' ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                echo $before . get_the_title() . $after;
            } else {
                $cat = get_the_category(); $cat = $cat[0];
                echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                $postID = get_post($post->ID);
                echo '<a href="' . get_permalink($postID) . '">' . $postID->post_title . '</a>';
            }
        }
        elseif ( is_product()) {
            if ( get_post_type() != 'post' ) {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                echo $before . get_the_title() . $after;
            } else {
                $cat = get_the_category(); $cat = $cat[0];
                echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                echo $before . get_the_title() . $after;
            }
        }
        elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        } elseif ( is_attachment() ) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID); $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;
        } elseif ( is_page() && !$post->post_parent ) {
            echo $before . get_the_title() . $after;
        } elseif ( is_page() && $post->post_parent ) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
            echo $before . get_the_title() . $after;
        } elseif ( is_search() ) {
            echo $before . 'Search results for "' . get_search_query() . '"' . $after;
        } elseif ( is_tag() ) {
            echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
        } elseif ( is_author() ) {
            global $author;
            echo $before . 'Articles posted by ' . $userdata->display_name . $after;
        } elseif ( is_404() ) {
            echo $before . 'Error 404' . $after;
        }
        if ( get_query_var('paged') ) {
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
            echo __('Page') . ' ' . get_query_var('paged');
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
        }
        echo '</nav>';
        echo '</div></div></div></div>';
    }
}
add_action( 'flatsome_before_blog' , 'get_flatsome_blog_breadcrumbs', 20 );

/*-----------------------------------------------------------------------------------*/
/* Breadcrumb display */
/*-----------------------------------------------------------------------------------*/

add_action('woo_main_before','woo_display_breadcrumbs',10);
if (!function_exists( 'woo_display_breadcrumbs')) {
	function woo_display_breadcrumbs() {
		global $woo_options;
		if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' && ! (is_home()) ) {
		echo '<section id="breadcrumbs">';
			woo_breadcrumbs();
		echo '</section><!--/#breadcrumbs -->';
		}
	} // End woo_display_breadcrumbs()
} // End IF Statement

add_action( 'woo_main_before', 'remove_woo_display_breadcrumbs', 0 );

function remove_woo_display_breadcrumbs() {

    remove_action('woo_main_before','woo_display_breadcrumbs',10);
	
}


add_action('woo_content_before','woo_display_breadcrumbs',10);


// Add Font
function add_font3(){ ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />
<?php }
add_action('wp_head','add_font3');

/**
 * Alters the font-page main query
 */
add_action( 'pre_get_posts', 'wpse_217284_alter_front_page_query' );
function wpse_217284_alter_front_page_query( $query ) {

    // don't run on the backend
    if ( is_admin() )
      return;

    // Only run on the front page main query
    if ( $query->is_main_query() && is_front_page() ) {

        $query->set( 'orderby', 'rand' );
        $query->set( 'posts_per_page', 10 );
    }

    return;
}



if(!function_exists('custom_breadcrumb')): 
		function custom_breadcrumb() {
			global $post;
 
			if(!is_front_page()){
				echo '<li> <a href="'.home_url().'">Trang chủ</a></li>';
			}
 
			if(is_singular(array('post'))){
				$cats=get_the_category();
				$catName=$cats[0]->name;
				$catID=get_cat_ID( $catName );
				$catLink=get_category_link($catID);
				echo '<li> <a href="'.$catLink.'">'.$catName.'</a></li>';
				$curTitle=get_the_title(($post->ID),0,14);
				$curTitle=(strlen($curTitle) >14)?substr($curTitle,0,14).'...':$curTitle;
				echo '<li class="active">'.$curTitle.'</li>';
			}
			// Your custom post type - Portfolio here
			if(is_singular(array('portfolio'))){
				$pType= get_terms('portfolio_type'); 
				if(is_array($pType)&& count($pType)>0){
					$portLink="#";
					$portName=$pType[0]->name;
					echo '<li> <a href="'.$portLink.'">'.$portName.'</a></li>';
				}
			}
			if(is_page()){ 
				$postParents=get_post_ancestors($post->ID);
				if(is_array($postParents) && count(($postParents)>0)){
					foreach ($postParents as $key => $value) {
						$pageLink=get_permalink($value);
						$pageTitle=get_the_title($value);
						echo '<li> <a href="'.$pageLink.'">'.$pageTitle.'</a></li>';
					}
				}
				$curTitle=get_the_title(($post->ID),0,14);
				$curTitle=(strlen($curTitle) >14)?substr($curTitle,0,14).'...':$curTitle;
				echo '<li class="active">'.$curTitle.'</li>';
 
 
			}
			if(is_category()){ 
				$category = get_category(get_query_var('cat'));
				$cat_id = $category->cat_ID;
				$theCat=get_cat_name($cat_id);
				echo '<li class="active">'.$theCat.'</a></li>';
			}
			if(is_tag()){
				echo '<li class="active">'.get_query_var('tag').'</a></li>';
			}
			if(is_author()){ 		
				echo '<li class="active">'. get_the_author_meta('user_nicename').'</a></li>';
			}
						if(is_search()){ 		
				echo '<li class="active">'. get_search_query().'</a></li>';
			}
 
		}
		endif;

// Bài viết cùng chuyên mục
add_shortcode('devwp_posts_related','flatsome_related_posts');
function flatsome_related_posts(){
ob_start();
$categories = get_the_category(get_the_ID());
if ($categories){
echo '<div class="relatedcat">';
$category_ids = array();
foreach($categories as $individual_category) array_push($category_ids, $individual_category->term_id);
$my_query = new wp_query(array(
'category__in' => $category_ids,
'post__not_in' => array(get_the_ID()),
'posts_per_page' => 6
));
$ids = wp_list_pluck( $my_query->posts, 'ID' );
$ids = implode(',', $ids);
if( $my_query->have_posts() ){
echo '<h3>Bài viết liên quan</h3>';
echo do_shortcode('[blog_posts style="normal" type="row" columns="3" columns__md="2" posts="6" image_height="56.25%" text_align="left" ids="' . $ids . '"]');
}
echo '</div>';
}
return ob_get_clean();
}

// Remove the default WooCommerce 3 JSON/LD structured data format
function remove_output_structured_data() {
   remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // Frontend pages
   remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // Emails
}
add_action( 'init', 'remove_output_structured_data' );

// trường author
add_filter( 'author_link', 'wpdongthanhcong_author_link', 10, 1 ); 	 	 
function wptangtoc_author_link( $link ) {	 	 
    $link = 'https://dongthanhcong.vn/nguyen-thuy-trang';
return $link;	 	  	 	 
}

// Start function show product attention below price
function attention_below_price(){
	global $post;
    $product_benefits = get_post_meta( $post->ID, '_bhww_benefits_wysiwyg', true );
    if ( ! empty( $product_benefits ) ) {
        // Updated to apply the_content filter to WYSIWYG content
        echo apply_filters( 'the_content', $product_benefits );
    }
}

add_shortcode('product_attention', 'attention_below_price');

add_action( 'add_meta_boxes', 'create_custom_meta_box' );
if ( ! function_exists( 'create_custom_meta_box' ) )
{
    function create_custom_meta_box()
    {
        add_meta_box(
            'custom_product_meta_box',
            __( 'Chú ý của sản phẩm <em>(Không bắt buộc)</em>', 'cmb' ),
            'add_custom_content_meta_box',
            'product',
            'normal',
            'default'
        );
    }
}
//  Custom metabox content in admin product pages
if ( ! function_exists( 'add_custom_content_meta_box' ) ){
    function add_custom_content_meta_box( $post ){
        $prefix = '_bhww_'; // global $prefix;
        $benefits = get_post_meta($post->ID, $prefix.'benefits_wysiwyg', true) ? get_post_meta($post->ID, $prefix.'benefits_wysiwyg', true) : '';
        $args['textarea_rows'] = 6;
        echo '<p>'.__( 'Nội dung này sẽ hiển thị bên trên giá tiền', 'cmb' ).'</p>';
        wp_editor( $benefits, 'benefits_wysiwyg', $args );
        echo '<input type="hidden" name="custom_product_field_nonce" value="' . wp_create_nonce() . '">';
    }
}

//Save the data of the Meta field
add_action( 'save_post', 'save_custom_content_meta_box', 10, 1 );
if ( ! function_exists( 'save_custom_content_meta_box' ) )
{
    function save_custom_content_meta_box( $post_id ) {
        $prefix = '_bhww_'; // global $prefix;
        // We need to verify this with the proper authorization (security stuff).
        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'custom_product_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'custom_product_field_nonce' ];
        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // Check the user's permissions.
        if ( 'product' == $_POST[ 'post_type' ] ){
            if ( ! current_user_can( 'edit_product', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }
        // Sanitize user input and update the meta field in the database.
        update_post_meta( $post_id, $prefix.'benefits_wysiwyg', wp_kses_post($_POST[ 'benefits_wysiwyg' ]) );
    }
}