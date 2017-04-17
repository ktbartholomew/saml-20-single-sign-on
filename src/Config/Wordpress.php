<?php

namespace Ktbartholomew\SamlPlugin\Config;

class Wordpress {
  private static $wp_option_name = 'saml_authentication_options';

  private static $current_version = '1.0.0-alpha';

  public static function getConfig() {
    return get_option(self::$wp_option_name);
  }

  public static function writeConfig($data) {
    return update_option(self::$wp_option_name, $data);
  }
}
