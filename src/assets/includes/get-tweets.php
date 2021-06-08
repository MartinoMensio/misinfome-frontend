<?php 
session_start();
require_once("twitteroauth/twitteroauth.php"); //Path to twitteroauth library

#$search = "%23kmiou OR %23ou_ceremonies";
$search = "#qualichain";
$notweets = 500;
$consumerkey = "5weqjAJSM0tNA00fIsbmf8WrU";
$consumersecret = "HSIlHQAnbD3Hwn0SB6OQGmJteXZ6yMizlqkz0HzgabioKebVvi";
$accesstoken = "246184280-inrMk5ioiaNzCkrJ5x9l5K4zriz98Qs9jBUEs27z";
$accesstokensecret = "5lu9tedruwUlQpnGyRB27Coo5e3gJvAS5gffUy9rQsDEG";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $tconnection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $tconnection;
}
 
$tconnection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
$search = str_replace("#", "%23", $search);
$tweets = $tconnection->get("https://api.twitter.com/1.1/search/tweets.json?q=".$search."&count=".$notweets."&lang=en");
 
//Check twitter response for errors.
if ( isset( $tweets->errors[0]->code )) {
    // If errors exist, print the first error for a simple notification.
    echo "Error encountered: ".$tweets->errors[0]->message." Response code:" .$tweets->errors[0]->code;
} else {
    // No errors exist. Write tweets to json/txt file.
    $file = "qc-tweets.txt";
    //$file = "cache/tweets.txt";
    $fh = fopen($file, 'w') or die("can't open file");
    fwrite($fh, json_encode($tweets));
    fclose($fh);
}
?>