<?php 
namespace App\Http\Controllers;

use App\Services\SlackService;
use Illuminate\Http\Request;

class ChannelsController extends Controller
{

	protected $slack_service;

	function __construct()
	{
		$this->slack_service = new SlackService('426023253824.427625182693', '172e22aad3ad6ca8441e226ba0c2bcf3');
	}

	public function index()
	{
		$user = auth()->user();
		return View('home');
	}

	public function create(Request $request)
	{
		$this->validate($request,['name'=>'required']);
		$user = auth()->user();
		$ch  =$this->slack_service->create_channel($request->name, $user->latest_token->access_token);
		
		if($ch->ok == false)
			return redirect()->back()->with(['error'=>'Some Error Happned: ' . $ch->error]);
		
		return back()->with(['status' => 'Channel Created Succesfully']);
	}
}