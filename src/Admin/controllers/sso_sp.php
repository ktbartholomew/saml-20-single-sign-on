<?php
  // Setup Default Options Array

  if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'],'sso_sp') )
  {
      if( isset($_POST['auto_cert']) )
      {
        $pk = openssl_pkey_new(array('private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA));

        global $current_user;
        get_currentuserinfo();

        $dn = array(
        "countryName" => "US",
        "organizationName" => get_bloginfo('name'),
        "commonName" => get_bloginfo('name') . " SAML Signing Certificate",
        "emailAddress" => $current_user->user_email
        );

        $csr = openssl_csr_new($dn,$pk);
        $crt = openssl_csr_sign($csr,null,$pk,1825);

        $keyfile = null;
        $certfile = null;

        openssl_pkey_export($pk,$keyfile);
        openssl_x509_export($crt,$certfile);


        //store keys in database
        $this->settings['certificate']['public_key'] = $certfile;
        $this->settings['certificate']['private_key'] = $keyfile;
        Ktbartholomew\SamlPlugin\Config\Wordpress::writeConfig($this->settings);
      }
      elseif( ( isset($_FILES['certificate']) && isset($_FILES['privatekey']) ) && ( $_FILES['certificate']['error'] == 0 && $_FILES['privatekey']['error'] == 0 ) )
      {
          $cert = file_get_contents($_FILES['certificate']['tmp_name']);
          $key = file_get_contents($_FILES['privatekey']['tmp_name']);
          if(openssl_x509_check_private_key($cert,$key))
          {
              //keys pass openssl key pair check,
              //store keys in database
              $this->settings->set_public_key($cert);
              $this->settings->set_private_key($key);
          }
          else
          {
              echo '<div class="error below-h2"><p>The certificate and private key you provided do not correspond to one another. They were not saved.</p></div>'."\n";
          }
      }

      // Update settings
      $this->settings['attributes']['username'] = $_POST['username_attribute'];
      $this->settings['attributes']['firstname'] = $_POST['firstname_attribute'];
      $this->settings['attributes']['lastname'] = $_POST['lastname_attribute'];
      $this->settings['attributes']['email'] = $_POST['email_attribute'];
      $this->settings['attributes']['groups'] = $_POST['groups_attribute'];

      foreach(wp_roles()->roles as $role_name => $role_meta){
          $this->settings['groups'][$role_name] = $_POST[sprintf('%s_group', $role_name)];
      }

      $this->settings['idp'] = $_POST['idp'];
      $this->settings['nameidpolicy'] = $_POST['nameidpolicy'];
      $this->settings['allow_unlisted_users'] = ($_POST['allow_unlisted_users'] == 'allow') ? true : false;

      Ktbartholomew\SamlPlugin\Config\Wordpress::writeConfig($this->settings);
  }

  $status = $this->get_saml_status();

  include(__DIR__ . '/../views/nav_tabs.php');
  include(__DIR__ . '/../views/sso_sp.php');

?>
