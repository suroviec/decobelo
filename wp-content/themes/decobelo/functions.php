<?php
/**
 * Decobelo functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Decobelo
 */


remove_action('woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal', 10 );
add_action('mini_cart_bottom_total', 'woocommerce_widget_shopping_cart_total',1 );

// ANCHOR cart upsells

remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
add_action('woocommerce_after_cart','woocommerce_cross_sell_display');


remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
add_action( 'custom_gallery', 'woocommerce_show_product_thumbnails', 25 );

// ANCHOR disable embed

function my_deregister_scripts(){
	//wp_deregister_script( 'wp-embed' );
  }
  add_action( 'wp_footer', 'my_deregister_scripts' );


// ANCHOR disable feed

remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );

function disable_feed() {
	wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
   }
   
   add_action('do_feed', 'disable_feed', 1);
   add_action('do_feed_rdf', 'disable_feed', 1);
   add_action('do_feed_rss', 'disable_feed', 1);
   add_action('do_feed_rss2', 'disable_feed', 1);
   add_action('do_feed_atom', 'disable_feed', 1);
   add_action('do_feed_rss2_comments', 'disable_feed', 1);
   add_action('do_feed_atom_comments', 'disable_feed', 1);


// ANCHOR przesuniecie onsale 
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
//add_action('custom_gallery', 'woocommerce_show_product_sale_flash', 5 );

add_action('new_badge', 'new_badge');

function new_badge() {
	global $product;
	if((get_the_terms($product->get_id(), 'pa_nowosc')[0]->slug) == 'tak') {
		echo sprintf(
			'<div class="new">%s</div>',
			__('Nowy', 'decobelo')
		);
	};
}


// ANCHOR funkcja do emaili

function email($etap, $shipping, $payment) {

	$options = get_option('emaile');
	$e = $options[$etap];

	$ts = array(
		'local_pickup' => 'osobisty',
		'flat_rate' => 'wysylka',
		'free_shipping' => 'wysylka'
	);

	$tp = array(
		'cod' => 'pobranie',
		'payustandard' => 'payu',
		'bacs' => 'przelew'
	);
	
	$translate = $ts[$shipping].'-'.$tp[$payment];

	echo wpautop($options[$etap][$translate]);	

}

// ANCHOR tresci maili

add_action( 'woocommerce_email_order_details', 'add_email_order_meta', 10, 3 );

function add_email_order_meta( $order, $sent_to_admin, $plain_text ){

	// wysylka
	$shipping = '';				
	$shipping_methods = $order->get_shipping_methods();
	foreach($shipping_methods as $id => $shipping_data) {
		$shipping = $shipping_data->get_method_id();
	}

	// platnosc
	$payment = $order->get_payment_method();

	// status
	$status = $order->get_status();

	if(($status == 'on-hold') || ($status == 'pending')){
		$etap = 'potwierdzenie';
	} elseif ($status == 'processing') {
		$etap = 'realizacja';
	} elseif($status == 'completed') {
		$etap = 'zakonczenie';
	}

	if ( $plain_text === false ) {
	
		echo '<p>' . email($etap, $shipping, $payment) . '</p>';
	
	} else {
	
		echo email($etap, $shipping, $payment);
	
	}
	
}



// ANCHOR promo price

add_action( 'woocommerce_after_shop_loop_item_title', 'reg_price', 4 );

function reg_price() {

	global $product;

	if ( $product->is_type( 'variable' ) && ($product->is_on_sale())) {
		echo '<span class="custom_variation_price variation-sale"><del><bdi>';
		echo $product->get_variation_regular_price();
		echo ' - ';	
		echo $product->get_variation_regular_price('max');
		echo 'zł';	
		echo '</bdi></del></span>';
	}

}

add_filter('woocommerce_variable_sale_price_html', 'shop_variable_product_price', 10, 2);
//add_filter('woocommerce_variable_price_html','shop_variable_product_price', 10, 2 );
function shop_variable_product_price( $price, $product ){
    $variation_min_reg_price = $product->get_variation_regular_price('min', true);
    $variation_min_sale_price = $product->get_variation_sale_price('min', true);
    if ( $product->is_on_sale() && !empty($variation_min_sale_price)){
        if ( !empty($variation_min_sale_price) )
            $price = '<del class="strike">' .  wc_price($variation_min_reg_price) . '</del>
        <ins class="highlight">' .  wc_price($variation_min_sale_price) . '</ins>';
    } else {
        if(!empty($variation_min_reg_price))
            $price = '<ins class="highlight">'.wc_price( $variation_min_reg_price ).'</ins>';
        else
            $price = '<ins class="highlight">'.wc_price( $product->regular_price ).'</ins>';
    }
    return $price;
}




