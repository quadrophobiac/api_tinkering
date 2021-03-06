<?php

// this uses the new SDK /facebook-php-sdk-v4
// requires PHP 5.4 -___- 
// that SDK further compartmentalises the src files previously obtained
// does not work on Chrome, unable to test it on other browsers because of web upload difficulty

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');
//ini_set('display_errors', 'On'); 
// Must pass session data for the library to work
// derived from http://www.benmarshall.me/facebook-sdk-php-v4/
// FBRLH_state is nothing of note
session_start();
date_default_timezone_set('Europe/London');
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
echo '<a href="http://localhost/api_tinkering/php/fb-sdk/logout.php?dest=min">LogOut</a><br>';

echo "testing session storage: ";
echo var_dump($_SESSION);
echo "<br>";
// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'

// global vars used for parsing fb graph data

$event = array('events'); // $identifier->location rather than ...->name of interest

$fieldEntitites = array ('education', 'work');

//$helper = new FacebookRedirectLoginHelper( 'http://localhost/api_tinkering/php/fb-sdk/min.php' ); // redirect helper
$helper = new FacebookJavaScriptLoginHelper(); // js helper
//
//
try {
  //$session = $helper->getSessionFromRedirect(); // php redirect helper
  echo "php is trying to get a helper from javascript<br>";
  $session = $helper->getSession(); // js helper
  echo "has PHP extracted session <br>";
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
      // this step appears redundant because it can still access the js cookie
      // can't destroy the cookie because I don't know its name.
      // Create session using saved token or the new one we generated at login
      $session = new FacebookSession( $session->getToken() );
      echo "testing session assignment from FacebookSession constructor<br>";
      //echo print_r($session);

try {
  echo "try<br>";
  $pageObjectEdges = array ('books','movies','music','games','television','groups','interests');
  $like = array('likes'); // both $identifier->category and $identifier->name of use
  // id candidates = {545287250} 545287250
  $request = new FacebookRequest( $session, 'GET', '/me?fields=id,books.name,music,television,movies,education,family,favorite_athletes.name,favorite_teams,events,groups.name,inspirational_people,interests,interested_in,likes,work' );
  // if siphoning data, remove id,name as they return strings rather than objects
  $response = $request->execute(); // using method from FacebookResponse
  // ->execute()->getGraphObject(GraphUser::className()); // this may be needed later to determine common likes
  $graphObject = $response->getGraphObject();
  $indices = $graphObject->getPropertyNames(); // returns an array of all properties for which values exist from FacebookRequest call above
  array_pop($indices); // an id element is included as part of the GraphObject class and is unneeded, this removes this for data munging
  array_shift($indices); // id is not used after this point
  echo print_r($indices);
  // use to debug correct permissions associated with token
  $fetchedData = $graphObject->asArray(); // returns a slightly different data structure to var_dump graphObject
  // said structure logged in gObjectasArray.txt

  $fileOut = $fetchedData['id'].".json";


  fbEdges($fetchedData, $pageObjectEdges, $indices, $fileOut);
  fbLikes($fetchedData, $like, $indices);
  echo "try<br>";
} catch (FacebookRequestException $fb) {
  echo "there was a facebook error "+print_r($fb);
  // The Graph API returned an error
} catch (\Exception $e) {
  // Some other error occurred
}


    } else {
      // No session
    print("<br>no session<br>\n");
    // echo var_dump($session);

    // echo "<br>";
    // uncomment below depending on redirect or JS helper
      // $loginURL = $helper->getLoginUrl(); // if passing scope vars; getLoginUrl($permissions);
      // echo '<br><a href="' . $loginURL . '">The Real Login</a><br>' .$session;
    }
    echo '<br><a href="http://localhost/api_tinkering/php/fb-sdk/min.php">Home</a><br>';

