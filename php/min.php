<?php
// attempting to use composer, locally installed

ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 'On');

session_start();
require 'vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;


// Replace the APP_ID and APP_SECRET with your apps credentials
FacebookSession::setDefaultApplication( '660024967417636','2eefa92d585da23aee04be9bc864404a' );
echo '<a href="http://localhost/api_tinkering/php/logout.php?dest=min">LogOut</a><br>';

// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'

$helper = new FacebookRedirectLoginHelper( 'http://localhost/api_tinkering/php/min.php' );

//
//
try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
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
      $loginURL = $helper->getLoginUrl(); // if passing scope vars; getLoginUrl($permissions);
      echo '<br><a href="' . $loginURL . '">The Real Login</a><br>' .$session;
    }
    echo '<br><a href="http://localhost/api_tinkering/php/min.php">Home</a><br>';

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