// ANCHOR vars a

function action_vars( $vars ) {

    // produkty nie sa przekazywane z list of vars
    $vars[] = 'a';

	return $vars;
}

add_filter( 'query_vars', 'action_vars' );


// ANCHOR usuniecie ratingu na widoku listy

remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

 // ANCHOR title i meta desc

add_action('pre_get_document_title',function() {

	global $wp_query;
	
	if(is_archive()) {

		$id = get_queried_object()->term_id;
		$title = get_queried_object()->name;
		$metatitle = get_term_meta($id, 'metatitle')[0];
		$metadesc = get_term_meta($id, 'metadesc')[0];

	} else {
		
		$id = get_queried_object()->ID;
		$title = get_queried_object()->post_title;
		$metatitle = get_post_meta($id, 'seo')[0][0];
		$metadesc = get_post_meta($id, 'seo')[0][1];
	}

	if($metatitle) {
		return $metatitle;
	}

	return;

});

add_action('wp_head','head_data', 1);

function head_data() {

	if(is_archive()) {

		$id = get_queried_object()->term_id;
		$title = get_queried_object()->name;
		$metatitle = get_term_meta($id, 'metatitle')[0];
		$metadesc = get_term_meta($id, 'metadesc')[0];

	} else {
		
		$id = get_queried_object()->ID;
		$title = get_queried_object()->post_title;
		$metatitle = get_post_meta($id, 'seo')[0][0];
		$metadesc = get_post_meta($id, 'seo')[0][1];
	}
	
	echo sprintf(
		'
	<meta name="description" content="%s">
	',
		$metadesc ? $metadesc : ''
	);
}



// ANCHOR opis kategorii i kolekcji

add_action('woocommerce_after_main_content', 'cat_desc', 5);

function cat_desc() {

	if(is_archive()) {
		$desc = get_term_meta(get_queried_object()->term_id)['descr'][0];
		$short_desc = get_term_meta(get_queried_object()->term_id)['short_desc'][0];

		if (strpos($desc, 'h2') == false) {
			echo '<h2>' . $short_desc . '</h2>';
		} 


		echo '<div class="cat_desc">';
			echo $desc;
		echo '</div>';
	}
	
}


// ANCHOR zmiany w koncie uzytkownika

add_filter ( 'woocommerce_account_menu_items', 'remove_account_links' );
function remove_account_links( $menu_links ){
	unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	unset( $menu_links['downloads'] ); // Disable Downloads
	return $menu_links;	
}



add_filter ( 'woocommerce_account_menu_items', 'rename_kokpit' );

function rename_kokpit( $menu_links ){
	
	$menu_links['dashboard'] = 'Dane konta';

	return $menu_links;
}




 
// ANCHOR header login


function header_login() {
	global $woocommerce;
	if(is_user_logged_in() == true) {
		echo '<span class="submenu-header">' . __('Twoje konto', 'decobelo') . '</span>';
		echo sprintf(
			'<ul>
				<li><a href=%s">%s</a></li>
				<li><a href=%s">%s</a></li>
				<li><a href=%s">%s</a></li>
				<li><a href=%s">%s</a></li>
				<li><a href=%s">%s</a></li>
			</ul>',
			wc_get_endpoint_url('edit-account', '', get_permalink(get_option('woocommerce_myaccount_page_id'))),
			__('Edytuj dane', 'decobelo'),
			wc_get_endpoint_url('lista', '', get_permalink(get_option('woocommerce_myaccount_page_id'))),
			__('Zapisane produkty', 'decobelo'),
			wc_get_endpoint_url('orders', '', get_permalink(get_option('woocommerce_myaccount_page_id'))),
			__('Zamówienia', 'decobelo'),
			wc_get_endpoint_url('edit-address', '', get_permalink(get_option('woocommerce_myaccount_page_id'))),
			__('Adresy', 'decobelo'),
			wc_logout_url(),
			__('Wyloguj', 'decobelo'),
		);
	} else {
		echo '<span class="submenu-header">' . __('Logowanie', 'decobelo') . '</span>';
		echo '<div class="login-form">';
		wp_login_form(
			array(
				'label_username' 	=> _e('Adres email', 'decobelo'),
				'label_log_in'		=> 'Zaloguj', 
				'remember'			=> false
			)
		);	
		echo '<p style="margin-bottom: 2rem;">';
		echo '<a href="' . wp_lostpassword_url() . '" class="">Przypomnij hasło</a>';
		echo '</p>';
		echo '<p>' . __('Nie masz konta?', 'decobelo') . '</p>';
		echo '<a href="' . add_query_arg( 'a', 'rejestracja', get_permalink( get_option('woocommerce_myaccount_page_id') ) ) . '" class="smallbtn1">Załóż konto</a>';
		echo '</div>';
	};
}

