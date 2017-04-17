<?php
/*
Plugin Name: SAML 2.0 Single Sign-On
Version: 1.0.0
Plugin URI: http://keithbartholomew.com
Description: Authenticate users using a SAML 2.0 identity provider.
Author: Keith Bartholomew
Author URI: https://keithbartholomew.com
*/

namespace Ktbartholomew\SamlPlugin;

use Ktbartholomew\SamlPlugin\Admin\Dashboard;
use Ktbartholomew\SamlPlugin\SP\SP;

require_once(__DIR__ . '/vendor/autoload.php');

// define('SAMLAUTH_ROOT', dirname(__FILE__));
// define('SAMLAUTH_URL', plugins_url() . '/' . basename( dirname(__FILE__)));

if (WP_DEBUG) {
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
}


// Things needed everywhere
// require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_settings.php' );
// require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_client.php' );

new SP();

// WordPress action hooks
// add_action('lost_password', array($SAML_Client,'disable_function'));
// add_action('retrieve_password', array($SAML_Client,'disable_function'));
// add_action('password_reset', array($SAML_Client,'disable_function'));
// add_filter('show_password_fields', array($SAML_Client,'show_password_fields'));
//
//
// Things needed only by the admin portal
if( is_admin() )
{
  new Dashboard();
}
