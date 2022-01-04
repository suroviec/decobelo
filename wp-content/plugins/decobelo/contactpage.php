<?php

/*** DODANIE KLAS  ***/ 

add_filter( 'postbox_classes_page_startowa_cta', 'custom_postbox'  );
add_filter( 'postbox_classes_page_start_collections_metabox', 'custom_postbox'  );
add_filter( 'postbox_classes_page_contact_data', 'custom_postbox'  );


/** TEKST CTA  **/ 

add_action('admin_head', function() {
    global $post;
    if($post->ID == 126) {
        add_meta_box(
            'contact_data',
            'Dane kontaktowe',
            'contact_data',
            'page',
            'advanced',
            'high'
        );
    }
});

function contact_data($post) {

    $meta = 'contact_data';
    $saved = maybe_unserialize(get_post_meta($post->ID, $meta))[0];   
    
    wp_nonce_field( $meta.'_data', $meta.'_nonce' );

    echo sprintf(
        '<label for="%s">Telefon (bez prefixu +48)</label><input type="text" name="%s[0]" value="%s"/>',
        $meta,
        $meta,
        $saved[0]
    );

    echo sprintf(
        '<label for="%s">Adres email</label><input type="text" name="%s[1]" value="%s"/>',
        $meta,
        $meta,
        $saved[1]
    );

    echo sprintf(
        '<label for="%s">Adres</label><textarea rows="4" name="%s[2]">%s</textarea>',
        $meta,
        $meta,
        $saved[2]
    );
    
    echo sprintf(
        '<label for="%s">Link do konta Instagram</label><input type="text" name="%s[3]" value="%s"/>',
        $meta,
        $meta,
        $saved[3]
    );

    echo sprintf(
        '<label for="%s">Link do konta Facebook</label><input type="text" name="%s[4]" value="%s"/>',
        $meta,
        $meta,
        $saved[4]
    );

}

add_action('save_post', 'save_contact_data');

function save_contact_data($post_id) {

    $meta = 'contact_data';

    if ( ! isset( $_POST[$meta.'_nonce'] ) )
        return $post_id;
    $nonce = $_POST[$meta.'_nonce'];
    if ( !wp_verify_nonce( $nonce, $meta.'_data' ) )
        return $post_id;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;
    
    update_post_meta( $post_id, $meta, $_POST[$meta] );
}






