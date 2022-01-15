<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Decobelo
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">

	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<?php 

	/**
	//var_dump(WC()->session->get('list'));
	$user_id = get_current_user_id();
	var_dump(get_user_meta($user_id, 'list')[0]);
	echo '<br>';
	var_dump(WC()->session->get('list'));
	 */
?>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	
	<header id="masthead" class="site-header <?php is_page() ? 'sticky' : '' ?>">
		<div class="site-branding">
			<?php the_custom_logo(); ?>
		</div><!-- .site-branding -->

		<div id="main-menu">
			<nav id="site-navigation" class="main-navigation">
				<ul id="primary-menu" class="menu">
					<?php custom_menu('product_cat', 'Produkty', true); ?>
					<?php custom_menu('kolekcje', 'Kolekcje', true, false, 'kolekcje'); ?>
					<li><a href="<?php echo get_permalink(60); ?>"><?php _e('Promocje', 'decobelo'); ?></a></li>
					<li><a href="<?php echo get_permalink(15); ?>"><?php _e('Szyte na miarę', 'decobelo'); ?></a></li>
				</ul>
			</nav><!-- #site-navigation -->
			<a href="" id="cart-icon" class="cover-btn" title="<?php _e('Wyszukaj produkt', 'decobelo'); ?>">
				Koszyk
				<span class="cart-count-mobile">0</span>
			</a>	
			<a href="" id="list-icon" class="cover-btn" title="<?php _e('Wyszukaj produkt', 'decobelo'); ?>">
				Ulubione
				<?php 
					echo sprintf(
						'<span class="list-count-mobile">%s</span>',
						WC()->session->get('list') ? count(WC()->session->get('list')) : '0'
					);
				?>
			</a>	
			<a href="" id="search-icon" class="cover-btn" title="<?php _e('Wyszukaj produkt', 'decobelo'); ?>">Wyszukaj</a>	
			<a href="" id="menu-switcher" class="cover-btn" title="<?php _e('Wyszukaj produkt', 'decobelo'); ?>">Menu</a>
			<div id="search">
				<?php echo do_shortcode('[fibosearch]'); ?>
				<div class="close"></div>
			</div>	
		</div>


		<div id="shop-menu">
			<ul>
				<li id="list-btn">
					<a href="" class="cover-btn" title="<?php _e('Lista Twoich ulubionych produktów', 'decobelo'); ?>">
						<?php _e('Lista ulubionych', 'decobelo'); ?>
						<?php 
							echo sprintf(
								'<span class="list-count">%s</span>',
								WC()->session->get('list') !== "" ? count(WC()->session->get('list')) : '0'
							);
						?>
						
					</a>
					<div class="submenu">
						<div class="close"></div>
						<span class="submenu-header"><?php _e('Zapisane produkty', 'decobelo'); ?></span>
						<ul class="saved-list">
							<?php echo header_saved_list(); ?>
						</ul>
					</div>
				</li>
				<li id="user-btn" class="<?php echo get_current_user_id() > 0 ? 'logged' : '' ?>">
					
					<a href="" class="cover-btn" title="<?php echo get_current_user_id() > 0 ? 'Jesteś zalogowana / zalogowany' : 'Kliknij, aby zalogować' ?>"><?php _e('Moje konto', 'decobelo'); ?></a>

					<ul class="submenu">
						<div class="close"></div>
						<div>
							<?php header_login(); ?>
						</div>
					</ul>
				</li>
				<li id="cart-btn" >
					<a href="" class="cover-btn" title="<?php _e('Zobacz zawartość koszyka', 'decobelo')?>">
						<?php _e('Koszyk', 'decobelo'); ?>
						<span class="cart-count"></span>
					</a>
					<div class="cart-container submenu">
						<div class="close"></div>
						<span class="submenu-header"><?php _e('Koszyk', 'decobelo'); ?></span>
						<div class="minicart-cont">
							<div id="cart-msg"></div>
							<ul class="cart-list">
								<div class="mini-cart">
									<?php woocommerce_mini_cart(); ?>
								</div>
							</ul>
								<div class="mini-cart-bottom"></div>
						</div>
						
					</div>
				</li>
			</ul>
		</div>
		<div id="dbmsg"></div>
		<div id="cover"><div class="close"></div></div>
	</header><!-- #masthead -->

