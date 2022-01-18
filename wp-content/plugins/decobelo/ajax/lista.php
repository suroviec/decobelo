<?php

// ANCHOR add list to user meta

function add_list_to_meta( $user_login, $user ) { 

    $list_from_session = WC()->session->get('list');

    $list_from_user = get_user_meta($user->ID, 'list');

    if(empty($list_from_session) == false) {
        if(empty($list_from_user) == false) {
            array_push($list_from_user,$list_from_session);
            update_user_meta( $user->ID, 'list', $list_from_session);
        } else {
            add_user_meta($user->ID, 'list', $list_from_session);
        }
    }

    // dopisanie do sesji listy usera

    if((empty($list_from_user) == false) && (empty($list_from_session) == true)) {
        WC()->session->set('list', $list_from_user[0]);
    }

}
add_action('wp_login', 'add_list_to_meta', 10, 2);

// ANCHOR DODANIE PRZYCISKU DODAJ DO LISTY

add_action( 'woocommerce_before_shop_loop_item_title', 'add_to_list_btn', 1 ); 

function add_to_list_btn() {
    global $product;
    $list = WC()->session->get('list');
    $id = $product->get_id();
	echo sprintf(
        '<div class="list %s"><button class="list-btn list-%s %s" data-product_id="%s" data-nonce="%s" data-user="%s" href="%s" title="%s">lista</button></div>',
        is_array($list) && in_array($id, $list) ? 'inlist' : '',
        $id,
        is_array($list) && in_array($id, $list) ? 'inlist' : '',
        $id,
        wp_create_nonce('list'),
        get_current_user_id(),
        admin_url('admin-ajax.php?action=list&post_id='.$product->get_id().'&nonce='.wp_create_nonce('list')),
        is_array($list) && in_array($id, $list) ? __('Produkt zapisany na liście ulubionych', 'decobelo') : __('Dodaj do listy ulubionych', 'decobelo'),
    );
}

// ANCHOR lista ulubionych w panelu użytkownika

/**

add_filter ( 'woocommerce_account_menu_items', 'list_link', 40 );
function list_link( $menu_links ){
	
	$menu_links = array_slice( $menu_links, 0, 2, true ) 
	+ array( 'lista' => 'Zapisane produkty' )
	+ array_slice( $menu_links, 2, NULL, true );
	
	return $menu_links;

}

add_action( 'init', 'lista_endpoint' );
function lista_endpoint() {

	add_rewrite_endpoint( 'lista', EP_PAGES );

}

add_action( 'woocommerce_account_lista_endpoint', 'lista_w_panelu' );
function lista_w_panelu() {

    echo '<pre>';
	var_dump(get_user_meta(get_current_user_id(), 'list')[0]);
    //var_dump(WC()->session->get('list'));
    echo '</pre>';

}

**/

// ANCHOR link w headerze

function header_saved_list() {
        ob_start();
        ?>

        <?php $session_list = WC()->session->get('list');


        if (!$session_list || (count($session_list) == 0)) {
            echo '<span>'. __('Brak zapisanych produktów', 'decobelo') .'</span>';
        } else {
            foreach ($session_list as $id) : ?>

            <li class="list-prod-<?php echo $id?>">
            <?php echo sprintf(
                '<div class="img"><a href="%s" title="%s">
                    <img src="%s" loading="lazy" widh="150" height="150" />
                    </a></div>
                    <div class="name"><a href="%s" title="%s"><span class="product-name">%s</span></a>
                    </a>
                    <div class="list-add-to-cart">%s</div></div>
                    <a href="%s" class="remove-from-list" data-product_id="%s" data-nonce="%s" data-user="%s">x</a>
                ',
                get_permalink($id),
                __('Zobacz produkt ', 'decobelo') . get_the_title($id),
                get_the_post_thumbnail_url($id, 'woocommerce_thumbnail'),
                get_permalink($id),
                __('Zobacz produkt ', 'decobelo') . get_the_title($id),
                get_the_title($id),
                do_shortcode('[add_to_cart id="'.$id.'"]', false),
                admin_url('admin-ajax.php?action=remove_from_list&post_id='.$id.'&nonce='.wp_create_nonce('remove_from_list')),
                $id,
                wp_create_nonce('list'),
                get_current_user_id()
            );
            ?>
            </li>
            <?php
            endforeach;
        };    
        $output = ob_get_clean();
        return $output;
}


add_action( 'woocommerce_init', 'enable_wc_session_cookie' );
function enable_wc_session_cookie(){ 
    if( is_admin() )
       return;

    if ( isset(WC()->session) && ! WC()->session->has_session() ) 
       WC()->session->set_customer_session_cookie( true ); 
}

add_action("wp_ajax_list", 'session_list');
add_action("wp_ajax_nopriv_list", 'session_list');

function session_list() {

    if ( !wp_verify_nonce( $_REQUEST['nonce'], 'list')) {
        exit;
    }   
        $product_id = $_REQUEST['product_id'];
        $current_list = WC()->session->get('list');
        $current_list[] = $product_id;
        WC()->session->set('list', array_unique($current_list));

        // zaktualizowanie listy dopisanej do uzytkownika

        $user = intval($_REQUEST['user']);

        if($user > 0) {
            update_user_meta($user, 'list', WC()->session->get('list'));
        }

        $update = WC()->session->get('list');
            $result['type'] = 'success';
            $result['ids'] = $update;
            $result['length'] = count($update);
            $result['lista'] = header_saved_list();
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }    
    die;
}

add_action("wp_ajax_remove_from_list", 'remove_from_list');
add_action("wp_ajax_nopriv_remove_from_list", 'remove_from_list');

function remove_from_list() {
   
        $product_id = sanitize_text_field($_REQUEST['product_id']);
        $current_list = WC()->session->get('list');
        $to_remove = array();
        $to_remove[] = $product_id;
        $current_list = array_diff($current_list, $to_remove);  
        WC()->session->set('list', $current_list);

        // zaktualizwanie listy dopisanej do uzytkownika

        $user = intval(sanitize_text_field($_REQUEST['user']));

        if($user > 0) {
            update_user_meta($user, 'list', WC()->session->get('list'));
        }

        $update = WC()->session->get('list');
            $result['type'] = 'success';
            $result['ids'] = $update;
            $result['length'] = count($update);
            $result['removed'] = $product_id;
            $result['user'] = $user;
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }    
    die;
}

