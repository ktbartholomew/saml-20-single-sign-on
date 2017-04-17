<?php

namespace Ktbartholomew\SamlPlugin\SP;

use Ktbartholomew\SamlPlugin\Config\Saml;
use Ktbartholomew\SamlPlugin\Config\Wordpress;
use \OneLogin_Saml2_Auth as Auth;

class SP {
  private $opt;

  function __construct() {
    $this->settings = Wordpress::getConfig();

    add_action('lost_password', array($this,'disable_function'));
    add_action('retrieve_password', array($this,'disable_function'));
    add_action('password_reset', array($this,'disable_function'));
    add_filter('show_password_fields', array($this,'show_password_fields'));

    add_action('init', function () {
      if(isset($_GET['saml_endpoint'])) {
        switch ($_GET['saml_endpoint']) {
          case 'metadata':
            new Endpoints\Metadata();
          break;
          case 'authn':
            $settings = Saml::getSettingsArray();
            $auth = new Auth($settings);

            $auth->login();
          break;
          case 'assertion':
            new Endpoints\Assertion();
          break;
        }
      }


    });

    if ($this->settings['enabled']) {
			add_action('wp_authenticate',array($this,'require_auth'));
	    add_action('wp_logout',array($this,'logout'));
      add_action('login_form', array($this, 'modify_login_form'));
		}
  }

  /**
   *  Authenticates the user using SAML
   *
   *  @return void
   */
  public function require_auth() {
    // If the user just logged out, don't try to log in again.
    if (isset($_GET['loggedout']) && $_GET['loggedout'] == 'true') {
      return;
    }

    // If the user is trying to bypass SSO, bail out
    $bypass_allowed = $this->settings['allow_sso_bypass'] === true;
    $bypass_requested = (isset($_GET['use_sso']) && $_GET['use_sso'] === 'false') || (isset($_POST['use_sso']) && $_POST['use_sso'] === 'false');
    if ($bypass_allowed && $bypass_requested) {
      return;
    }

    $settings = Saml::getSettingsArray();
    $auth = new Auth($settings);
    $redirect_url = (array_key_exists('redirect_to', $_GET)) ? wp_login_url( $_GET['redirect_to']) : get_admin_url();

    $auth->login($redirect_url);
    exit();
  }

  public function loginWithAssersion($nameID, $attributes) {
    if(array_key_exists($this->settings['attributes']['username'], $attributes) ) {
      $username = $attributes[$this->settings['attributes']['username']][0];
      if(get_user_by('login',$username)) {
        $this->simulate_signon($username, $attributes);
      } else {
        $this->new_user($attributes);
      }
    } else {
      http_response_code(400);

      printf("Received a valid assertion from the identity provider, but could not log in.\n");
      printf("Required attribute `%s` was not found in the assertion.", $this->settings['attributes']['username']);

      exit();
    }
  }

  /**
   * Sends the user to the SAML Logout URL (using SLO if available) and then redirects to the site homepage
   *
   * @return void
   */
  public function logout() {
    $this->saml->logout( get_option('siteurl') );
  }

  /**
   * Runs about halfway through the login form. If we're bypassing SSO, we need to add a field to the form
   *
   * @return void
   */
  public function modify_login_form() {
    if( array_key_exists('use_sso', $_GET) && $_GET['use_sso'] == 'false' && $this->settings['allow_sso_bypass'] == true )
    {
      echo '<input type="hidden" name="use_sso" value="false">'."\n";
    }
  }

  /**
   * Creates a new user in the WordPress database using attributes from the IdP
   *
   * @param array $attrs The array of attributes created by SimpleSAMLPHP
   * @return void
   */
  public function new_user($attrs) {
    $login = (array_key_exists($this->settings['attributes']['username'],$attrs)) ? $attrs[$this->settings['attributes']['username']][0] : 'NULL';
    $email = (array_key_exists($this->settings['attributes']['email'],$attrs)) ? $attrs[$this->settings['attributes']['email']][0] : '';
    $first_name = (array_key_exists($this->settings['attributes']['firstname'],$attrs)) ? $attrs[$this->settings['attributes']['firstname']][0] : '';
    $last_name = (array_key_exists($this->settings['attributes']['lastname'],$attrs)) ? $attrs[$this->settings['attributes']['lastname']][0] : '';
    $display_name = ($first_name || $last_name) ? $first_name . ' ' . $last_name : $login;

    $role = $this->update_role($attrs);

    if( $role !== false ) {
      $user_opts = array(
        'user_login' => $login,
        'user_pass'  => $this->random_password(),
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'display_name' => $display_name
      );

      wp_insert_user($user_opts);
      $this->simulate_signon($login, $attrs);
    } else {
      die('The website administrator has not given you permission to log in.');
    }
  }

  /**
   * Authenticates the user with WordPress using wp_signon()
   *
   * @param string $username The user to log in as.
   * @return void
   */
  public function simulate_signon($username, $attributes)
  {
    $this->update_role($attributes);
    $user = get_user_by('login', $username);
    wp_set_auth_cookie($user->ID);

    if( array_key_exists('redirect_to', $_GET) )
    {
      wp_redirect( $_GET['redirect_to'] );
    }
    else
    {
      wp_redirect(get_admin_url());
    }
    exit();
  }

  /**
   * Updates a user's role if their current one doesn't match the attributes provided by the IdP
   *
   * @return string
   */
  private function update_role($attributes)
  {
    if(array_key_exists($this->settings['attributes']['groups'], $attributes) )
    {
      $role = array();
      foreach(wp_roles()->roles as $role_name => $role_meta){
          if( in_array($this->settings['groups'][$role_name], $attributes[$this->settings['attributes']['groups']]) )
          {
            $role[] = $role_name;
          }
      }
      if(!empty($role)){}
      elseif( $this->settings['allow_unlisted_users'] )
      {
        $role[] = 'subscriber';
      }
      else
      {
        $role = false;
      }
    }
    else
    {
      $role = false;
    }

    $user = get_user_by('login',$attributes[$this->settings['attributes']['username']][0]);

    if($user) {
      $user->set_role('');
      foreach($role as $user_role) {
        $user->add_role($user_role);
      }
    }

    return $role;
  }

  /**
   * Return a big random string. Depends on OpenSSL.
   */
  private function random_password() {
    return openssl_random_pseudo_bytes(64);
  }

  public function show_password_fields($show_password_fields) {
    return false;
  }

  public function disable_function() {
    die('Disabled');
  }
}
