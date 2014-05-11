<?php

// Adding Redux Framework
if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/admin/ReduxCore/framework.php' ) ) {
    require_once( dirname( __FILE__ ) . '/admin/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/redux-config.php' ) ) {
    require_once( dirname( __FILE__ ) . '/redux-config.php' );
}
