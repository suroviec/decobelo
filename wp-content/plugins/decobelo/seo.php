<?php

/** TEKST CTA  **/ 

add_filter( 'postbox_classes_page_seo', 'custom_postbox'  );
add_filter( 'postbox_classes_post_seo', 'custom_postbox'  );

add_action('admin_head', function() {
    add_meta_box(
        'seo',
        'Pozycjonowanie',
        'seo_metabox',
        array('page', 'post', 'product'),
        'side',
        'default'
    );
});

function seo_metabox($post) {

    $meta = 'seo';
    $saved = maybe_unserialize(get_post_meta($post->ID, $meta))[0];   
    
    wp_nonce_field( $meta.'_data', $meta.'_nonce' );

    echo sprintf(
        '<label for="%s">Meta title</label><input type="text" name="%s[]" value="%s"/>',
        $meta,
        $meta,
        $saved[0]
    );

    echo sprintf(
        '<label for="%s">Meta description</label><textarea rows="8" name="%s[]">%s</textarea>',
        $meta,
        $meta,
        $saved[1]
    );

}

add_action('save_post', 'save_seo');

function save_seo($post_id) {

    $meta = 'seo';

    if ( ! isset( $_POST[$meta.'_nonce'] ) )
        return $post_id;

    $nonce = $_POST[$meta.'_nonce'];
    
    if ( !wp_verify_nonce( $nonce, $meta.'_data' ) )
        return $post_id;
    
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;
    
    update_post_meta( $post_id, $meta, $_POST[$meta] );

}