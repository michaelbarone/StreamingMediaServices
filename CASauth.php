<?php

/**
 * Example that changes html of phpcas messages
 *
 * PHP Version 5
 *
 * @file     example_html.php
 * @category Authentication
 * @package  PhpCAS
 * @author   Joachim Fritschi <jfritschi@freenet.de>
 * @author   Adam Franco <afranco@middlebury.edu>
 * @license  http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link     https://wiki.jasig.org/display/CASC/phpCAS
 */
 
 
// set this in init.php to bypass cas login.  login details set in init.php when this is enabled 
if(isset($disableCAS) && $disableCAS===1) {
	return;
} 
 
// Load the settings from the central config file
require_once 'CASconfig.php';
// Load the CAS lib
require_once $phpcas_path . '/CAS.php';

// Enable debugging
$date = date('Y-m-d');
$thispath = dirname(__FILE__);
phpCAS::setDebug("$thispath/assets/logs/CAS-log-$date.log");


// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context, false);

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server.
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
phpCAS::setNoCasServerValidation();

// customize HTML output
phpCAS::setHTMLHeader(
    '<html>
  <head>
    <title>__TITLE__</title>
  </head>
  <body>
  <h1>__TITLE__</h1>'
);
phpCAS::setHTMLFooter(
    '<hr>
    <address>
      phpCAS __PHPCAS_VERSION__,
      CAS __CAS_VERSION__ (__SERVER_BASE_URL__)
    </address>
  </body>
</html>'
);

$auth = phpCAS::checkAuthentication();
if(!$auth && (!isset($checkCASlogged) || $checkCASlogged === 0)) {
	phpCAS::handleLogoutRequests(false);
	// force CAS authentication
	phpCAS::forceAuthentication();
}
// reset this to 0;
$checkCASlogged = 0;
// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().
if($auth){
$userid = strtolower(phpCAS::getUser());
$_SESSION['userid']="$userid";
$userATTR = phpCAS::getAttributes();
return $userATTR;
}
?>
