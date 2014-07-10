<?php
// attempting to use composer, locally installed

ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'On');

session_start();
require 'vendor/autoload.php';

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

// Replace the APP_ID and APP_SECRET with your apps credentials
FacebookSession::setDefaultApplication( 'app_id','app_key' );
echo '<a href="http://localhost/api_tinkering/php/logout.php?dest=fb">LogOut</a><br>';
// if ($_SERVER['REQUEST_METHOD'] == "POST") {
//   echo "post detected<br><br>";
// }
// if ($_SERVER['REQUEST_METHOD'] == "GET") { won't
//   echo "get detected<br><br>";
// }

// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'
// e.g. $helper = new FacebookRedirectLoginHelper( 'http://mydomain.com/redirect' );
$helper = new FacebookRedirectLoginHelper( 'http://localhost/api_tinkering/php/fb.php' );
//print_r($helper);
// Check if existing session exists
    if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
      print ("_SESSION<br>\n");
      print_r($_SESSION);
      // create a new session object
      $session = new FacebookSession( $_SESSION['fb_token'] );
//fbData($session);
                    // Create new session from saved access_token
                    // $session = new FacebookSession( $_SESSION['fb_token'] );
                    // $request = (new FacebookRequest( $session, 'GET', '/me' ))->execute();
                    // // Get response as an array
                    // $user = $request->getGraphObject()->asArray();
                    // foreach ($user as $e) {
                    //   echo($e."<br>");
                    // }
                    //

      // Validate the access_token to make sure it's still valid - this try catch returns null
      try {
        if ( ! $session->validate() ) {
          $session = null;
        }
      } catch ( Exception $e ) {
        // Catch any exceptions
        //$session = null;
      }
      echo "after try and catch<br>";
      echo var_dump($session);
    } else {
      print ("no _SESSION<br>\n");
      print_r($_SESSION);
      // No session exists
      try {
        print("<br>try session reassignment <br>\n");
        print count( array_filter((array)$helper) ); // expect 5
        // print count(array_filter(array)$helper));
        $session = $helper->getSessionFromRedirect(); // this breaks the code, but only on the click to loginURL
            echo "<br>getSessionFromRedirect returned: ";
            echo var_dump($session)."<br>";
        print count( array_filter((array)$helper) ); // expect 6

      } catch( FacebookRequestException $ex ) {
        echo "<br>FB Request exception = ".$ex;
        // When Facebook returns an error
      } catch( Exception $ex ) {

        // When validation fails or other local issues
        echo "Exception".$ex->message;
      }
      print("<br>end else <br>\n");
    } /* END $_SESSION CONTROL FLOW*/

// BEGIN FacebookSession CONTROL FLOW LOGIC
//
//
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
      $loginURL = $helper->getLoginUrl(); // if passing scope vars; getLoginUrl($permissions);
      echo '<br><a href="' . $loginURL . '">The Real Login</a><br>' .$session;
    }
    echo '<br><a href="http://localhost/api_tinkering/php/fb.php">Home</a><br>';

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
    <br><a href="http://localhost/api_tinkering/php/fb.php">Home</a><br>
  </body>

</html>
