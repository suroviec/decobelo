<?php

class Emaile {
 
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
            'woocommerce',
            'Treści e-maili',
            'Treści e-maili',
            'manage_options',
            'tresci-emaili',
            array(
                $this,
                'emaile'
            )
        );
    }

    function save_options() {

        if ( ! isset( $_POST['emaile_nonce'] ) )
            return;

        $nonce = $_POST['emaile_nonce'];
        
        if ( !wp_verify_nonce( $nonce, 'emaile_data' ) )
            return;
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

        if($_POST['emaile']) {
            update_option('emaile', $_POST['emaile']);
        };
    
    }
 
    /**
     * Settings page display callback.
     */
    function emaile() {

        $this->save_options();

        $emails = array(
            array(
                'name' => 'potwierdzenie',
                'title' => 'Treści maili z potwierdzeniem zamówienia'
            ),
            array(
                'name' => 'realizacja',
                'title' => 'Treści maili po przekazaniu zamówienia do realizacji'
            ),
            array(
                'name' => 'zakonczenie',
                'title' => 'Treści maili po zakończeniu zamówienia'
            ),
        );

        echo '<div class="wrap">';

            echo '<form method="post" action="">';

            wp_nonce_field( 'emaile_data', 'emaile_nonce' );

            foreach($emails as $email) {
                $this->postboxy($email);
            };

            submit_button();

        echo '</form>';

        echo '</div>';

       
    }


    function postboxy($email) {

        $options = get_option('emaile');

        $vars = array(
            array(
                'name' => 'osobisty-pobranie',
                'title' => 'Odbiór osobisty + płatność przy odbiorze'
            ),
            array(
                'name' => 'osobisty-przelew',
                'title' => 'Odbiór osobisty + przelew'
            ),
            array(
                'name' => 'osobisty-payu',
                'title' => 'Odbiór osobisty + PayU'
            ),
            array(
                'name' => 'wysylka-payu',
                'title' => 'Wysyłka + PayU'
            ),
            array(
                'name' => 'wysylka-przelew',
                'title' => 'Wysyłka + przelew'
            ),
            array(
                'name' => 'wysylka-pobranie',
                'title' => 'Wysyłka + pobranie'
            )
        );
        

        echo '<h1 class="wp-heading-inline">' . $email['title'] . '</h1>';
            echo '<div class="g3">';

                foreach($vars as $var) {
            
                    echo sprintf(
                        '<div class="postbox">
                            <div class="postbox-header"><h2>%s</h2></div>
                            <div class="inside">
                                <p>
                                    <textarea class="fw" rows="10" name="%s" value="%s" />%s</textarea>
                                </p>',
                                $var['title'],
                                'emaile['. $email['name'] .'][' . $var['name'] . ']',
                                $options ? $options[$email['name']][$var['name']] . 'asdasd' : 'aaa',
                                $options ? $options[$email['name']][$var['name']] : 'aaa'    
                    );

                    echo '</div></div>';
                }

            echo '</div>';
    
    }

}
 
new Emaile;