add_action('wp_logout','auto_redirect_after_logout');

function auto_redirect_after_logout(){

  wp_redirect( home_url() );
  exit();
}


/** nowy rozmiar obrazka dla menu kategorii **/

add_image_size( 'menu-img', '354', '200', array('center', 'center') );
add_image_size( 'desktop', '1280', '845', array('center', 'center') );
add_image_size( 'vertical', '690', '455', array('center', 'center') );
add_image_size( 'mobile', '746', '746', array('center', 'center') );
add_image_size( 'img_on_product_list', '420', '420', true,  );


// ANCHOR custom menu 

function custom_menu($tax='product_cat', $name='Produkty', $img = false, $main=false, $link = 'sklep') {

	$r_terms = get_terms(
		array(
			'taxonomy' 		=> $tax,
   			'hide_empty' 	=> false,
			'order'			=> 'ASC',
			'childless'		=> false
		)
	);
	
	$r_menu = array();

	foreach($r_terms as $r_term) {

		if($r_term->term_id == 15) {
			continue;
		}

		if($r_term->parent == 0) {
			$terms[$r_term->term_id] = array(
				'name' 	=> $r_term->name,
				'url'	=> get_term_link($r_term->term_id, $tax),
				'img_id'	=> get_term_meta($r_term->term_id, 'img')[0],
				'sub'	=> array()
			);
		}
	};

	foreach($r_terms as $r_term) {
		if($r_term->parent > 0) {
			$terms[$r_term->parent]['sub'][] = array(
				'name' 	=> $r_term->name,
				'url'	=> get_term_link($r_term->term_id, $tax),
				'parent' => $r_term->parent
			);
		}
	}


	if($main == false) {
		echo '<li id="menu-' . $tax . '"><a href="' . $link . '">' . $name . '</a><ul class="sub-menu">';
		echo '<span class="menu-header">' . $name . '</span>';
		echo '<span class="close"></span>';
	} else {
		echo '<ul class="main-cats menu-'. $tax .'">';
	}
	


	foreach($terms as $term) {

		if(($img == true) && ($term['img_id']))  {
			$image = '<img srcset="' . wp_get_attachment_image_srcset($term['img_id']) . '" src="' . wp_get_attachment_image_src($term['img_id'], 'menu-img')[0] . '" loading="lazy" width="354" height="200">';
		} else {
			$image = '';
		} 

		echo sprintf(
			'<li><a href="%s" title="%s">%s%s</a>',		
			$term['url'],
			$term['name'] . __(' - zobacz produkty', 'decobelo'),
			$image,
			$term['name']
		);

			if($term['sub']) {
		
				echo '<ul>';
				
					foreach($term['sub'] as $s_term) {

						echo sprintf(
							'<li><a href="%s" title="%s">%s</a></li>',		
							$s_term['url'],
							$s_term['name'] . __(' - zobacz produkty z kategorii', 'decobelo'),
							$s_term['name']
						);

					};

				echo '</ul>';
			};
		echo '</li>';
	};

	if($main == false) {
		echo '</ul></li>';
	} else {
		echo '</ul>';
	}
	

};

function start_products_block($tax='product_cat', $name='Produkty', $img = false) {

	$r_terms = get_terms(
		array(
			'taxonomy' 		=> $tax,
   			'hide_empty' 	=> false,
			'order'			=> 'ASC',
			'childless'		=> false
		)
	);
	
	$r_menu = array();

	foreach($r_terms as $r_term) {

		if($r_term->term_id == 15) {
			continue;
		}

		if($r_term->parent == 0) {
			$terms[$r_term->term_id] = array(
				'name' 	=> $r_term->name,
				'url'	=> get_term_link($r_term->term_id, $tax),
				'img_id'	=> get_term_meta($r_term->term_id, 'img')[0],
				'sub'	=> array()
			);
		}
	};

	foreach($r_terms as $r_term) {
		if($r_term->parent > 0) {
			$terms[$r_term->parent]['sub'][] = array(
				'name' 	=> $r_term->name,
				'url'	=> get_term_link($r_term->term_id, $tax),
				'parent' => $r_term->parent
			);
		}
	}

	echo '<ul class="">';

	foreach($terms as $term) {

		if(($img == true) && ($term['img_id']))  {
			$image = '<img srcset="' . wp_get_attachment_image_srcset($term['img_id']) . '" src="' . wp_get_attachment_image_src($term['img_id'], 'menu-img')[0] . '" loading="lazy" width="354" height="200">';
		} else {
			$image = '';
		} 

		echo sprintf(
			'<li><a href="%s" title="%s">%s%s</a>',		
			$term['url'],
			$term['name'] . __(' - zobacz produkty', 'decobelo'),
			$image,
			$term['name']
		);

			if($term['sub']) {
		
				echo '<ul>';

					foreach($term['sub'] as $s_term) {

						echo sprintf(
							'<li><a href="%s" title="%s">%s</a></li>',		
							$s_term['url'],
							$s_term['name'] . __(' - zobacz produkty z kategorii', 'decobelo'),
							$s_term['name']
						);

					};

				echo '</ul>';
			};
		echo '</li>';
	};

	echo '</ul>';

};

