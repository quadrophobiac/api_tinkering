<?php
// both of these instructive, though neither worked out of the box, so to speak 
// http://www.benmarshall.me/facebook-sdk-php-v4/
// http://metah.ch/blog/2014/05/facebook-sdk-4-0-0-for-php-a-working-sample-to-get-started/
// this is a minimal version - that logs in without doing all the possible error checking - however it is more legiible
//  set_include_path('/var/www/api_tinkering/Facebook');
ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'On');
// error checking SO IMPORTANT!!!! http://stackoverflow.com/questions/1475297/phps-white-screen-of-death
// Must pass session data for the library to work
// derived from http://www.benmarshall.me/facebook-sdk-php-v4/
// FBRLH_state is nothing of note
session_start();
$_COOKIE['fbsr_'] = "HOLY SHIT";
// Include the libraries files
require_once( 'Facebook/Entities/AccessToken.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphUser.php' );
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

require_once('Facebook/FacebookJavaScriptLoginHelper.php ');
require_once('Facebook/FacebookSignedRequestFromInputHelper.php');

use Facebook\AccessToken;
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
use Facebook\GraphUser;

use Facebook\FacebookSignedRequestFromInputHelper;
use Facebook\FacebookJavaScriptLoginHelper;

// Replace the APP_ID and APP_SECRET with your apps credentials
FacebookSession::setDefaultApplication( 'app_id','app_secret' );
echo '<a href="http://localhost/api_tinkering/logout.php">LogOut</a><br>';
echo print_r($_COOKIE);

// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'

//$helper = new FacebookRedirectLoginHelper( 'http://localhost/api_tinkering/phpjs.php' );
$helper = new FacebookJavaScriptLoginHelper();

try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  echo "facebook error = ".$ex;
} catch( Exception $ex ) {
  // When validation fails or other local issues
  echo "other fucking error".$ex;
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
      $permissions = array(
        'email',
        'user_location',
        'user_events',
        'user_friends',
        'user_groups',
        'user_hometown',
        'user_interests',
        'user_likes',
        'user_birthday'
        );
      //   publish_actions,email,user_photos,user_videos,user_likes
      // );
    echo "<br>";
      $loginURL = $helper->getLoginUrl($permissions); // if passing scope vars; getLoginUrl($permissions);
      echo '<br><a href="' . $loginURL . '">The Real Login</a><br>' .$session;
    }
    echo '<br><a href="http://localhost/api_tinkering/phpjs.php">Home</a><br>';

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

  </body>

</html>
