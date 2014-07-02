<?php

require '../library/tw/tmhOAuth.php';
require '../library/tw/tmhUtilities.php';


class Model_Twitter {

	public function followers($tw_id) {

		$table = new Zend_Db_Table("twitter_user");
		$twuser = $table->fetchAll($table->select()->where("id = ".(int)$tw_id))->toArray();
		$twuser = $twuser[0];

		$tmhOAuth = new tmhOAuth(array(
		  'consumer_key'    => $twuser["consumer_key"],
		  'consumer_secret' => $twuser["consumer_secret"],
		));

		$here = tmhUtilities::php_self();

		$tmhOAuth->config['user_token']  = $twuser["user_token"];
		$tmhOAuth->config['user_secret'] = $twuser["user_secret"];

		$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));

		if ($code == 200) {
		
			$resp = json_decode($tmhOAuth->response['response']);
    	$resp->screen_name;
		
			$cursor = $twuser["last_cursor"];
			while($cursor) {
				$url = $tmhOAuth->url('1.1/followers/list');
				$code = $tmhOAuth->request('GET', $url, array(
					"cursor"=>$cursor
				));
				if($code == 200) {
					$response = $tmhOAuth->response;
					$users = json_decode($response["response"]);
					$this->addUsers($users->users, $twuser["id"]);
					$cursor = (int)$users->next_cursor;				
				}else{
					break;
				}
			}
			$table->update(array("last_cursor"=>$cursor), "id = ".$twuser["id"]);
		}
	}

	public function addUsers($users, $twuser_id) {
		$table = new Zend_Db_Table("twitter_followers");
		foreach($users as $user) {
			$user = array(
				"user" => $user->screen_name,
				"location" => $user->location,
				"twitter_user"=>$twuser_id
			);
			try{ $table->insert($user); }catch(Exception $e){}
		}
	}

	public function tweet($message, $lat, $lng) {
	
		$tmhOAuth = new tmhOAuth(array(
		  'consumer_key'    => '39fWjp29UuJkoNxzK1cAg',
		  'consumer_secret' => '7hNmlFPXGbEycerqo8wVcD9V5Q6Ebof7OkKCoBPw',
		));

		$here = tmhUtilities::php_self();

		$tmhOAuth->config['user_token']  = "334266070-LnDtpDLIiRK88aiWBE2RxaxVDNa5nDEphY1dTTKh";
		$tmhOAuth->config['user_secret'] = "iLZtzlTR6BmB40lWryCZyDQ89kOPrYSDNCjIZ8n2Y0Q";

		$code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));

		if ($code == 200) {
		
			$resp = json_decode($tmhOAuth->response['response']);
    		$resp->screen_name;
		
			$url = $tmhOAuth->url('1.1/statuses/update');

			$code = $tmhOAuth->request('POST', $url, array(
				'status' => $message,
				'lat' => $lat,
				'long' => $lng,
				'display_coordinates' =>true,
				'wrap_links' => true
			));
		}
		return false;
		
 	}	

}
