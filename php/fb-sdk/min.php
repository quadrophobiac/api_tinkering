<?php

// both of these instructive, though neither worked out of the box, so to speak 
// http://www.benmarshall.me/facebook-sdk-php-v4/
// http://metah.ch/blog/2014/05/facebook-sdk-4-0-0-for-php-a-working-sample-to-get-started/
// this is a minimal version - that logs in without doing all the possible error checking - however it is more legiible

ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'On'); 
// Must pass session data for the library to work
// derived from http://www.benmarshall.me/facebook-sdk-php-v4/
// FBRLH_state is nothing of note
session_start();
 
// Include the libraries files
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/Entities/AccessToken.php' );
require_once( 'Facebook/Entities/SignedRequest.php' ); // added to assuage FacebookSignedRequestFromInputHelper error
require_once( 'Facebook/GraphNodes/GraphObject.php' );
require_once( 'Facebook/GraphNodes/GraphSessionInfo.php' );
require_once( 'Facebook/HttpClients/FacebookCurl.php' );
require_once( 'Facebook/HttpClients/FacebookHttpClientInterface.php' );
//require_once( 'Facebook/HttpClients/FacebookHttpable.php' ); FacebookHttpClientInterface
require_once( 'Facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( 'Facebook/Exceptions/FacebookSDKException.php' );
require_once( 'Facebook/Exceptions/FacebookRequestException.php' );
require_once( 'Facebook/Exceptions/FacebookAuthorizationException.php' );
require_once( 'Facebook/Helpers/FacebookRedirectLoginHelper.php' );
 // requires FacebookSignedRequestFromInputHelper also apparently
require_once( 'Facebook/Helpers/FacebookSignedRequestFromInputHelper.php'); // if added before JS loginHelper, no error thrown

require_once( 'Facebook/Helpers/FacebookJavaScriptLoginHelper.php' ); // <-- the problem

use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSession;
use Facebook\Entities\AccessToken; // adding entities to alias doesn't hinder it
use Facebook\Entities\SignedRequest;
use Facebook\GraphNodes\GraphSessionInfo;
use Facebook\GraphNodes\GraphObject;
use Facebook\HttpClients\FacebookCurl; // adding HttpClients to alias doesn't hinder it
//use Facebook\HttpClients\FacebookHttpable; // adding HttpClients to alias doesn't hinder it
use Facebook\HttpClients\FacebookHttpClientInterface;
use Facebook\HttpClients\FacebookCurlHttpClient; // adding HttpClients to alias doesn't hinder it
use Facebook\Exceptions\FacebookAuthorizationException;
use Facebook\Exceptions\FacebookRequestException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Facebook\Helpers\FacebookSignedRequestFromInputHelper;

use Facebook\Helpers\FacebookJavaScriptLoginHelper; // <-- the problem

// Replace the APP_ID and APP_SECRET with your apps credentials
FacebookSession::setDefaultApplication( 'APP_ID','APP_SECRET' ); // 'APP_ID','APP_SECRET'
echo '<a href="http://localhost/api_tinkering/php/fb-sdk/logout.php?dest=min">LogOut</a><br>';

// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'

//$helper = new FacebookRedirectLoginHelper( 'http://localhost/api_tinkering/php/fb-sdk/min.php' ); // redirect helper
$helper = new FacebookJavaScriptLoginHelper(); // js helper
//
//
try {
  //$session = $helper->getSessionFromRedirect(); // php redirect helper
  $session = $helper->getSession(); // js helper
} catch( FacebookRequestException $ex ) {
  echo "facebook req exception ".$ex;
} catch( Exception $ex ) {
  // When validation fails or other local issues
  echo "other exception ".$ex;
}


    if ( isset( $session ) ) {
      print("session<br>\n");

      // Save the session
      $_SESSION['fb_token'] = $session->getToken();

      // Create session using saved token or the new one we generated at login
      $session = new FacebookSession( $session->getToken() );
      echo "testing session assignment from FacebookSession constructor<br>";
      echo print_r($session);
      // why reassign breaks when it saves
      // return (string) $session->accessToken; <- dont know whrere this came from!
      fbData($session);
    } else {
      // No session
    print("<br>no session<br>\n");
    echo var_dump($session);
      // Requested permissions - optional, If no permissions are provided, it’ll use Facebook’s default public_profile 
      // $permissions = array(
      //   'email',
      //   'user_location',
      //   'user_birthday'
      // );
    echo "<br>";
    // uncomment below depending on redirect or JS helper
      // $loginURL = $helper->getLoginUrl(); // if passing scope vars; getLoginUrl($permissions);
      // echo '<br><a href="' . $loginURL . '">The Real Login</a><br>' .$session;
    }
    echo '<br><a href="http://localhost/api_tinkering/php/fb-sdk/min.php">Home</a><br>';

    function fbData($session){
        //print some FB data
        $request = (new FacebookRequest( $session, 'GET', '/me' ))->execute();
        // Get response as an array
        $user = $request->getGraphObject()->asArray();
        foreach ($user as $e) {
          echo($e."<br>");
        }
    }
print "<br>end PHP <br>";
?>
<html>
  <head>
    
  </head>
  <body>
  <!-- js login div -->
    <div id="status">
       Click on Below Image to start the demo: <br/>
      <img src="http://hayageek.com/examples/oauth/facebook/oauth-javascript/LoginWithFacebook.png" style="cursor:pointer;" onclick="Login()"/>
    </div>
  </body>

</html>
