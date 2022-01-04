<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Decobelo
 */

?>	
	<?php if(is_checkout()) : ?>

		<script>
			
			window.easyPackAsyncInit = function () {
			easyPack.init({
				defaultLocation: [51.778711878622225, 19.451706487124284]
			});
			var map = easyPack.mapWidget('map-widget', function(point){
				console.log(point);
				document.querySelector('#inpost span').innerHTML = "Wybrany paczkomat:<br><b>" + point.name + ", " + point.address_details.city + ", " + point.address_details.street + " " + point.address_details.building_number + "</b>";
				document.querySelector('#inpost input').value = point.name;
				document.querySelector('#inpost a').textContent = "Zmień";
				document.querySelector('#inpost a').style.marginLeft = "8px";
				
				var actives = document.querySelectorAll('.active');
				actives.forEach(function(el	){
					el.classList.remove('active');
				});

				document.querySelector('#map-widget').style.display = "none";

				});
			};
			 
	</script>
	
			
		</script>
		
		<div id="map"><div id="map-widget"></div></div>;

	<?php endif; ?>
	
	<footer id="colophon" class="site-footer">
		
		<div class="frt">
		<div class="footer-info">
			<div class="footer-logo">
				<img src="<?php echo get_template_directory_uri(); ?>/style/i/logo_w.svg" alt="Logo Decobelo">
			</div>
			<div class="footer-contact-data">
				<ul class="frt2">
					<?php 
						$data = get_post_meta(126, 'contact_data')[0];
					?>
					<li>
						<?php 
							echo wpautop($data[2]); 
							echo '<a target="_blank" title="'. __('Nawigacja Google Maps','decobelo').'" href="https://www.google.com/maps/dir//Inventini+Sp.+z+o.+o.,+Towarowa+14,+44-338+Jastrz%C4%99bie-Zdr%C3%B3j/@49.939918,18.5598193,17z/data=!4m9!4m8!1m0!1m5!1m1!1s0x47114fb6087e3339:0x4acc073e5bfc1584!2m2!1d18.562008!2d49.939918!3e0">'.__('Wskazówki dojazdu','decobelo').'</a>';
						?>					
					</li>
					<li>
						<?php 
							echo sprintf(
								'<a href="tel:+48%s">%s</a>',
								$data[0],
								$data[0]
							)
						?>
						<?php 
						echo sprintf(
							'<a href="mailto:%s">%s</a>',
							$data[1],
							$data[1]
						)
						?>
						<ul class="social">
							<li>
								<?php 
									echo sprintf(
										'<a target="_blank" title="%s" href="%s"><img src="%s" alt="%s"></a>',
										__('Konto Instagram', 'decobelo'),
										$data[3],
										get_template_directory_uri().'/style/i/ig_w.svg',
										__('Konto Instagram', 'decobelo')
									)
								?>
							</li>
							<li>
								<?php 
									echo sprintf(
										'<a target="_blank" title="%s" href="%s"><img src="%s" alt="%s"></a>',
										__('Konto Facebook', 'decobelo'),
										$data[4],
										get_template_directory_uri().'/style/i/fb_w.svg',
										__('Konto Facebook', 'decobelo')
									)
								?>
							</li>
						</ul>
					</li>	
				</ul>
			</div>
		</div>
			<nav id="footer-navigation" class="footer-navigation">
				<button class="menu-toggle mobile" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'decobelo' ); ?></button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-stopki',
						'menu_id'        => 'menu-stopki',
					)
				);
				?>
			</nav>
		</div>
		<div class="site-info">
			<span>Ⓒ <?php echo date("Y"); ?> Decobelo</span>
			<span class="sep"> | </span>
				<?php
				
				printf( esc_html__( 'Wykonanie: %1$s.', 'decobelo' ), '<a href="http://surowiec.io">Marcin Surowiec</a>' );
				?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
