<?php

// http://stackoverflow.com/questions/18687276/how-to-display-href-in-curl-using-php

require("taggrab.class.php");
$stag="<title"; // start tag
$etag="</title>"; // end tag
//$url = "http://www.prote.in/en/feed/2013/02/myo1";

$div = "<div class=\"item_body";
$divE = "</div>";

// unsuccessful pairings 
// s: "item-body", e: "</div>"

// tags of interest, in order of appearance
// <title></title>
// <div class="item-body"></div>
$file = 'ff-check.txt';
$urlrun = file($file);
	foreach ($urlrun as $url) {

		$tspider = new tagSpider();
	    $tspider->fetchPage($url); // if getting stuff from a page
		$date = preg_replace('(http://www.p.*feed)','',$url);
	    $retrieved = fopen('faux_sql.txt', 'a');
		$missed = fopen('log.txt', 'a');
	    $title = $tspider->parse_array($stag, $etag);
	    $body = $tspider->parse_array($div, $divE);
	    if(!$body){
	    	fwrite ($missed, "body content not detected for ".$url." using combination of ".$div.$divE."\n");
	    	$err = $tspider->printPage();
	    	fwrite ($missed, $url." threw ".$err."\n");
	    } else {
			fwrite($retrieved, "0~0~".$date."~".$date."~'".$body[0]."'~'".$title[0]."'~'none'~'publish'~'open'~'open'~'none'~'".$title[0]."'~'none'~'none'~".$date."~".$date."~'none'~0~'http://'~0~post~'none'~0 |@|\n");
		}
		fclose($retrieved);
		fclose($missed);

	}
    //print ("for the URL dated ".$date);

?>