<?php

class Dodatkowe {
 
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
            'Dodatkowe informacje',
            'Dodatkowe informacje',
            'manage_options',
            'dodatkowe-informacje',
            array(
                $this,
                'dodatkowe'
            )
        );
    }

    function save_options() {

        if($_POST['dodatkowe']) {

            $options = $_POST['dodatkowe'];

            foreach($options as $slug => $data) {

                if($data['remove'] == 'tak') {

                    unset($options[$slug]);

                } elseif(($slug == 'n') && ($data['title'] !== '')) {

                    $output[sanitize_title($data['title'])] = array(
                        'title'  => $data['title'],
                        'content'=> wp_kses_stripslashes(wpautop($data['content']))
                    );

                } elseif(($slug !== '') && ($slug !== 'n')) {

                    $output[$slug] = array(
                        'title'  => $data['title'],
                        'content'=> wp_kses_stripslashes(wpautop($data['content'])),
                        'remove' => $data['remove']
                    );

                }
                
            };

            update_option('dodatkowe', $output);
        };
    
    }
 
    /**
     * Settings page display callback.
     */
    function dodatkowe() {

        wp_nonce_field( 'd_nonce', 'd_nonce' );  

        $this->save_options();

        $options = get_option('dodatkowe');

        if($options) {
            $l = count($options);
        }
     

        echo '<div class="wrap">';
            echo '<h1 class="wp-heading-inline">Dodatkowe informacje</h1>';
            echo '<form method="post" action="">';
                echo '<div class="g2">';


                if($options) {

                    foreach($options as $slug => $data) {
                
                        echo sprintf(
                            '<div class="postbox">
                                <div class="postbox-header"><h2>%s</h2></div>
                                <div class="inside">
                                    <p>
                                        <input type="text" class="fw" name="%s" value="%s" />
                                    </p>',
                                    $data['title'],
                                    'dodatkowe[' . $slug . '][title]',
                                    $data['title']
                        );
                    
                        wp_editor(
                            wp_kses_stripslashes($data['content']),
                            'dcontent_'.$slug,
                            array(
                            'media_buttons' => true,
                            'textarea_rows' => 16,
                            'tabindex' => 4,
                            'teeny' => false,
                            'textarea_name' => 'dodatkowe['.$slug.'][content]',
                            'tinymce' => array(
                                'toolbar1'=> 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,undo,redo,',
                                'toolbar2' => false,
                                'statusbar' => false
                            ),
                            'quicktags' => false
                            )
                        ); 
    
    
    
                        echo '<div><p><input type="checkbox" name="dodatkowe['.$slug.'][remove]" value="tak"><label>Idź mi precz z tym! </label></p></div>';
    
                        echo '</div></div>';
    
                    }

                }

                    echo sprintf(
                        '<div class="postbox">
                            <div class="postbox-header"><h2>%s</h2></div>
                            <div class="inside">
                                <p>
                                    <input type="text" class="fw" name="%s" value="%s" />
                                </p>',
                                'Nowy element',
                                'dodatkowe[n][title]',
                                ''
                    );
                
                    wp_editor(
                        '',
                        'dcontent_new',
                        array(
                        'media_buttons' => true,
                        'textarea_rows' => 16,
                        'tabindex' => 4,
                        'teeny' => true,
                        'textarea_name' => 'dodatkowe[n][content]',
                        'tinymce' => array(
                            'toolbar1'=> 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,undo,redo,',
                            'toolbar2' => false,
                            'statusbar' => false
                        ),
                        'quicktags' => false
                        )
                    ); 

                    echo '</div></div>';

                echo '</div>';
            
                submit_button();
            
                echo '</form>';
        echo '</div>';

        echo '<pre>';

                //  var_dump(get_option('dodatkowe'));

        echo '<pre>';
    }
}
 
new Dodatkowe;


add_action( 'admin_menu', 'metabox_dodatkowe' );

function metabox_dodatkowe() {

    add_meta_box(
        'metabox_dodatkowe', // metabox ID
        'Dodatkowe informacje', // title
        'dodatkowe_informacje', // callback function
        'product', // post type or post types in array
        'side', // position (normal, side, advanced)
        'default' // priority (default, low, high, core)
    );

}

function dodatkowe_informacje($post) {

    $id = $post->ID;

    $saved = get_post_meta($id, 'dodatkowe')[0];

    $options = get_option('dodatkowe');

    echo '<ul>';

    foreach($options as $slug => $data) {

        if(in_array($slug, $saved)) {
            $checked = "checked";
        } else {
            $checked = "";
        }

        echo sprintf(
            '<li><label for="%s"><input type="checkbox" name="%s" value="%s" %s />%s</label></li>',
            'd-'.$slug,
            'dodatkowe[]',
            $slug,
            $checked,
            $data['title']
        );
    };

    echo '</ul>';

}

add_action( 'save_post', 'dodatkowe_save', 10, 2 );

    function dodatkowe_save( $post_id, $post ) {
       
        if( isset( $_POST[ 'dodatkowe' ] ) ) {

            update_post_meta( $post_id, 'dodatkowe', $_POST[ 'dodatkowe' ] );
        } 
    
        return $post_id;
    
    }




