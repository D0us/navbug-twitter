<?php

require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter {

	// Set twitter api credentials here
	private $CONSUMER_KEY = 'BN1KASFmawuldI44iFP6AQ';
	private $CONSUMER_SECRET = '7pDjgU7JlAcdrOGsm24LM8XI6zDqcFqQw6A5cs2sI';
	private $access_token = '2237875400-0Cvk12XJcDr4SixJeIm80ovmUUxgovRufySuYjN';
	private $access_token_secret = 'iwlQHpmuV8OLNhDWjjMXlLm193GtLwrOSqiHeVO6Ldgwi';
 

	function __construct() {

	}

	function __destruct() {

	}

	/*
	Get all third party accounts
	*/
	public function get_third_party_accounts() {
		// Get accounts from db or just hardcode them here
		$accounts = ['BillGates'];
		return $accounts;

	}

	public function check_last_lookup($user_ids = NULL) {
		return true;
		}

	public function get_tweets($user_ids = NULL) {

		// Get all account ids ifs not supplied
		if (!isset($user_ids)) {
			$user_ids = $this->get_third_party_accounts();
			// print_r($user_ids);
		}

		// does the connection need to be estalished every time?
		$connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $this->access_token, $this->access_token_secret);

		// Get tweets for each account
		foreach ($user_ids as $user_id) {

			$request_params = array(
				'count' 			=> 25,
				'exclude_replies' 	=> true,
				'user_id' 			=> $user_id
				);

			$statuses = $connection->get("statuses/home_timeline", $request_params);

			if ($connection->getLastHttpCode() != 200) {
				// Error
				continue;
			}

			// print_r($statuses);

			/*
			Sample code - Insert tweet into db if it doesn't exist already
			*/
			try {

				$db = new PDO("mysql:host=127.0.0.1;dbname=twitter;charset=utf8", "admin", "1234");

				$sql = "INSERT INTO `tweets`
						(`id`, `screen_name`, `name`, `text`, `date_posted`, `lookup_date`)
						VALUES (:id, :screen_name, :name, :tweet_text, :date_posted, CURRENT_DATE)";

				$stmt = $pdo->prepare($sql);
				echo 'test';
				foreach($statuses as $status) {
					echo '1';
					$stmt->execute(array('id' => $status['id'], 'screen_name' => $status['screen_name'], 'name' => $status['name'], 'tweet_text' => $status['text'], 'date_posted' => $status['created_at']));
				}

			} catch (PDOException $e) {
				print_r($e);
			}




		}


	}

	public function connect_test() {
			try {

				$db = new PDO("mysql:host=localhost;port=3306;dbname=twitter;charset=utf8", "test", "");

				// $sql = "INSERT INTO `tweets`
				// 		(`id`, `screen_name`, `name`, `text`, `date_posted`, `lookup_date`)
				// 		VALUES (:id, :screen_name, :name, :tweet_text, :date_posted, CURRENT_DATE)";

				// $stmt = $pdo->prepare($sql);
				// echo 'test';
				// foreach($statuses as $status) {
				// 	echo '1';
				// 	$stmt->execute(array('id' => $status['id'], 'screen_name' => $status['screen_name'], 'name' => $status['name'], 'tweet_text' => $status['text'], 'date_posted' => $status['created_at']));
				// }

			} catch (PDOException $e) {
				print_r($e);
			}


	}


}


// Show errors for dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$twitter = new Twitter();
// $twitter->get_tweets();
$twitter->connect_test();




?>