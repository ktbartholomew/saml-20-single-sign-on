<?php

namespace Ktbartholomew\SamlPlugin\SP\Endpoints;

use Ktbartholomew\SamlPlugin\Config\Saml;
use Ktbartholomew\SamlPlugin\Config\Wordpress;
use Ktbartholomew\SamlPlugin\SP\SP;
use \OneLogin_Saml2_Auth as Auth;

class Assertion {
  function __construct() {
    $wordpress_config = Wordpress::getConfig();

    if (!$wordpress_config['enabled']) {
      return;
    }

    session_start();
    $settings = Saml::getSettingsArray();
    $auth = new Auth($settings);

    $auth->processResponse(null);

    if (!empty($auth->getErrors())) {
        echo('<p>'.implode(', ', $auth->getErrors()).'</p>');
        exit();
    }

    if (!$auth->isAuthenticated()) {
      http_response_code(403);
      echo '<p>not authenticated</p>';
      exit();
    }

    $sp = new SP();
    $sp->loginWithAssersion($auth->getNameId(), $auth->getAttributes());
  }
}
