<?php

add_action('admin_notices', 'start_slider');

function start_slider() {
    global $post;
    if($post->ID !== 17) return;

    function slider_data() {

        $data = maybe_unserialize(get_post_meta(17, 'slides')[0]);    

        echo '<div class="g2">';
        
        for($i = 0; $i < 3; $i++) {

            echo sprintf(
                '<div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle">%s</h2>
                    </div>
                    <div class="inside">
                        <div>
                            <p class="">
                                <label class="post-attributes-label" for="parent_id">Wyświetlanie</label>
                            </p>
                            <div class="radio">
                                <label for="%s" class="">%s</label>
                                <input id="%s" type="radio" name="%s" value="tak" %s>
                            </div>
                            <div class="radio">
                                <label for="%s" class="">%s</label>
                                <input id="%s" type="radio" name="%s" value="nie" %s>
                            </div>
                        </div>
                        <div class="">
                            <p class="">
                                <label class="post-attributes-label" for="parent_id">Nagłówek</label>
                            </p>    
                            <input type="text" class="" name="%s" value="%s" />
                        </div>
                        <div class="">
                            <p class="">
                                <label class="post-attributes-label" for="parent_id">Tekst</label>
                            </p>    
                            <textarea name="%s" cols="30" rows="7" >%s</textarea>
                        </div>
                        <div class="">
                            <p class="">
                                <label class="post-attributes-label" for="parent_id">Adres linku (bez https://decobelo.pl)</label>
                            </p>    
                            <input type="text" class="" name="%s" value="%s" />
                        </div>
                        <div class="">
                            <p class="">
                                <label class="post-attributes-label" for="parent_id">Tytuł przycisku</label>
                            </p>    
                            <input type="text" class="" name="%s" value="%s" />
                        </div>',
                'Slajd ' . ($i + 1),
                'slides-' . $i. '-check',
                'tak',
                'slides-' . $i. '-check',
                'slides[' . $i . '][check]',
                $data[$i]['check'] == 'tak' ? 'checked' : '',
                'slides-' . $i. '-check',
                'nie',
                'slides-' . $i. '-check',
                'slides[' . $i . '][check]',
                $data[$i]['check'] == 'nie' ? 'checked' : '',
                'slides[' . $i . '][title]',
                $data[$i]['title'],
                'slides[' . $i . '][content]',
                $data[$i]['content'],
                'slides[' . $i . '][url]',
                $data[$i]['url'],
                'slides[' . $i . '][link_name]',
                $data[$i]['link_name']
            );


            // FOTO MOBILE

            echo '<p class=""><label class="post-attributes-label" for="parent_id">Zdjęcie dla wersji mobilnej</label></p>';

            $slide_img = $data[$i]['img']['mobile']; 

            if($slide_img) {
                
                $img_url = wp_get_attachment_image_src($slide_img, 'medium')[0];

                echo '<a href="#" class="misha-upl"><img style="display:block; margin-bottom: 15px" src="' . $img_url . '" /></a>';
                echo '<a href="#" class="misha-rmv button action">Usuń zdjęcie</a>';
                echo '<input type="hidden" name="slides[' . $i . '][img][mobile]" value="' . $slide_img . '">';

            } else {

                echo '<a href="#" class="misha-upl button action">Dodaj zdjęcie</a>';
                echo '<a href="#" class="misha-rmv " style="display:none">Usuń zdjęcie</a>';
                echo '<input type="hidden" name="slides[' . $i . '][img][mobile]" value="">';

            }

            // FOTO DESKTOP 

            echo '<p class=""><label class="post-attributes-label" for="parent_id">Zdjęcie dla wersji desktop</label></p>';    

            $slide_img = $data[$i]['img']['desktop']; 

            if($slide_img) {
                
                $img_url = wp_get_attachment_image_src($slide_img, 'medium')[0];

                echo '<a href="#" class="misha-upl" style="display:block"><img style="display:block; margin-bottom: 15px" src="' . $img_url . '" /></a>';
                echo '<a href="#" class="misha-rmv button action">Usuń zdjęcie</a>';
                echo '<input type="hidden" name="slides[' . $i . '][img][desktop]" value="' . $slide_img . '">';

            } else {

                echo '<a href="#" class="misha-upl button action">Dodaj zdjęcie</a>';
                echo '<a href="#" class="misha-rmv button action" style="display:none">Usuń zdjęcie</a>';
                echo '<input type="hidden" name="slides[' . $i . '][img][desktop]" value="">';

            }



                
            echo '</div></div>';

        };

        echo '</div>'; // g2

    };

    add_action('edit_form_after_editor', 'slider_data');

}

function save_slides($post_id) {

    update_post_meta($post_id, 'slides', $_POST['slides']);

}

add_action('save_post', 'save_slides'); 