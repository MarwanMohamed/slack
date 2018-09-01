<?php 
namespace App\Http\Controllers;

use App\Services\SlackService;
use Illuminate\Http\Request;

class SlackController extends Controller
{

	protected $slack_service;

	function __construct()
	{
		$this->slack_service = new SlackService('426023253824.427625182693', '172e22aad3ad6ca8441e226ba0c2bcf3');
	}

	function redirect()
	{
		if(auth()->check())
			return redirect(url('/home'));

		$url =  $this->slack_service->generate_authorization_url("admin,channels:write", url("slack/callback"));
		return redirect($url);
	}

	public function callback(Request $request)
	{
		if(auth()->check())
			return redirect(url('/home'));

		if(!$request->filled('code')){
			return redirect(url('/'));
		}
		
		$request->code;
		$tokenData 	= $this->slack_service->exchange_authcode_with_token($request->code,url("slack/callback"));
		$user 		= $this->slack_service->create_or_retrive_user($tokenData);
		//login user with object and redirecet
		auth()->login($user);
		return redirect('/home');
	}
}