// this was not running because var/www has permission issues

    function fbEdges($fbAsArray, $edges, $indices, $json){
      // requires a GraphObject->asArray array, an array of valid FB API edges, and returned property names [->getPropertyNames()] as indices
      $retrieved = fopen('termtest.txt', 'a');
      fwrite($retrieved,"inside fbEdges subroutine\n");
      $arrOfArr = array();
        foreach ($edges as $filter){
          if(in_array($filter, $indices)){
            $innerArray = $fbAsArray[$filter]->data; // this is the array which houses every page one has liked
            // print "the contents of ".$filter." are as follows: \n";
            // print_r($innerArray);
            print $filter." as follows \n******\n";
            $assArr = array();
            foreach ($innerArray as $ting){ // $ting is each element of the array, which are represented as {name:__, id:__} objects
              // /print "id: ".$ting->id.", handle: ".$ting->name."\n";
                // this is a atomised version of $graphObject->asArray()['nameOfFbCategory']->data->name
              try {
                fwrite($retrieved,$ting->id.", handle: ".$ting->name."\n");
              } catch (\Exception $ex) {
                echo "read write error".$ex."\n";
              }
              $assArr[$ting->id] = $ting->name;
            }
            print "******\n";
          }
      //print_r($assArr);
      // array_push($arrOfArr, $assArr);
          $arrOfArr[$filter] = $assArr;
      }
      print_r($arrOfArr);
      $toJSON = fopen($json, 'a');
      fwrite($toJSON,json_encode($arrOfArr));
      fclose($retrieved);
      fclose($toJSON);
    }

    function fbLikes($fbAsArray, $edges, $indices){
      // because each object can have its own category this needs to be a separate function
      // requires a GraphObject->asArray array, an array of valid FB API edges, and returned property names [->getPropertyNames()] as indices
        $retrieved = fopen('termtest.txt', 'a');
        fwrite($retrieved,"inside fbLikes subroutine\n");
        foreach ($edges as $filter){
          if(in_array($filter, $indices)){
            $innerArray = $fbAsArray[$filter]->data; // this is the array which houses every page one has liked
            // print "the contents of ".$filter." are as follows: \n";
            // print_r($innerArray);
            print $filter ." as follows \n******\n";
            foreach ($innerArray as $ting){ // $ting is each element of the array, which are represented as {name:__, id:__} objects
              print "Category: ".$ting->category." , ";
              print $ting->name."\n";
              try {
                fwrite($retrieved,$ting->id.", handle: ".$ting->name."\n");
              } catch (\Exception $ex) {
                echo "read write error".$ex."\n";
              }
                // this is a atomised version of $graphObject->asArray()['nameOfFbCategory']->data->name
            }
            print "******\n";
          }
      }
      fclose($retrieved);
    }



print "<br>end PHP <br>";

?>
<html>
  <head>
    <script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : 'APP ID', // Set YOUR APP ID
      //channelUrl : 'http://hayageek.com/examples/oauth/facebook/oauth-javascript/channel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    FB.Event.subscribe('auth.authResponseChange', function(response) {
      if (response.status === 'connected') {
        document.getElementById("message").innerHTML +=  "<br>Connected to Facebook";
          //SUCCESS
      }
      else if (response.status === 'not_authorized') {
        document.getElementById("message").innerHTML +=  "<br>Failed to Connect";
          //FAILED
      } else {
        document.getElementById("message").innerHTML +=  "<br>Logged Out";
          //UNKNOWN ERROR
      }
    });
  };

  function Login() {
    FB.login(function(response) {
      if (response.authResponse) {
        getUserInfo();
        // location.reload();
      } else {
        console.log('User cancelled login or did not fully authorize.');
      }
    },{scope: 'user_friends, user_interests, user_about_me, user_education_history, user_events, user_groups, user_interests, user_likes, user_work_history'});
    // initial scope :: {scope: 'email,user_photos,user_videos'}
  }

  function getUserInfo() {
      FB.api('/me', function(response) {
        var str="<b>Name</b> : "+response.name+"<br>";
            str +="<b>Link: </b>"+response.link+"<br>";
            str +="<b>Username:</b> "+response.username+"<br>";
            str +="<b>id: </b>"+response.id+"<br>";
            str +="<b>Email:</b> "+response.email+"<br>";
            str +="<input type='button' value='Get Photo' onclick='getPhoto();'/>";
            str +="<input type='button' value='Logout' onclick='Logout();'/>";
            document.getElementById("status").innerHTML=str;
      });
    }

  // Load the SDK asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));

</script>
  </head>
  <body>
  <!-- js login div -->
    <div id="status">
       Click on Below Image to start the demo: <br/>
      <img src="http://hayageek.com/examples/oauth/facebook/oauth-javascript/LoginWithFacebook.png" style="cursor:pointer;" onclick="Login()"/>
    </div>
  </body>

</html>
