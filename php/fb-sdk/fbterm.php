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

// $sessionToken = 'token'; //'token'
// // Create session using saved token
// $session = new FacebookSession($sessionToken);

$session = new FacebookSession('token');

// Get the GraphUser object for the current user:

try {
  // $me = (new FacebookRequest(
  //   $session, 'GET', '/me'
  // ))->execute()->getGraphObject(GraphUser::className());
  // echo $me->getName();
  // $response = $request->execute();
  // get response
$request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' );
  // if siphoning data, remove id,name as they return strings rather than objects
  $response = $request->execute(); // using method from FacebookResponse
  $graphObject = $response->getGraphObject();

  // echo print_r($graphObject->asArray())."\n"; // returns a slightly different data structure to var_dump graphObject
  // said structure logged in gObjectasArray.txt
  $allArray = $graphObject->asArray();
  echo $allArray['id']."\n";
  echo $allArray['name']."\n";
 //  echo $allArray['books']->data()."\n";
  // foreach($allArray as $key => $value) {
  //   print $key." => ".$value."\n";
  // } // breaks when it arrives at the Objects

  // echo print_r($allArray['books']); // 
  // echo print_r($allArray['books']->data);
  $lala = $allArray['books']->data; // this is now the array of data for books
  print_r($lala[5]->name);
  // echo $graphObject->getProperty('name')."\n"; // passes with 'name' because name has a string as its value pair
  // echo $graphObject->getPropertyAsArray('likes')."\n"; // returns null
  // echo var_dump($graphObject->getPropertyAsArray('likes'))."\n"; // returns null


  // MASS DUMPING METHODS, brutal but effective
  // var_dump($graphObject); 
  // print_r($graphObject); // uncomment if not writing to a file
  // print_r($graphObject->getPropertyNames()); // uncomment if not writing to a file :: writes an array of whats returned from request
  //fbData($session);
} catch (FacebookRequestException $e) {
  // The Graph API returned an error
} catch (\Exception $e) {
  // Some other error occurred
}

// getGraphObjectList approach
// returns empty array

// $request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' );
// $response = $request->execute();
// $graphList = $response->getGraphObjectList();
// print_r($graphList);


    function fbData($session){
      print "function fbData follows ... \n";
        try {
          $retrieved = fopen('/home/stephen/termtest.txt', 'a');
        } catch (\Exception $ex) {
            echo "file open error".$ex."\n";
        }
        fwrite($retrieved,"TEST entry\n");
        //print some FB data
        $parameters = array('books','education');
        $request = (new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' ))->execute();
        // Get response as an array
        $user = $request->getGraphObject()->asArray();
        // print_r($user); // uncomment if not writing to a file
        // var_dump($user); // uncomment if not writing to a file
        // foreach ($user as $e) {
        //   try {
        //     fwrite($retrieved,$e."\n");
        //   } catch (\Exception $ex) {
        //     echo "read write error".$ex."\n";
        //   }
        //   echo($e."<br>");
        // }
          fclose($retrieved);
    }

print "<br>end PHP <br>";
print "registed change?\n";
?>
