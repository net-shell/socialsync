<?php namespace App\Http\Controllers;

use Socialize;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\QueryException;

use Exception;
use ErrorException;
use App\OAuthData;
use App\User;

class AuthController extends Controller {

	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
		$this->middleware('guest', ['except' => ['getLogout', 'getSocial', 'getCallback', 'getDetach']]);
	}

	private function socialite()
	{
		Socialize::extend('linkedin', function($app) {
			$config = $app['config']['services.linkedin'];
			return Socialize::buildProvider('App\Services\LinkedInProvider', $config);
		});
	}

	public function getIndex() {
		return view('login');
	}

	public function postIndex(Request $request)
	{
		$credentials = $request->only(['email', 'password']);
		if(count($credentials) == count(array_filter($credentials))) {
			try {
				$attempt = $this->auth->attempt($credentials);
			}
			catch(ErrorException $e) { }
		}
		return redirect()->intended('/');
	}

	public function getLogout()
	{
		$this->auth->logout();
		return redirect('/');
	}

	public function getDetach($provider = 'facebook')
	{
		return [
			'success' => $this->auth->user()->oauth_data()
				->whereProvider($provider)
				->delete()
		];
	}

	public function getSocial($provider = 'facebook')
	{
		$this->socialite();
		$service = Socialize::with($provider);
		$scopes = config("services.$provider.scopes");
		//dd($scopes);
		if($scopes && count($scopes)) {
			$service = $service->scopes($scopes);
		}
		return $service->redirect();
	}

	public function getCallback($provider = 'facebook')
	{
		try
		{
			$this->socialite();
			$user = Socialize::with($provider)->user();

			// Get or create the user data record
			$provider_id = $user->id;
			$record = OAuthData::firstOrCreate(compact('provider', 'provider_id'));
			$record->token = $user->token;
			$record->user_data = json_encode($user);

			$userModel = null;

			// Check if this provider is new
			if(!$record->user_id) {
				if($this->auth->check()) {
					// Associate authenticated user with this account
					$record->user_id = $this->auth->id();
				}
				else {
					// Create new user, assign
					$userModel = User::firstOrCreate(['email' => $user->email]);
					$userModel->name = $user->name;
					$userModel->save();
					$record->user_id = $userModel->id;
				}
			}
			// The provided acount is already linked with a user
			else {
				// Find the linked user
				$userModel = User::find($record->user_id);
				// Check if the account's been linked to another user
				if($this->auth->check() && $record->user_id != $this->auth->id()) {
					// Create new record
					$record = $record->replicate(['id', 'user_id']);
					$record->user_id = $this->auth->id();
				}
			}

			if(!$this->auth->check()) {
				$this->auth->login($userModel);
			}

			if(!$this->auth->user()->email && $user->email) {
				try {
					$this->auth->user()->fill(['email' => $user->email])->save();
				}
				catch(QueryException $e) {}
			}

			$record->save();
		}
		catch(Exception $e) { }
		return redirect('/');
	}
}
