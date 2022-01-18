<?php
/**
 * Template Name: Strona kontaktu
 *
 * @package Decobelo
 */

get_header();
?>

	<main id="primary" class="site-main">

		<h1><?php the_title(); ?></h1>
		<div class="contact-data">
			<ul>
				<?php 
					$data = get_post_meta($post->ID, 'contact_data')[0];
				?>
				<li>
					<span>Zadzwoń do nas</span>
					<?php 
						echo sprintf(
							'<a href="tel:+48%s">%s</a>',
							$data[0],
							$data[0]
						)
					?>
				</li>	
				<li>
				<span class="title">Wyślij e-mail:</span>
					<?php 
						echo sprintf(
							'<a href="mailto:%s">%s</a>',
							$data[1],
							$data[1]
						)
					?>
				</li>
				<li>
					<p><b>Decobelo</b></br>
					<b>jest marką Inventini sp. z o.o.</b></br></p>
					<p>NIP: 642-318-51-68</br>
					REGON: 243452509</br>
					KRS: 0000493482</br>
					Kapitał zakładowy 5 000,00 PLN</br></p>
				</li>	
				<li>
					<span>Odwiedź nas:</span>
					<?php 
						echo wpautop($data[2]); 
						echo '<a target="_blank" title="'. __('Nawigacja Google Maps','decobelo').'" href="https://www.google.com/maps/dir//Inventini+Sp.+z+o.+o.,+Towarowa+14,+44-338+Jastrz%C4%99bie-Zdr%C3%B3j/@49.939918,18.5598193,17z/data=!4m9!4m8!1m0!1m5!1m1!1s0x47114fb6087e3339:0x4acc073e5bfc1584!2m2!1d18.562008!2d49.939918!3e0">'.__('Wskazówki dojazdu','decobelo').'</a>';
					?>					
				</li>
			</ul>
		</div>
		<div class="map">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29053.765283306642!2d18.549066587523615!3d49.93606884807119!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47114fb6087e3339%3A0x4acc073e5bfc1584!2sInventini%20Sp.%20z%20o.%20o.!5e0!3m2!1spl!2spl!4v1642450710613!5m2!1spl!2spl" width="100%" height="500px" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
		</div>
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();