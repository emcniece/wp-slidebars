<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="sb-site">
 *
 * @package WordPress
 * @subpackage wp-slidebars
 * @since WP Slidebars 1.0
 */
global $wp_slidebars;

?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>

	<nav class="<?php wp_slidebars_navbar_classes($echo=true); ?>" role="navigation">

		<?php if( in_array( $wp_slidebars['opt-layout'], array(2,4) ) ): ?>
			<!-- Left Control -->
			<div class="sb-toggle-left navbar-left">
				<div class="navicon-line"></div>
				<div class="navicon-line"></div>
				<div class="navicon-line"></div>
			</div><!-- /.sb-control-left -->
		<?php endif; ?>

		<?php if( in_array( $wp_slidebars['opt-layout'], array(3,4) ) ): ?>
			<!-- Right Control -->
			<div class="sb-toggle-right navbar-right">
				<div class="navicon-line"></div>
				<div class="navicon-line"></div>
				<div class="navicon-line"></div>
			</div><!-- /.sb-control-right -->
		<?php endif; ?>
		
		<div class="container">
			<!-- Logo -->
			<div id="logo" class="navbar-left">
				<a href="http://plugins.adchsm.me/slidebars/"><img src="<?php echo $wp_slidebars['wp-sb-logo']['url']; ?>" alt="<?php bloginfo('site_title'); ?>"></a>
			</div><!-- /#logo -->
			
			<!-- Menu -->
			<?php wp_nav_menu( array( 'theme_location'=>'navbar-menu', 'container'=>false, 'menu_class'=>'nav navbar-nav navbar-right' ) ); ?>

		</div>
	</nav>