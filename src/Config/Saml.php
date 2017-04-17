<?php

namespace Ktbartholomew\SamlPlugin\Config;

use \OneLogin_Saml2_Settings;

class SAML {
  public static function getSettingsArray() {
    $wordpress_config = Wordpress::getConfig();

    return array(
      'strict' => true,
      'debug' => constant('WP_DEBUG'),
      'sp' => array(
        'entityId' => get_home_url(),
        'NameIDFormat' => $wordpress_config['nameidpolicy'],
        'assertionConsumerService' => array(
          'url' => get_home_url(null, '?saml_action=assertion'),
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ),
        'x509cert' => $wordpress_config['certificate']['public_key'],
        'privateKey' => $wordpress_config['certificate']['private_key']
      ),
      'idp' => array(
        'entityId' => $wordpress_config['idp'],
        'singleSignOnService' => array(
          'url' => $wordpress_config['idp_details'][$wordpress_config['idp']]['SingleSignOnService'],
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        ),
        'certFingerprint' => $wordpress_config['idp_details'][$wordpress_config['idp']]['certFingerprint']
      ),
      'security' => array(
        'authnRequestsSigned' => true,
        'logoutRequestSigned' => true,
        'signMetadata' => true,
        'wantAssertionsSigned' => true,
        'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
        'lowercaseUrlencoding' => true
      )
    );
  }

  public static function getConfig() {
    return new OneLogin_Saml2_Settings(self::getSettingsArray());
  }

  public static function outputMetadata() {
    $settings = self::getConfig();

    header('Content-Type: text/xml');
    echo $settings->getSPMetadata();
  }
}
