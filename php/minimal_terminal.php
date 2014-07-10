<?php
require 'vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

FacebookSession::setDefaultApplication('id','secret' );

// Use one of the helper classes to get a FacebookSession object.
//   FacebookRedirectLoginHelper
//   FacebookCanvasLoginHelper
//   FacebookJavaScriptLoginHelper
// or create a FacebookSession with a valid access token:
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
  $response = $request->execute();
  $graphObject = $response->getGraphObject();
  var_dump($graphObject);
  print_r($graphObject->getPropertyNames());

} catch (FacebookRequestException $e) {
  // The Graph API returned an error
} catch (\Exception $e) {
  // Some other error occurred
}

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

?>