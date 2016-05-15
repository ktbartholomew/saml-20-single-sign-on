<?php
/**
 * Metadata that is parse and used by the simplesaml
 * SimpleSAML_Metadata parser object. This is
 * required for simple saml to obtain the proper
 * sp configurations.
 */

$ini = null;
$wp_opt = get_option('saml_authentication_options');
$blog_id = (string)get_current_blog_id();

/*
 * Check database for idp detail configuration,
 * if not found, use flat file configuration.
 * If idp details are missing, the plugin should halt with parser error.
 */
if (isset($wp_opt['idp_details'])) {
    $ini = $wp_opt['idp_details'];
}
else
{
    $configPath = constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini';
    $ini = parse_ini_file($configPath, true);
}

foreach($ini as $key => $array)
{
  $metadata[$key] = array(
          'name' => array(
                  'en' => $array['name']
          ),
          'SingleSignOnService'  => $array['SingleSignOnService'],
          'certFingerprint'      => $array['certFingerprint']
  );

  if( trim($array['SingleLogoutService']) != '' )
  {
    $metadata[$key]['SingleLogoutService'] = $array['SingleLogoutService'];
  }
}
