---
Contributors: ktbartholomew
Tags: sso, saml, single sign-on, simplesamlphp, onelogin, ssocircle
Requires at least: 3.3
Tested up to: 4.4
Stable tag: 0.9.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
---
# SAML 2.0 Single Sign-On WordPress Plugin

SAML 2.0 Single Sign-On allows you to use any SAML 2.0-compliant Identity Provider for Single Sign-On to your blog or network of blogs.  The plugin will replace the standard WordPress login screen and automatically redirect login/logout requests to your SSO portal. Group membership from the Identity Provider (such as Active Directory) can be used to determine what privileges the user will have on your blog, such as Administrator, Editor, or Subscriber. This plugin uses a modified version of the [SimpleSAMLPHP](https://simplesamlphp.org/) library for all SAML assertions, and can be configured exclusively from the WordPress Admin menu.

## Installation

1. Upload the contents of the plugin to the `/wp-content/plugins/` directory of your WordPress site.
2. Activate the plugin through the **Plugins** menu in WordPress
3. Configure the **Identity Provider** and **Service Provider** sections of the plugin in the **Settings > Single Sign-On** menu.
4. Enable the plugin to do authentication on the **General** section of the plugin.

## Development

Plugin development with this repo requires Docker and Docker Compose > `1.5.2`.

1. Copy the sample environment file, `env.sample` to `env`.
1. Assign values to each of the environment variables.
1. Run `script/build`
1. Run `script/start`

The Wordpress instance will be available on port 80 of your Docker host, with the plugin (in `./src`) mounted to the appropriate plugins directory inside the Wordpress container. This assumes your Docker host can see your working filesystem, which in many cases will require a Virtualbox-backed docker-machine instance.

## Changelog

### 0.9.2
* Updates a few look-and-feel things to fit better with WP 3.8
* Resolves an issue that was preventing the IdP from being recognized when adding IdP information manually.
* Respects the redirect_to parameter in wp-login.php, allowing SAML logins to redirect users to any page of your choosing.
* Adds an option to bypass SAML authentication and use the traditional method. With the option enabled, create and use a login URL like `http://example.com/wp-login.php?use_sso=false`

### 0.9.1
* The plugin is feature-complete until v1.0. All updates between 0.9.0 and 1.0 will be strictly bugfixes or improvements.
* Fewer warnings and errors when not all IdP attributes are specified.
* If a user's group membership changes at the IdP, their WordPress role will be changed accordingly at next login.

### 0.9.0
* Added nonces and basic type-checking to admin pages for improved security.
* Quick access to common attributes used by popular IdP's including ADFS, OneLogin, and SimpleSAMLPHP
* Extensive internal code improvements to improve maintainability

### 0.8.9
* Status check lets you know when everything appears to be configured correctly.
* Fixed an issue that prevented users from logging out if a Single Logout service was not specified.
* Fixed an issue that caused SP settings to get out of sync when importing IdP settings from metadata.

### 0.8.8
* IdP info can now be automatically loaded from a metadata URL.
* Signing certificate can be automatically generated if you don't know how (or don't want to do it yourself). The generated certificate can be downloaded so you can share it with your IdP.

### 0.8.7
* Uploading a certificate and private key is now optional, which makes IdP-initiated testing much simpler.
* Folders and config files are created if they don't already exist, which fixes many issues with various screens being blank.

### 0.8.6
* Moved configuration files from plugins directory to a subdirectory in uploads to ensure the plugin doesn't break itself when updated.
