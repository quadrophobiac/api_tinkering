<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On'); 
// Must pass session data for the library to work
// derived from http://www.benmarshall.me/facebook-sdk-php-v4/
// FBRLH_state is nothing of note
session_start();
 
// Include the libraries files
require_once( 'Facebook/Entities/AccessToken.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphSessionInfo.php' );
require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/HttpClients/FacebookCurl.php' );
require_once( 'Facebook/HttpClients/FacebookHttpable.php' );
require_once( 'Facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/FacebookSDKException.php' );
require_once( 'Facebook/FacebookRequestException.php' );
require_once( 'Facebook/FacebookAuthorizationException.php' );
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookRedirectLoginHelper.php' );
 
use Facebook\GraphSessionInfo;
use Facebook\FacebookSession;
use Facebook\FacebookCurl;
use Facebook\FacebookHttpable;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookResponse;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\FacebookSDKException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphObject;
 
// Replace the APP_ID and APP_SECRET with your apps credentials
FacebookSession::setDefaultApplication( '660024967417636','2eefa92d585da23aee04be9bc864404a' );
 
// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'
// e.g. $helper = new FacebookRedirectLoginHelper( 'http://mydomain.com/redirect' );
$helper = new FacebookRedirectLoginHelper( 'http://localhost/api_tinkering/another.php' );
//print_r($helper);
// Check if existing session exists
if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
  print ("_SESSION<br>\n");
  // Create new session from saved access_token
  $session = new FacebookSession( $_SESSION['fb_token'] );
  //

  // Validate the access_token to make sure it's still valid
  try {
    if ( ! $session->validate() ) {
      $session = null;
    }
  } catch ( Exception $e ) {
    // Catch any exceptions
    $session = null;
  }
} else {
  print ("no _SESSION<br>\n");
  print_r($_SESSION);
  // No session exists
  try {
    print("<br>try session reassignment <br>\n");
    print_r($helper);
    $session = $helper->getSessionFromRedirect(); // this breaks the code, but only on the click to loginURL
    // above method returns null
    if(!$session){echo "<br>null<br>";}
    else {echo($session."!!!<br>");} // doesn't print on first load, :. if(!$session){echo "<br>null<br>";} = confirm null
    print_r($helper); // does print
  } catch( FacebookRequestException $ex ) {
    echo "<br>FB Request exception = ".$ex;
    // When Facebook returns an error
  } catch( Exception $ex ) {

    // When validation fails or other local issues
    echo "Exception".$ex->message;
  }
  print("<br>end else <br>\n");
}

// Check if a session exists
if ( isset( $session ) ) {
  print("session<br>\n");
  // Save the session
  $_SESSION['fb_token'] = $session->getToken();

  // Create session using saved token or the new one we generated at login
  $session = new FacebookSession( $session->getToken() );
  // return (string) $this->accessToken;

  // Create the logout URL (logout page should destroy the session)
  $logoutURL = $helper->getLogoutUrl( $session, 'http://localhost/api_tinkering/out.php' );
} else {
  // No session
print("no session<br>\n");
  // Requested permissions - optional, If no permissions are provided, it’ll use Facebook’s default public_profile 
  // $permissions = array(
  //   'email',
  //   'user_location',
  //   'user_birthday'
  // );

  // Get login URL
  $loginUrl = $helper->getLoginUrl(); // if passing scope vars; getLoginUrl($permissions);
  // eg returned https://www.facebook.com/v2.0/dialog/oauth?
    //  client_id=660024967417636&
    //  redirect_uri=http%3A%2F%2Flocalhost%2Fapi_tinkering%2Fanother.php&
    //  state=326ced0fbce8e9a1a9fdec4f39a59a3c
    //  &sdk=php-sdk-4.0.9
    //  &scope=
  print $loginUrl."\n";
}

print "<br>end PHP <br>";
?>
<html>
  <head>
    
  </head>
  <body>
    <br><a href="<?php echo $loginURL ?>">Facebook Connect</a><br>
  <?php
    echo '<a href="' . $helper->getLoginUrl() . '">Login</a><br>' .$session;
    ?>
    <br><a href="http://localhost/api_tinkering/out.php">LogOut</a><br>
    <!-- <br><a href="<?php echo $logoutURL ?>">Logout</a><br> pasing var this way results in weird assignment-->
  </body>

</html>