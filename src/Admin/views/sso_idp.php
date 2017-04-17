<div class="wrap">
    <h2>SAML Identity Provider Settings</h2>
    <p><strong>Note:</strong> A valid Identity Provider (IdP) must be defined before <?php if( is_multisite()){ echo 'any sites in the network';} else{ echo 'the site';}?> can use Single-Sign On.<?php if( is_multisite()){ echo ' These settings affect all sites in your network.';}?></p>
  <form method="post">
    <?php wp_nonce_field('sso_idp_metadata'); ?>
    <h3>Autofill using Metadata</h3>
    <label for="metadata_url">URL to IdP Metadata </label><input type="text" name="metadata_url" size="40" />
    <input type="submit" name="fetch_metadata" class="button" value="Fetch Metadata"/>
  </form><br/>

  <div class="option-separator"><span class="caption">OR</span></div>

  <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__); ?>&updated=true">
  <?php wp_nonce_field('sso_idp_manual'); ?>
    <h3>Enter IdP Info Manually</h3>
    <fieldset class="options">



    <table class="form-table">
        <?php
            foreach($metadata as $key => $idp)
            {
?>
<tr valign="top">
  <th scope="row"><label for="idp_name">IdP name</label></th>
  <td><input type="text" name="idp_name" id="sp_auth_inp" value="<?php echo $idp['name']['en']; ?>" size="40" />
  <span class="setting-description">The name that will appear when setting up a service provider.</span>
  </td>
</tr>
<tr valign="top">
  <th scope="row"><label for="idp_identifier">URL Identifier</label></th>
  <td><input type="text" name="idp_identifier" id="idp_identifier" value="<?php echo $key; ?>" size="40" />
  <span class="setting-description">The URL that identifies this particular IdP.</span>
  </td>
</tr>
<tr valign="top">
  <th scope="row"><label for="idp_signon">Single Sign-On URL</label></th>
  <td><input type="text" name="idp_signon" id="idp_signon" value="<?php echo $idp['SingleSignOnService']; ?>" size="40" />
  <span class="setting-description">Tthe URL where sign-on assertions are sent.</span>
  </td>
</tr>
<tr valign="top">
  <th scope="row"><label for="idp_logout">Single Logout URL</label></th>
  <td><input type="text" name="idp_logout" id="idp_logout" value="<?php if( array_key_exists('SingleLogoutService',$idp) ){echo $idp['SingleLogoutService'];} ?>" size="40" />
  <span class="setting-description">The URL where logout assertions are sent.</span>
  </td>
</tr>
<tr valign="top">
  <th scope="row"><label for="idp_fingerprint">Certificate Fingerprint</label></th>
  <td><input type="text" name="idp_fingerprint" id="idp_fingerprint" value="<?php echo implode(":",str_split($idp['certFingerprint'],2)); ?>" size="40" style="font-family: monospace;"/>
  <span class="setting-description">The fingerprint of the certificate that the IdP uses to sign assertions.</span>
  </td>
</tr>
<?php
            }
            ?>
    </table>
    </fieldset>
    <div class="submit">
      <input type="submit" name="submit" class="button button-primary" value="Update Options" />
    </div>
  </form>


</div>
