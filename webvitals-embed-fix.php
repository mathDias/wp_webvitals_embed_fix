<?php
/*
Plugin Name: Web Vitals Embed Fix
Plugin URI: https://github.com/mathDias
Description: Lazyload embed blocks wordpress
Version: 1.0.0
Author: Matheus Dias
Author URI: https://github.com/mathDias
License: GPLv2 or later
Text Domain: webvital-embed-fix
*/
if(! defined('ABSPATH')) {
    die;
}
  
if ( !function_exists( 'add_action' ) ) {
      echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
      exit;
}

require_once __DIR__.'/vendor/autoload.php';

if(class_exists('App\\Init')){
    App\Init::register_services();
}