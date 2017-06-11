<?php

require_once('twitter.class.php');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


// Create twitter object
$twitter = new Twitter();

// Collect and store tweets in db
$twitter->collect_tweets();

// Provide own accounts
// $accounts = ['aldousscales', 'Sydney_Uni'];
// $twitter->collect_tweets($accounts);	

// Get tweets from db 
// $tweets = $twitter->get_tweets();
// print_r($tweets);

// Get remaining requests for 15 min window
// $rates = $twitter->get_rate_limit();
// print_r($rates);

?>