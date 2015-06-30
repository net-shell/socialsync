<?php namespace App\Http\Controllers\API;

use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use App\Post;

class PostController extends Controller {

	private $auth;
	private $request;

	public function __construct(Guard $auth, Request $request) {
		$this->middleware('auth');
		$this->auth = $auth;
		$this->request = $request;
	}

	public function index()
	{
		$input = $this->request->only(['provider', 'start', 'end']);
		return Post::whereUserId($this->auth->id())
			->whereProvider($input['provider'])
			->whereBetween('posted_at', array($input['start'], $input['end']))
			->latest()->get(['id', 'text as title', 'posted_at as start', 'image', 'link', 'provider']);
	}

	public function show($post) {
		return $post->load(['market', 'categories']);
	}

	public function store()
	{
		$id = $this->request->input('id');
		$input = $this->request->only(['provider', 'text', 'link', 'image']);
		$categories = $this->request->input('categories');
		$inputSchedule = $this->request->only(['schedule_date', 'schedule_time']);

		if(!$input['link']) {
			throw new Exception('The "link" argument is missing', 1);
		}

		$post = (int)$id
			? Post::find($id)->fill($input)
			: new Post($input);

		if($inputSchedule['schedule_date']) {
			$time = explode(':', $inputSchedule['schedule_time']);
			$date = new Carbon($inputSchedule['schedule_date']);
			$date->setTime($time[0], $time[1]);
			if($date->timestamp > time()) {
				$post->posted_at = $date;
			}
		}
		else {
			$post->posted_at = new Carbon;
		}

		$post->user_id = $this->auth->id();

		$post->save();

		if(count($categories)) {
			$post->categories()->sync($categories);
		}
		else {
			$post->categories()->detach();
		}

		$post->provider_id = $post->id;
		$post->save();
	}

	public function destroy($id)
	{

	}
}
