<?php

$wp_opt = get_option('saml_authentication_options');
$blog_id = (string)get_current_blog_id();

$idp_file = constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini';

/*
 * Check database for idp detail configuration,
 * if not found, check for flat file configuration
 * otherwise, use empty config
 */
if (isset($wp_opt['idp_details']))
{
    $details = array_keys($wp_opt['idp_details']);
    $idp = $details[0];
}
elseif (file_exists($idp_file))
{
    $details = array_keys(parse_ini_file($idp_file ,true));
    $idp = $details[0];
}
else {
    $idp = NULL;
}


$config = array(

	// This is a authentication source which handles admin authentication.
	'admin' => array(
		// The default is to use core:AdminPassword, but it can be replaced with
		// any authentication source.

		'core:AdminPassword',
	),


	// An authentication source which can authenticate against both SAML 2.0
	// and Shibboleth 1.3 IdPs.

	$blog_id => array(
		'saml:SP',
		'NameIDPolicy' => $wp_opt['nameidpolicy'],
		// The entity ID of this SP.
		// Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
		'entityID' => NULL,
		'sign.authnrequest' => TRUE,
		'sign.logout' => TRUE,
		'redirect.sign' => TRUE,
		// The entity ID of the IdP this should SP should contact.
		// Can be NULL/unset, in which case the user will be shown a list of available IdPs.
		'idp' => $idp
	)
);

// Cert and Key may not exist,
// check database then for flat files
if(isset($wp_opt['certificate']['public_key']))
{
    $certificate = $wp_opt['certificate']['public_key'];
    $removedCertTag = str_replace('CERTIFICATE-----', '', $certificate);
    $removedBeginTag = str_replace('-----BEGIN', '', $removedCertTag);
    $noTags = str_replace('-----END', '', $removedBeginTag);
    $config[$blog_id]['certData'] = trim($noTags);
}
elseif (file_exists( constant('SAMLAUTH_CONF') . '/certs/' . $blog_id . '/' . $blog_id . '.cer') )
{
	$config[$blog_id]['certificate'] = constant('SAMLAUTH_CONF') . '/certs/' . $blog_id . '/' . $blog_id . '.cer';
}

// Set key paths
$upload_dir = constant('SAMLAUTH_CONF') . '/certs/' . $blog_id;
$keyPath = $upload_dir . '/' . $blog_id . '.key';

// If key information is found in database,
// but keyfile doesn't exist, create it
// otherwise, check if keyfile exists
if(
    isset($wp_opt['certificate']['private_key'])
    && !file_exists($keyPath)
)
{
    // Create all parent directories to store private key
    if(! is_dir($upload_dir))
    {
        mkdir($upload_dir, 0775, true);
    }

    if (
        file_put_contents(
            $keyPath,
            $wp_opt['certificate']['private_key']
        )
    )
    {
        $config[$blog_id]['privatekey'] = $keyPath;
    }
}
elseif( file_exists($keyPath))
{
	$config[$blog_id]['privatekey'] = $keyPath;
}

return $config;
