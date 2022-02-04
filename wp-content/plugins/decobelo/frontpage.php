<?php

/*** USUNIECIE EDYTORA  ***/ 

add_action( 'admin_init', 'hide_editor' );
 
function hide_editor() {
    $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
    if( !isset( $post_id ) ) return;
 
    $template_file = get_post_meta($post_id, '_wp_page_template', true);
    
    if($template_file == 'db_contactpage.php'){ // edit the template name
        remove_post_type_support('page', 'editor');
    }

    /**
    if($template_file == 'db_startpage.php'){ // edit the template name
        remove_post_type_support('page', 'editor');
    }
     */
}


// ANCHOR banery z tekstem

add_action('admin_head', function() {

    global $post;
    if($post->ID == 17) {

        add_meta_box(
            'info-first',
            'Info 1',
            'info_first',
            'page',
            'side'
        );

    }
    
});

function info_first() {

    ob_start(); ?>

        <?php 
        
            global $post;
            $info =get_post_meta($post->ID, 'info')[0][0];
        
        ?>

        <?php wp_nonce_field( 'info', 'info_nonce' ); ?>

        <label for="info-f-title">Tytuł</label>
        <input type="text" name="info[0][title]" rows="10" cols="40" id="info-f-title" value="<?php echo $info['title']; ?>"/>
    
        <label for="info-1-tekst">Tekst</label>
        <textarea name="info[0][content]" rows="10" cols="40" id="info-1-tekst"><?php echo $info['content']; ?></textarea>

        <label for="info-1-url">Link (bez https://decobelo.pl/)</label>
        <input type="text" name="info[0][url]" id="info-1-url" value="<?php echo $info['url']; ?>" />

        <label for="info-1-linkname">Tekst linku</label>
        <input type="text" name="info[0][linkname]" id="info-1-linkname" value="<?php echo $info['linkname']; ?>" />


    <?php

    echo ob_get_clean();

}



add_action('admin_head', function() {

    global $post;
    if($post->ID == 17) {

        add_meta_box(
            'info-sec',
            'Info 2',
            'info_sec',
            'page',
            'side'
        );

    }
    
});

function info_sec() {

    ob_start(); ?>

        <?php wp_nonce_field( 'info', 'info_nonce' ); ?>

        <?php 
        
            global $post;
            $info =get_post_meta($post->ID, 'info')[0][1];
        
        ?>

        <label for="info-1-title">Tytuł</label>
        <input type="text" name="info[1][title]" rows="10" cols="40" id="info-1-title" value="<?php echo $info['title']; ?>" />
    
        <label for="info-1-tekst">Tekst</label>
        <textarea name="info[1][content]" rows="10" cols="40" id="info-1-tekst"><?php echo $info['content']; ?></textarea>

        <label for="info-1-url">Link (bez https://decobelo.pl/)</label>
        <input type="text" name="info[1][url]" id="info-1-url" value="<?php echo $info['url']; ?>" />

        <label for="info-1-linkname">Tekst linku</label>
        <input type="text" name="info[1][linkname]" id="info-1-linkname" value="<?php echo $info['linkname']; ?>" />

        <label>Zdjęcie</label>

        <div>

            <?php

                $img = $info['img']; 
            
                if($img) {
                    
                    $img_url = wp_get_attachment_image_src($img, 'medium')[0];

                    echo '<a href="#" class="misha-upl"><img style="display:block; margin-bottom: 15px" src="' . $img_url . '" /></a>';
                    echo '<a href="#" class="misha-rmv button action">Usuń zdjęcie</a>';
                    echo '<input type="hidden" name="info[1][img]" value="' . $img . '">';

                } else {

                    echo '<a href="#" class="misha-upl button action">Dodaj zdjęcie</a>';
                    echo '<a href="#" class="misha-rmv " style="display:none">Usuń zdjęcie</a>';
                    echo '<input type="hidden" name="info[1][img]" value="">';

                }

            ?>

        </div> 

    <?php

    echo ob_get_clean();

}

add_action( 'save_post', 'save_info' );

function save_info($post_id) {

    update_post_meta( $post_id, 'info', $_POST['info'] );

}




/*** DODANIE KLAS  ***/ 

add_filter( 'postbox_classes_page_startowa_cta', 'custom_postbox'  );
add_filter( 'postbox_classes_page_start_collections_metabox', 'custom_postbox'  );

function custom_postbox($classes) {
  array_push($classes,'custom-postbox');
  return $classes;
}


/*** WYBOR KOLEKCJI  ***/ 

add_action('admin_init', 'kolekcje_checkbox', 10,2);

function kolekcje_checkbox() {

    $test = new CheckboxMetabox;

    $options = array(
        'label'         => 'Wyświetlane kolekcje',
        'description'   => 'Zdjęcia wybrać można w ekranie edycji kolekcji',
        'meta'          => 'start_collections',
        'places'        => 'page',
        'limit'         => 17
    );

    $kolekcje = get_terms( array(
        'taxonomy' => 'kolekcje',
        'hide_empty' => false,
    ) );

    $data = array();

    foreach($kolekcje as $kolekcja) {
        $data[] = array(
            'id' => $kolekcja->term_id,
            'label' => $kolekcja->name,
        ); 
    };

    $test->setOptions($options);
    $test->setData($data);
    $test->generate();
}


/** TEKST CTA  **/ 

/**

add_action('admin_head', function() {
    global $post;
    if($post->ID == 17) {
        add_meta_box(
            'startowa_cta',
            'Dane do CTA',
            'startowa_cta',
            'page',
            'advanced',
            'high'
        );
    }
});



function startowa_cta($post) {

    $meta = 'startowa_cta';
    $saved = maybe_unserialize(get_post_meta($post->ID, $meta))[0];   
    
    wp_nonce_field( $meta.'_data', $meta.'_nonce' );

    echo sprintf(
        '<label for="%s">Tytuł</label><input type="text" name="%s[]" value="%s"/>',
        $meta,
        $meta,
        $saved[0]
    );

    echo sprintf(
        '<label for="%s">Tekst</label><textarea rows="8" name="%s[]">%s</textarea>',
        $meta,
        $meta,
        $saved[1]
    );

    echo sprintf(
        '<label for="%s">Tekst linku</label><input type="text" name="%s[]" value="%s" />',
        $meta,
        $meta,
        $saved[2]
    );
}

add_action('save_post', 'save_startowa_cta');

function save_startowa_cta($post_id) {

    $meta = 'startowa_cta';

    if ( ! isset( $_POST[$meta.'_nonce'] ) )
        return $post_id;
    $nonce = $_POST[$meta.'_nonce'];
    if ( !wp_verify_nonce( $nonce, $meta.'_data' ) )
        return $post_id;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;
    
    update_post_meta( $post_id, $meta, $_POST[$meta] );
}

**/





