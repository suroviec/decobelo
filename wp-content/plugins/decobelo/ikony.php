<?php

class Ikony {
 
    /**
     * Constructor.
     */
    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }
 
    /**
     * Registers a new settings page under Settings.
     */
    function admin_menu() {
        add_submenu_page(
            'edit.php?post_type=product',
            'Ikony konserwacji',
            'Ikony konserwacji',
            'manage_options',
            'ikony',
            array(
                $this,
                'ikony'
            )
        );
    }

    function save_options() {

        if($_POST['ikony']) {

            $options = $_POST['ikony'];

            foreach($options as $slug => $data) {

                if($data['remove'] == 'tak') {

                    unset($options[$slug]);

                } elseif(($slug == 'n') && ($data['title'] !== '')) {

                    $output[sanitize_title($data['title'])] = array(
                        'title'  => $data['title'],
                        'img'=> $data['img']
                    );

                } elseif(($slug !== '') && ($slug !== 'n')) {

                    $output[$slug] = array(
                        'title'  => $data['title'],
                        'img'=> $data['img'],
                        'remove' => $data['remove']
                    );

                }
                
            };

            update_option('ikony', $output);
        };
    
    }
 
    /**
     * Settings page display callback.
     */
    function ikony() {

        wp_nonce_field( 'd_nonce', 'd_nonce' );  

        $this->save_options();

        $options = get_option('ikony');

        if($options) {
            $l = count($options);
        }
     

        echo '<div class="wrap ikony">';
            echo '<h1 class="wp-heading-inline">Ikony konserwacji</h1>';
            echo '<form method="post" action="">';
                echo '<div class="g6">';

                if($options) {

                    foreach($options as $slug => $data) {
                
                        echo sprintf(
                            '<div class="postbox">
                                <div class="postbox-header"><h2>%s</h2></div>
                                <div class="inside">
                                    <p>
                                        <b>Tytuł</b>
                                        <input type="text" class="fw" name="%s" value="%s" />
                                    </p>',
                                    $data['title'],
                                    'ikony[' . $slug . '][title]',
                                    $data['title']
                        );

                        echo '<b>Zdjęcie</b>';

                        echo '<div>';
                    
                        $img_url = wp_get_attachment_image_src($data['img'])[0];

                        echo '<a href="#" class="misha-upl"><img src="' . $img_url . '" /></a>';
                        echo '<a href="#" class="misha-rmv button action">Usuń zdjęcie</a>';
                        echo '<input type="hidden" name="ikony[' . $slug . '][img]" value="' . $data['img'] . '">';

                        echo '</div>';  
    
    
                        echo '<div><p><input type="checkbox" name="ikony['.$slug.'][remove]" value="tak"><label>Idź mi precz z tym! </label></p></div>';
    
                        echo '</div></div>';
    
                    }

                }

                    echo sprintf(
                        '<div class="postbox">
                            <div class="postbox-header"><h2>%s</h2></div>
                            <div class="inside">
                                <p>
                                    <b>Tytuł</b>
                                    <input type="text" class="fw" name="%s" value="%s" />
                                </p>',
                                'Nowy element',
                                'ikony[n][title]',
                                ''
                    );
                
                    echo '<b>Zdjęcie</b>';

                    echo '<div style="margin-top:5px;">';
                        echo '<a href="#" class="misha-upl button action">Dodaj zdjęcie</a>';
                        echo '<a href="#" class="misha-rmv " style="display:none">Usuń zdjęcie</a>';
                        echo '<input type="hidden" name="ikony[n][img]" value="">';
                    echo '</div>';
                    

                    

                    echo '</div></div>';

                echo '</div>';
            
                submit_button();
            
                echo '</form>';
        echo '</div>';

    }

}
 
new Ikony;

add_action( 'admin_menu', 'metabox_ikony' );

function metabox_ikony() {

    add_meta_box(
        'metabox_ikony', // metabox ID
        'Ikony konserwacji', // title
        'opcje_ikon', // callback function
        'product', // post type or post types in array
        'normal', // position (normal, side, advanced)
        'default' // priority (default, low, high, core)
    );

}

function opcje_ikon($post) {

    $id = $post->ID;

    $options = get_post_meta($id, 'ikony')[0];
    
    if($options) {

        foreach($options as $slug => $data) {

            echo '<div>
                <h4> ' . $data['title'] . '</h4>
                <input type="hidden" name="ikony[' . $slug . '][title]" value="' . $data['title'] . '" placeholder="Zmień tytuł"/>';
                wybierz_ikony($slug, $data['values']);
            echo '</div>';
        }
    }

    echo '<div>
            <h4>Nowy element</h4>
            <input type="text" name="ikony[n][title]" value="" placeholder="Podaj tytuł"/>';
            wybierz_ikony('n');

    echo '</div>';

    echo '<span class="fw">Aby wyłączyć wybraną opcję wystarczy odznaczyć wszystkie dopisane do niej ikony.</br>Jeśli przewidziana jest tylko jedna opcja nie trzeba uzupełniać jej tytułu.</span>';


}

    function wybierz_ikony( $option=null, $values = null ) {

        $ikony = get_option('ikony');

        $t = array_column($ikony, 'title');

        array_multisort($t, SORT_ASC, $ikony);

        echo '<ul>';

        foreach ($ikony as $key => $value) {

            if($values) {
                if(in_array($key, $values)) {
                    $checked = "checked";
                } else {
                    $checked = "";
                }
            } else {
                $checked = "";
            }
             
            echo sprintf(
                '<li>
                    <input type="checkbox" name="%s" value="%s" id="i-%s" %s />
                    <label for="i-%s"><b>%s</b></label></li>',
                'ikony[' . $option . '][values][]',
                $key,
                $key . '-' . $option,
                $checked,
                $key . '-' . $option,
                $value['title']
            );

        };
        
        echo '</ul>';

    }


    add_action( 'save_post', 'ikony_save', 10, 2 );

    function ikony_save( $post_id, $post ) {
       
        if( isset( $_POST[ 'ikony' ] ) ) {

         

            $input = $_POST[ 'ikony' ];

            foreach ($input as $slug => $data) {

                if(($slug == 'n') && $data['values']) {

                    if($data['title'] == "") {
                        $data['title'] = "Podstawowy";
                    }

                    $output[sanitize_title($data['title'])] = array(
                        'title' => $data['title'], 
                        'values' => $data['values']
                    );
                } elseif (($slug !== 'n') && $data['values']) {
                    $output[$slug] = array(
                        'title' => $data['title'], 
                        'values' => $data['values']
                    );
                } elseif (empty($data['values'])) {
                    unset($input[$slug]);
                };
            };

            update_post_meta( $post_id, 'ikony', $output );
        } 
    
        return $post_id;
    
    }
