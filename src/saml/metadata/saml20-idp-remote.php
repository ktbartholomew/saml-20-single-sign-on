<?php

$ini = null;
$wp_opt = get_option('saml_authentication_options');
$blog_id = (string)get_current_blog_id();

if (isset($wp_opt['idp_details'])) {
    $ini = parse_ini_string($wp_opt['idp_details'], true);
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
