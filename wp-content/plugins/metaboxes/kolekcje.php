<?php
add_action("kolekcje_edit_form_fields", 'edit_tax_fields', 10, 2);
add_action("product_cat_edit_form_fields", 'edit_tax_fields', 10, 2);

function edit_tax_fields($term, $taxonomy){
    
    $meta = 'tax'; 
    wp_nonce_field( $meta.'_data', $meta.'_nonce' ); 
    
    ?>
    <tr valign="top">
        <th scope="row">Krótki opis (na stronie głównej)</th>
        <td>
            <input type="text" name="short_desc" id="" value="<?php echo get_term_meta($term->term_id, 'short_desc')[0]; ?>" />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Opis</th>
        <td>
            <?php 
                wp_editor(
                    get_term_meta($term->term_id, 'descr')[0],
                    'descr',
                    array(
                      'media_buttons' => false,
                      'textarea_rows' => 7,
                      'tabindex' => 4,
                      'teeny' => true,
                      'textarea_name' => 'desc',
                      'tinymce' => array(
                        'toolbar1'=> 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,undo,redo,',
                        'toolbar2' => false,
                        'statusbar' => false
                      ),
                      'quicktags' => false
                    )
                ); 
            ?>
            <p class="description">Jeśli w opisie nie ma nagłówka h2 nagłówek opisu na frontendzie zostanie pobrany z krótkiego opisu.</p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Meta title (SEO)</th>
        <td>
            <input type="text" name="metatitle" id="" value="<?php echo get_term_meta($term->term_id, 'metatitle')[0]; ?>" />
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Meta description (SEO)</th>
        <td>
            <textarea name="metadesc" id="" cols="70" rows="2"><?php echo get_term_meta($term->term_id, 'metadesc')[0]; ?></textarea>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">Zdjęcie "okładkowe"</th>
        <td>
        <?php 
                $cat_img = get_term_meta($term->term_id, 'img')[0]; 
                if($cat_img) : ?>
                    <?php 
                        $img_url = wp_get_attachment_image_src($cat_img)[0];
                    ?>
                    <a href="#" class="misha-upl"><img src="<?php echo $img_url; ?>" /></a>
                    <a href="#" class="misha-rmv button action">Usuń zdjęcie</a>
                    <input type="hidden" name="img" value="<?php echo $cat_img; ;?>">
                <?php else : ?>
                    <a href="#" class="misha-upl button action">Dodaj zdjęcie</a>
                    <a href="#" class="misha-rmv " style="display:none">Usuń zdjęcie</a>
                    <input type="hidden" name="img" value="">
                <?php endif; ?>
        </td>
    </tr>
    
    <?php
} 

add_action( 'edited_kolekcje', 'tax_save_term_fields' );
add_action( 'edited_product_cat', 'tax_save_term_fields' );

function tax_save_term_fields( $term_id ) {

    $meta = 'tax';

    if ( ! isset( $_POST[$meta.'_nonce'] ) )
        return $term_id;
    $nonce = $_POST[$meta.'_nonce'];
    if ( !wp_verify_nonce( $nonce, $meta.'_data' ) )
        return $term_id;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $term_id;

    update_term_meta(
        $term_id,
        'short_desc',
        $_POST[ 'short_desc' ]
    );    

	update_term_meta(
		$term_id,
		'descr',
		$_POST[ 'desc' ]
	);

    update_term_meta(
		$term_id,
		'metatitle',
		$_POST[ 'metatitle' ]
	);

    update_term_meta(
		$term_id,
		'metadesc',
		$_POST[ 'metadesc' ]
	);

    update_term_meta(
		$term_id,
		'img',
		$_POST[ 'img' ]
	);
}

function hide_description_row() {
    echo "<style> .term-description-wrap { display:none; } </style>";

}

add_action( "kolekcje_edit_form", 'hide_description_row');
add_action( "kolekcje_add_form", 'hide_description_row');
add_action( "product_cat_edit_form", 'hide_description_row');
add_action( "product_cat_add_form", 'hide_description_row');