/**
 * Change several of the breadcrumb defaults
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => '<img src="' . get_template_directory_uri() . '/style/i/arr.svg" alt="">',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
        );
}

// ANCHOR usuniecie slecta kraju


function remove_country( $fields ) {

	unset($fields['billing']['billing_country']);
	unset($fields['shipping']['shipping_country']);
	
return $fields;

}
	
add_filter('woocommerce_checkout_fields','remove_country');


// ANCHOR promo i new przy tytule produktu 

add_action('woocommerce_single_product_summary','onsale_badge', 5 );

function onsale_badge() {
	global $product;
	if($product->is_on_sale() == 1) {
		echo sprintf(
			'<div class="onsale">%s</div>',
			__('Promo', 'decobelo')
		);
	}
}

add_action('woocommerce_single_product_summary','new_badge', 5 );

// REDIRECT JESLI BLAD W LOGOWANIU

add_action( 'wp_login_failed', 'my_front_end_login_fail' );

function my_front_end_login_fail() {
      wp_redirect( get_permalink( get_option('woocommerce_myaccount_page_id') ) . '?a=failed' );
      exit;
}

// ZMIANA ZDJECIA W POJEDYNCZYM PRODUKCIE LISCIE

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );

add_action('woocommerce_before_shop_loop_item_title', 'custom_archive_product_img');

function custom_archive_product_img() {
	global $product;
	echo $product->get_image('img_on_product_list');
	echo sprintf(
		'<img src="%s" alt="%s">',
		wp_get_attachment_image_src( $product->get_gallery_attachment_ids()[0], 'img_on_product_list')[0],
		'aaa'
	);
	
}


// PRZESUNIECIE ADD TO CART NA LISCIE PRODUKTOW

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 20);


// ZMIANA POLOZENIA TEKSTU PROMOCJA W PRODUKCIE W LISCIE PRODUKTOW

//remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 ); 
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 ); 


// CUSTOM SCRIPTS W PANELU ADMINA

add_action('wp_head', 'custom_head_scripts');

function custom_head_scripts() {
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;700;800&display=swap" rel="stylesheet"> 
<?php
}

// CUSTOM SCRIPTS WE FRONTENDZIE

function decobelo_custom_scripts() {
	wp_enqueue_style( 'decobelo-style', get_template_directory_uri().'/style/all.css' , array(), _S_VERSION );
	wp_enqueue_script( 'decobelo-theme-script', get_template_directory_uri() . '/js/decobelo-theme.js', array(), _S_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'decobelo_custom_scripts' );


// UNDERSCORE


if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'decobelo_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function decobelo_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Decobelo, use a find and replace
		 * to change 'decobelo' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'decobelo', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-glowne' => esc_html__( 'Menu główne', 'decobelo' ),
				'menu-stopki' => __( 'Menu stopki' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'decobelo_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'decobelo_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function decobelo_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'decobelo_content_width', 640 );
}
add_action( 'after_setup_theme', 'decobelo_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function decobelo_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'decobelo' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'decobelo' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'decobelo_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function decobelo_scripts() {
	//wp_enqueue_style( 'decobelo-style', get_stylesheet_uri(), array(), _S_VERSION );
	//wp_style_add_data( 'decobelo-style', 'rtl', 'replace' );

	//wp_enqueue_script( 'decobelo', get_template_directory_uri() . '/js/db.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'decobelo_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}


/**
 * Disable WooCommerce block styles (front-end).
 */
function themesharbor_disable_woocommerce_block_styles() {
	wp_dequeue_style( 'wc-blocks-style' );
	wp_dequeue_style( 'wp-block-library' );
  }
  add_action( 'wp_enqueue_scripts', 'themesharbor_disable_woocommerce_block_styles' );