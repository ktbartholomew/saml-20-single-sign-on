<?php

namespace Ktbartholomew\SamlPlugin\SP\Endpoints;

use Ktbartholomew\SamlPlugin\Config\Saml;

class Metadata {
  function __construct() {
    Saml::outputMetadata();
    exit();
  }
}
