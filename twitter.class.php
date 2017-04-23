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

	/**
	* Get all third party accounts
	*/
	public function get_third_party_accounts() {
		// Get accounts from db or just hardcode them here
		$accounts = ['Sydney_Uni'];
		return $accounts;
	}

	public function get_rate_limit() {
		$connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $this->access_token, $this->access_token_secret);
			$response = $connection->get("application/rate_limit_status", ['resources' => 'statuses']);
			if ($connection->getLastHttpCode() == 200) {
				return $response->resources->statuses->{'/statuses/user_timeline'};
			} else {
				// Error
			}
	}


	/**
	* Get the the id of the most recent tweet created by a user
	* Use the return to tell the lookup where to stop
	*/
	public function get_last_tweet_id($user_id) {

			try {
				$dbh = new PDO("mysql:host=127.0.0.1;dbname=twitter;charset=utf8", "root", "root");
				$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  

				$sql = "SELECT * FROM `tweets` WHERE `screen_name` = :screen_name ORDER BY `id` DESC LIMIT 1";
				$sth = $dbh->prepare($sql);
				$sth->bindParam(":screen_name", $user_id);
				$sth->execute();
				$result = $sth->fetch();
				return $result['id'];

			} catch (PDOException $e) {
				print_r($e);
			}

	}

	/**
	* Get tweets from db
	*/
	public function get_tweets() {

			try {
				$dbh = new PDO("mysql:host=127.0.0.1;dbname=twitter;charset=utf8", "root", "root");
				$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  

				$sql = "SELECT * FROM `tweets`";
				$sth = $dbh->prepare($sql);
				$sth->execute();
				$results = $sth->fetchAll();
				return $results;

			} catch (PDOException $e) {
				print_r($e);
			}
	}


	/**
	* Request new tweets from supplied users and store the result in db
	*/
	public function collect_tweets($user_ids = NULL) {

		// Get all account ids if not supplied
		if (!isset($user_ids)) {
			$user_ids = $this->get_third_party_accounts();
		}

		// does the connection need to be estalished every time?
		$connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $this->access_token, $this->access_token_secret);

		// Get tweets for each account
		foreach ($user_ids as $user_id) {

			$request_params = array(
				'count' 			=> 100, //max 3200
				'exclude_replies' 	=> true,
				'screen_name' 		=> $user_id,
				);
			$since_id = $this->get_last_tweet_id($user_id);
			if (isset($since_id) && !empty($since_id)) {
				$request_params['since_id'] = $since_id;		
			}

			$response = $connection->get("statuses/user_timeline", $request_params);
			if ($connection->getLastHttpCode() != 200) {
				// Error
				print_r($response);
				echo 'Twitter API response error - '.$response;
				continue;
			}


			/*
			Sample code - Insert tweet into db if it doesn't exist already
			*/

			// print_r($response);
			try {

				$dbh = new PDO("mysql:host=127.0.0.1;dbname=twitter;charset=utf8", "root", "root");
				$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  

				$sql = "INSERT IGNORE INTO `tweets`
						(`id`, `screen_name`, `name`, `text`, `date_posted`, `lookup_date`)
						VALUES (:id, :screen_name, :name, :tweet_text, :date_posted, CURRENT_DATE)";
				$sth = $dbh->prepare($sql);

				print_r($response);

				foreach($response as $status) {
					$sth->bindParam(":id", $status->id_str, PDO::PARAM_INT);
					$sth->bindParam(":screen_name", $status->user->screen_name, PDO::PARAM_STR);
					$sth->bindParam(":name", $status->user->name, PDO::PARAM_STR);
					$sth->bindParam(":tweet_text", $status->text, PDO::PARAM_STR);
					$sth->bindParam(":date_posted", $status->created_at);
					$sth->execute();
				}

			} catch (PDOException $e) {
				print_r($e);
			}

		}

	}


}

?>