<?php

// Adding Redux Framework
if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/admin/ReduxCore/framework.php' ) ) {
    require_once( dirname( __FILE__ ) . '/admin/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/redux-config.php' ) ) {
    require_once( dirname( __FILE__ ) . '/redux-config.php' );
}


// Custom theme-side styles
function wp_slidebars_scripts() {
	global $wp_slidebars;
	
	// Check for debug mode - applies to js and css
	$sb_min = $wp_slidebars['opt-slidebars-debug-mode'] ? '' : '.min';
	$sb_ver = $wp_slidebars['opt-slidebars-version'];

	// Stylesheets
	wp_enqueue_style( 'main', get_stylesheet_uri() );
	wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/assets/bootstrap/dist/css/bootstrap.min.css' );
	wp_enqueue_style( 'slidebars', get_template_directory_uri().'/assets/slidebars/distribution/'.$sb_ver.'/slidebars'.$sb_min.'.css' );

	wp_enqueue_style( 'wp_slidebars_theme', get_template_directory_uri().'/css/slidebars-theme.css' );
	wp_enqueue_style( 'wp_slidebars_style', get_template_directory_uri().'/css/slidebars-style.css' );
	wp_enqueue_style( 'wp_slidebars_dynamic', get_template_directory_uri().'/css/dynamic.css' );


	// Check jQuery version
	wp_dequeue_script('jquery');
	wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array(), '1.10.2', true );

	// Remaining scripts
	wp_enqueue_script( 'bootstrap', get_template_directory_uri().'/assets/bootstrap/dist/js/bootstrap.min.js', array('jquery'), '3.1.1', true);
	wp_enqueue_script( 'slidebars', get_template_directory_uri().'/assets/slidebars/distribution/'.$sb_ver.'/slidebars'.$sb_min.'.js', array('jquery'), $sb_ver, true);

}
add_action( 'wp_enqueue_scripts', 'wp_slidebars_scripts' );


// Custom admin-side styles
function wp_slidebars_options_scripts() {
	wp_enqueue_style( 'wp_slidebars_style', get_template_directory_uri().'/css/redux.css' );
}
add_action( 'admin_enqueue_scripts', 'wp_slidebars_options_scripts');


// Needed to prevent dynamic css from appearing in admin pages
function wp_slidebars_remove_redux_dynamic_css() {
	wp_dequeue_style('redux-external-fonts');
}
add_action( 'admin_footer', 'wp_slidebars_remove_redux_dynamic_css');


// Add specific CSS class by filter
add_filter('body_class','wp_slidebars_class_names');
function wp_slidebars_class_names($classes) {
	
	$classes[] = 'wp-slidebars';	
	return $classes;
}

// Add extra navbar classes
function wp_slidebars_navbar_classes($echo=true){
	global $wp_slidebars;

	// Default classes
	$navbar_classes = array('navbar', 'navbar-default', 'navbar-fixed-top', 'sb-slide');

	// Update classes as set in Redux
	if($wp_slidebars['opt-navbar-glass-effect']) $navbar_classes[] = 'glass';

	if($echo) echo implode($navbar_classes, ' ');
	return $navbar_classes;
}

// Register menu locations
function register_wp_slidebars_menus() {
  register_nav_menus(
    array(
      'navbar-menu' => __( 'Navbar Menu' ),
    )
  );
}
add_action( 'init', 'register_wp_slidebars_menus' );


// Registering Sidebar locations
function wp_slidebars_register_sidebars(){

	register_sidebar( array(
	    'name'         => __( 'Left Slidebar' ),
	    'id'           => 'left-slidebar',
	    'description'  => __( 'Widgets in this area will be shown on the left-hand side.' ),
	    'before_widget'=> '<div id="%1$s" class="widget %2$s">',
	    'after_widget' => '</div>',
	    'before_title' => '<h1>',
	    'after_title'  => '</h1>',
	));
	register_sidebar( array(
	    'name'         => __( 'Right Slidebar' ),
	    'id'           => 'right-slidebar',
	    'description'  => __( 'Widgets in this area will be shown on the right-hand side.' ),
	    'before_widget'=> '<div id="%1$s" class="widget %2$s">',
	    'after_widget' => '</div>',
	    'before_title' => '<h1>',
	    'after_title'  => '</h1>',
	));

}
add_action( 'init', 'wp_slidebars_register_sidebars' );