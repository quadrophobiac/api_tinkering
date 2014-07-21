<?php
ini_set('error_reporting', E_ALL);
session_start();
print_r($_SESSION);
echo "<br>";
print_r($_COOKIE);
echo "<br>";
/*
WORKING!!!!!!
 */
date_default_timezone_set('Europe/London');
require 'vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

FacebookSession::setDefaultApplication('APP ID','APP SECRET' );


try  {
	$seeker = new FacebookJavaScriptLoginHelper();
  // JS SDK stores a reference variable in the cookie
  // PHP sdk can exchange this token for a proper session access token
  // ergo session variables are necessary
	$session = $seeker->getSession();
} catch( FacebookRequestException $ex ) {
  echo "facebook error = ".$ex;
} catch( Exception $ex ) {
  // When validation fails or other local issues
  echo "other fucking error".$ex;
}
if ($session) {
  //echo print_r($session);
  try {
    $request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' );
    $response = $request->execute();
    $graphObject = $response->getGraphObject();
    //var_dump($graphObject);
    print_r($graphObject->getPropertyNames());
  } catch (FacebookRequestException $e) {
    // The Graph API returned an error
  } catch (\Exception $e) {
    // Some other error occurred, that \ is important apparently
  }
} else {
  echo "async isn't working";
}
// Get the GraphUser object for the current user:

// 1st Error thrown DateTime::__construct(): It is not safe to rely on the system's timezone settings. 
// You are *required* to use the date.timezone setting or the date_default_timezone_set() function.
// Remedied via http://stackoverflow.com/questions/16765158/date-it-is-not-safe-to-rely-on-the-systems-timezone-settings-in-codeigniter
// and  sudo service apache2 restart
$retrieved = fopen('fb.txt', 'a');
// try {
// 	$request = new FacebookRequest( $session, 'GET', '/me?fields=id,name,books.name,education,family,favorite_athletes.name,favorite_teams,events.location,groups.name,inspirational_people,interests.name,interested_in,likes.name,work' );
// 	$response = $request->execute();
// 	$graphObject = $response->getGraphObject();
// 	//var_dump($graphObject);
// 	print_r($graphObject->getPropertyNames());
// } catch (FacebookRequestException $e) {
//   // The Graph API returned an error
// } catch (\Exception $e) {
//   // Some other error occurred, that \ is important apparently
// }
try {
	fwrite($retrieved,"WORDUP");
} catch (\Exception $e) {
  echo "read write error".$ex;
}
fclose($retrieved);
?>
<html>
<body>
<div id="fb-root"></div>
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
    },{scope: 'email,user_photos,user_videos'});
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
  function getPhoto() {
      FB.api('/me/picture?type=normal', function(response) {
        var str="<br/><b>Pic</b> : <img src='"+response.data.url+"'/>";
        document.getElementById("status").innerHTML+=str;
    });
  } function Logout() {
      FB.logout(function(){document.location.reload();});
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
<div align="center">
<h2>Facebook OAuth Javascript Demo</h2>

<div id="status">
   Click on Below Image to start the demo: <br/>
  <img src="http://hayageek.com/examples/oauth/facebook/oauth-javascript/LoginWithFacebook.png" style="cursor:pointer;" onclick="Login()"/>
</div>

<br/><br/><br/><br/><br/>

<div id="message">
  Logs:<br/>
</div>

</div>
</body>
</html>