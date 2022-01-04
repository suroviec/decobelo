<?php
class CheckboxMetabox{

    public $label;
    public $description;
    public $meta;
    public $data;
    public $places;
    public $postid;

    public function __construct() {
    }

    public function setOptions($options) {
        $this->label = $options['label'];
        $this->description = $options['description'];
        $this->meta = $options['meta'];
        $this->places = $options['places'];
        $this->limit = $options['limit'];
        $this->postid = $options['post_id'];
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function createDump() {
        echo '<pre>';
        var_dump($this->data);
        echo '</pre>';
    }

    public function dataDump() {
        add_action('admin_head', function() {
            add_meta_box( $this->meta.'_metabox', 'Data dump', array( $this, 'createDump' ), $this->places, 'advanced', 'high' );
        });  
    }

    public function start_cat_metabox($post) {

        echo '<ul class="checkbox-container">';

        wp_nonce_field( $this->meta.'_data', $this->meta.'_nonce' );
        $saved_data = maybe_unserialize(get_post_meta($post->ID, $this->meta));    

        foreach($this->data as $single_data) {
            echo sprintf(
                '<li><label><input %s id="option_%s" type="checkbox" name="%s[]" value="%s" />%s</label></li>',
                in_array($single_data['id'], $saved_data[0]) ? 'checked' : '',
                $single_data['id'],
                $this->meta,
                $single_data['id'],
                $single_data['label'],
            );
        };
        echo '</ul>';
    }

    public function save_start_cat_metabox($post_id) {
        if ( ! isset( $_POST[$this->meta.'_nonce'] ) )
            return $post_id;
        $nonce = $_POST[$this->meta.'_nonce'];
        if ( !wp_verify_nonce( $nonce, $this->meta.'_data' ) )
            return $post_id;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;
        update_post_meta( $post_id, $this->meta, $_POST[$this->meta] );
    }

    public function limit() {
        global $post;
        if($this->limit) {
            if($this->limit == $post->ID) {
                add_meta_box( $this->meta.'_metabox', $this->label, array( $this, 'start_cat_metabox' ), $this->places, 'advanced', 'high' );
            }
        }
    }

    public function generate() {
            add_action('admin_head', array($this, 'limit'));  
            add_action('save_post', array($this, 'save_start_cat_metabox' ));
    }

}

class TextareaMetabox{

    public $label;
    public $description;
    public $meta;
    public $data;
    public $places;
    public $postid;

    public function __construct() {
    }

    public function setOptions($options) {
        $this->label = $options['label'];
        $this->description = $options['description'];
        $this->meta = $options['meta'];
        $this->places = $options['places'];
        $this->limit = $options['limit'];
        $this->postid = $options['post_id'];
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function createDump() {
        echo '<pre>';
        var_dump($this->data);
        echo '</pre>';
    }

    public function dataDump() {
        add_action('admin_head', function() {
            add_meta_box( $this->meta.'_metabox', 'Data dump', array( $this, 'createDump' ), $this->places, 'advanced', 'high' );
        });  
    }

    public function start_cat_metabox($post) {

        wp_nonce_field( $this->meta.'_data', $this->meta.'_nonce' );
        $saved_data = maybe_unserialize(get_post_meta($post->ID, $this->meta));    

            echo sprintf(
                '<textarea name="%s" id="%s">%s</textarea>',
                $this->meta,
                $this->meta,
                $saved_data[0]
            );
    }

    public function save_start_cat_metabox($post_id) {
        if ( ! isset( $_POST[$this->meta.'_nonce'] ) )
            return $post_id;
        $nonce = $_POST[$this->meta.'_nonce'];
        if ( !wp_verify_nonce( $nonce, $this->meta.'_data' ) )
            return $post_id;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;
        update_post_meta( $post_id, $this->meta, $_POST[$this->meta] );
    }

    public function limit() {
        global $post;
        if($this->limit) {
            if($this->limit == $post->ID) {
                add_meta_box( $meta.'_metabox', $this->label, array( $this, 'start_cat_metabox' ), $this->places, 'advanced', 'high' );
            }
        }
    }

    public function generate() {
            add_action('admin_head', array($this, 'limit'));  
            add_action('save_post', array($this, 'save_start_cat_metabox' ));
    }

}