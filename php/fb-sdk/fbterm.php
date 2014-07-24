<?php
// Next Steps
// Understand which edges dont utilise dataArray = ( {name: ___, id:____ },{name: ___, id:____ }, ... {name: ___, id:____ }  ) structure
// creating seperate fb Graph Decomposition file for this purpose
// 
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
use Facebook\Helpers\FacebookJavaScriptLoginHelper;



// Replace the APP_ID and APP_SECRET with your apps credentials
FacebookSession::setDefaultApplication( 'APP_ID','APP_SECRET' ); // 'APP_ID','APP_SECRET'

// $sessionToken = 'token'; //'token'
// // Create session using saved token
// $session = new FacebookSession($sessionToken);

$session = new FacebookSession('token');

// Get the GraphUser object for the current user:

$pageObjectEdges = array ('books','movies','music','games','television','groups','interests');
$like = array('likes'); // both $identifier->category and $identifier->name of use
$event = array('events'); // $identifier->location rather than ...->name of interest
try {

$request = new FacebookRequest( $session, 'GET', '/me?fields=books.name,music,television,movies,education,family,favorite_athletes.name,favorite_teams,events,groups.name,inspirational_people,interests,interested_in,likes,work' );
  // if siphoning data, remove id,name as they return strings rather than objects
  $response = $request->execute(); // using method from FacebookResponse
  // ->execute()->getGraphObject(GraphUser::className()); // this may be needed later to determine common likes
  $graphObject = $response->getGraphObject();
  $indices = $graphObject->getPropertyNames(); // returns an array of all properties for which values exist from FacebookRequest call above
  array_pop($indices); // an id element is included as part of the GraphObject class and is unneeded, this removes this for data munging
  print_r($indices);
  // use to debug correct permissions associated with token
  $fetchedData = $graphObject->asArray(); // returns a slightly different data structure to var_dump graphObject
  // said structure logged in gObjectasArray.txt
fbEdges($fetchedData, $pageObjectEdges, $indices);
fbLikes($fetchedData, $like, $indices);
  // MASS DUMPING METHODS, brutal but effective
  // var_dump($graphObject); 
  // print_r($graphObject); // uncomment if not writing to a file
  // print_r($graphObject->getPropertyNames()); // uncomment if not writing to a file :: writes an array of whats returned from request
  //fbData($session); // invoke fbData function
} catch (FacebookRequestException $e) {
  // The Graph API returned an error
} catch (\Exception $e) {
  // Some other error occurred
}

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
    // arrays to be passed to fb edges, toArray returns these entitites as Objects, whose first property(data) is an array of objects

     // $identifier->name
    // fb Edge Edge Cases
    

    function fbEdges($fbAsArray, $edges, $indices){
      // requires a GraphObject->asArray array, an array of valid FB API edges, and returned property names [->getPropertyNames()] as indices
        foreach ($edges as $filter){
          if(in_array($filter, $indices)){
            $innerArray = $fbAsArray[$filter]->data; // this is the array which houses every page one has liked
            // print "the contents of ".$filter." are as follows: \n";
            // print_r($innerArray);
            print $filter." as follows \n******\n";
            foreach ($innerArray as $ting){ // $ting is each element of the array, which are represented as {name:__, id:__} objects
              print "id: ".$ting->id.", handle: ".$ting->name."\n";
                // this is a atomised version of $graphObject->asArray()['nameOfFbCategory']->data->name
            }
            print "******\n";
          }
      }
    }

    function fbLikes($fbAsArray, $edges, $indices){
      // because each object can have its own category this needs to be a separate function
      // requires a GraphObject->asArray array, an array of valid FB API edges, and returned property names [->getPropertyNames()] as indices
        foreach ($edges as $filter){
          if(in_array($filter, $indices)){
            $innerArray = $fbAsArray[$filter]->data; // this is the array which houses every page one has liked
            // print "the contents of ".$filter." are as follows: \n";
            // print_r($innerArray);
            print $filter ." as follows \n******\n";
            foreach ($innerArray as $ting){ // $ting is each element of the array, which are represented as {name:__, id:__} objects
              print "Category: ".$ting->category." , ";
              print $ting->name."\n";
                // this is a atomised version of $graphObject->asArray()['nameOfFbCategory']->data->name
            }
            print "******\n";
          }
      }
    }

    // arrays comprising fbFields, toArray returns as an Array of Objects

    function fbFields(){

    }

print "<br>end PHP <br>";
print "registed change?\n";
?>
