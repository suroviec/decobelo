<?php
add_action('init', 'kolekcje_cat');

function kolekcje_cat() {
    $labels = array(
        'name' => _x( 'Kolekcje', 'taxonomy general name' ),
        'singular_name' => _x( 'Kolekcja', 'taxonomy singular name' ),
        'search_items' =>  __( 'Przeszukaj kolekcje' ),
        'all_items' => __( 'Wszystkie kolekcje' ),
        'parent_item' => __( 'Nadrzędna kolekcja' ),
        'parent_item_colon' => __( 'Nadrzędna kolekcja:' ),
        'edit_item' => __( 'Edytuj kolekcję' ), 
        'update_item' => __( 'Zaktualizuj kolekcję' ),
        'add_new_item' => __( 'Dodaj kolekcję' ),
        'new_item_name' => __( 'Nazwa kolekcji' ),
        'menu_name' => __( 'Kolekcje' ),
      );    
     
      register_taxonomy('kolekcje','product', array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'kolekcja' ),
      ));
}

add_filter('manage_edit-kolekcje_columns', function ( $columns ) 
{
    if( isset( $columns['description'] ) )
        unset( $columns['description'] );   

    return $columns;
} );

add_filter('manage_edit-product_cat_columns', function ( $columns ) 
{
    if( isset( $columns['description'] ) )
        unset( $columns['description'] );   

    return $columns;
} );
