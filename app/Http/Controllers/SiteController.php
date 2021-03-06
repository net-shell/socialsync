<?php namespace App\Http\Controllers;

use Auth;
use PayPal;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Services\SocialManager;
use App\Jobs\AwaitAction;
use App\MarketItem;
use App\Post;
use App\Log;
use Exception;

class SiteController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('checkUser');
		$this->social = new SocialManager(app());
	}

	public function getIndex()
	{
		$market = array();
		$providers = Auth::user()->providers;
		$query = MarketItem::whereIn('provider', $providers);
		foreach ($query->get()->groupBy('post_id') as $key => $marketItem) {
			$market[$key] = (new Collection($marketItem))
				->keyBy('action')
				->sortByDesc('updated_at');
		}
		return view('home', compact('market'));
	}

	public function getAction($post = null, $action = null)
	{
		$post = Post::find($post);

		if(!$post) {
			app()->abort(422);
		}

		if($action) {
			// Log action and push to queue
			$log = Log::firstOrNew([
				'reason' => $action,
				'market_item_id' => $post->market()->whereAction($action)->first()->id
			]);

			if(!$log->id) {
				Auth::user()->log()->save($log);
				$job = (new AwaitAction($log))->onQueue('check');
		  	$this->dispatch($job);
			}
			else {
				return ['error' => trans('app.action_duplicate')];
			}
		}

		if(!$post) {
			return ['success' => true];
		}

		$url = !$action ? false
			: $this->social->with($post->provider)->action($action, $post->provider_id);

		if($url === true) {
			return ['success' => true];
		}
		if(!$url || !parse_url($url)) {
			$url = $post->link;
		}
		return ['success' => true, 'redirect' => $url];
	}

	public function getFeed($provider)
	{
		$feed = $this->social->with($provider)->feed();

		$providerUser = Auth::user()->oauth_data()
			->whereProvider($provider)
			->first();

		$scheduled = Auth::user()->posts()
			->where('posted_at', '>', date('Y-m-d H:i:s'))
			->whereProvider($provider)
			->count();

		$providerUser = $providerUser ? $providerUser->user_data : Auth::user();

		return view('feed', compact('feed', 'provider', 'providerUser', 'scheduled'));
	}

}
