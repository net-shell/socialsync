@extends('app')

@section('content')

<h1>
	{{ $profile->name }}
	<small>{{ $profile->points }}</small>
</h1>
<div class="row">
	@foreach($profile->posts()->latest()->get() as $post)
	<div class="col-sm-4">
		<div class="market-item tile-block tile-gray">
			<div class="tile-header">
				<a href="{{ $post->link }}">
					<i class="{{ config("br.actions.$post->provider.icon") }}"></i>
					{{ $post->text }}
				</a>
			</div>
			@if($post->image)
			<div class="tile-content image" style="background-image: url({{ $post->image }});"></div>
			@endif
			<div class="tile-footer clearfix">
				<div class="btn-group pull-right">
					@if(false)
					@foreach(config("br.actions.$post->provider") as $action => $settings)
					<?php if(!is_array($settings)) continue; ?>
					<a target="_blank" href="{{ action('SiteController@getAction', ['post' => $post->id, 'action' => $action]) }}" class="btn btn-default" title="@lang("app.providers.$action")">
						<i class="fa fa-{{ $settings['icon'] }}"></i>
					</a>
					@endforeach
					@endif
				</div>
				<div class="time">
					<i class="fa fa-clock-o"></i>
					<small>{{ $post->created_at->diffForHumans() }}</small>
				</div>
			</div>
		</div>
	</div>
	@endforeach
</div>
@stop
