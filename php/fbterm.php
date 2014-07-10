<?php
// both of these instructive, though neither worked out of the box, so to speak 
// http://www.benmarshall.me/facebook-sdk-php-v4/
// http://metah.ch/blog/2014/05/facebook-sdk-4-0-0-for-php-a-working-sample-to-get-started/
// this is a minimal version - that logs in without doing all the possible error checking - however it is more legiible

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');
// error checking SO IMPORTANT!!!! http://stackoverflow.com/questions/1475297/phps-white-screen-of-death
// Must pass session data for the library to work
// derived from http://www.benmarshall.me/facebook-sdk-php-v4/
// FBRLH_state is nothing of note
session_start();

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
FacebookSession::setDefaultApplication( 'app_id','app_secret' );

$sessionToken = 'fb_token';
// Create session using saved token
$session = new FacebookSession($sessionToken);

if ( isset( $session ) ) {
  // graph api request for user data
  $parameters = array('books','education');
  $usrid = '___';
  //$request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,books,education,family,favorite_athletes,favorite_teams,events,groups,inspirational_people,interests,interested_in,likes,work' );
  // more specific named query
  // me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,events.name,groups.name,inspirational_people,interests,interested_in,likes.name,work
  $request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject();
	$user = $response->getGraphObject(GraphUser::className());
  // print data
  // echo  print_r( $graphObject, 1 );

   	var_dump($graphObject);
   	print_r($graphObject->getPropertyNames()); // prints list of array/object IDs
   	//fbData($session);
}

//fbData($session);
    function fbData($session){
    	print "function fbData follows ... \n";
        //print some FB data
        $parameters = array('books','education');
        $request = (new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' ))->execute();
        // Get response as an array
        $user = $request->getGraphObject()->asArray();
        print_r($user);
        var_dump($user);

    }


print "<br>end PHP <br>";
?>

