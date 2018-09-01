<?php
namespace App\Services;

use App\User;
use App\UserToken;

class SlackService
{

	protected $client_id;
	protected $client_secret;
	//configure service with slack cerdentials 
	public function __construct($client_id, $client_secret)
	{
		$this->client_id 		= $client_id;
		$this->client_secret 	= $client_secret;
	}

	public function generate_authorization_url($scope, $redirect_uri)
	{
		$params = ['redirect_uri','scope'];
		
		$url = "https://slack.com/oauth/authorize?client_id=".$this->client_id."&";
		foreach ($params as $param) {
			$url .= $param .'='. $$param.'&';
		}
		return trim($url, '&');
	}

	public function exchange_authcode_with_token($code, $redirect_uri)
	{
		$params = ['redirect_uri','code'];
		
		$url = "https://slack.com/api/oauth.access?client_id=" . $this->client_id."&client_secret=" . $this->client_secret.'&';
		foreach ($params as $param) {
			$url .= $param.'='.$$param.'&';
		}
		$url = trim($url,'&');
		$data = file_get_contents($url);
		return json_decode($data);
	}

	public function create_or_retrive_user($token_object)
	{
		$user = User::where('slack_user_id', $token_object->user_id)->first();
		if($user){
			//craete new access token record and return user object form db
			$this->createTokenForUser($token_object,$user->id);
			return $user;
		}
		$user = $this->createUserFromToken($token_object);
		$this->createTokenForUser($token_object, $user->id);
		return $user;
	}

	private function createTokenForUser($token_object,$user_id)
	{
		$token = new UserToken();
		$token->access_token 	= $token_object->access_token;
		$token->scope 			= $token_object->scope;
		$token->user_id 		= $user_id;
		$token->save();
		return $token;
	}

	private function createUserFromToken($token_object)
	{
		$user = new User();
		$user->email = 'slack-user-' . $token_object->user_id .'@mycoolapp.com';
		$user->password = "mydummypassword";
		$user->slack_user_id = $token_object->user_id ;
		$user->save();
		return $user;
	}

	public function create_channel($name, $token)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => urldecode("https://slack.com/api/channels.create"),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_POSTFIELDS => ['name'=>$name],
		  CURLOPT_HTTPHEADER => array("accept: application/json",
		  		"Authorization: Bearer ".$token,
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		return json_decode($response);
	}
}