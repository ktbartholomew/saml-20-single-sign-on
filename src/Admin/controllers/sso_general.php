<?php
  global $saml_opts;
  $status = $this->get_saml_status();

  if (isset($_POST['submit']) &&  wp_verify_nonce($_POST['_wpnonce'],'sso_general') )
  {
    if(get_option('saml_authentication_options'))
    		$saml_opts = get_option('saml_authentication_options');

    // Is the enable box checked, and is the plugin ready to be enabled?
    if(isset($_POST['enabled']) && $_POST['enabled'] == 'enabled')
    {
      if($status->num_errors === 0)
      {
        $saml_opts['enabled'] = true;
      }
      else
      {
        $saml_opts['enabled'] = false;
        echo '<div class="error settings-error"><p>There are still errors in your SAML configuration (see the status table below). You cannot enable SAML authentication until all errors are resolved.</p></div>'."\n";
        echo '<script type="text/javascript">jQuery(\'.updated.settings-error\').remove();</script>';
      }
    }
    else
    {
      $saml_opts['enabled'] = false;
    }

    // Is the Allow SSO Bypass box checked?
    if (isset($_POST['allow_sso_bypass']) && $_POST['allow_sso_bypass'] == 'yes' )
    {
      $saml_opts['allow_sso_bypass'] = true;
    }
    else
    {
      $saml_opts['allow_sso_bypass'] = false;
    }

    update_option('saml_authentication_options', $saml_opts);
  }

  if(get_option('saml_authentication_options'))
  {
		$saml_opts = get_option('saml_authentication_options');
	}

  $metadata = [
    'entityID' => '',
    'Logout' => '',
    'Consumer' => ''
  ];

  include(__DIR__ . '/../views/nav_tabs.php');
	include(__DIR__ . '/../views/sso_general.php');

?>
