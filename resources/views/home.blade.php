@extends('app')

@section('js')
$(function() {
	$('.market-item .action a').click(function(e) {
		e.preventDefault();
		$.get($(this).attr('href'), function(r) {
			if(r.error) {
				alert(r.error)
			} else {
				if(r.redirect) window.open(r.redirect) 
			}
		});
	})
})
@stop

@section('pageClass', 'focus')

@section('content')

<div class="header">
	<h2>
		{{ $user->points }}
		@lang('app.points')
		<a class="btn btn-primary buy" data-toggle="modal" data-target="#buyModal">
			<i class="fa fa-dollar"></i>
			@lang('app.buy_points')
		</a>

		<a class="btn btn-success buy" data-toggle="modal" data-target="#postModal">
			<i class="fa fa-plus"></i>
			@lang('app.post_new')
		</a>
	</h2>
</div>

@if(count($market))
<div>
	<div class="market row">
		@foreach($market as $id => $actions)
		<div class="col-sm-6 col-md-4 col-lg-3">
			<div class="market-item tile-block {{ $actions->first()->user->id == $user->id ? 'tile-aqua' : 'tile-gray' }}">
				<div class="tile-header">
					<i class="{{ config('br.actions.'. $actions->first()->provider . '.icon') }}"></i>
					@if($actions->first()->user->id == $user->id)
					<a href="#" data-toggle="modal" data-target="#{{ $actions->first()->provider }}BoostModal" data-post-id="{{ $actions->first()->post->id }}">
						<i class="fa fa-fw fa-pencil-square"></i>
					</a>
					@endif
					<a href="{{ action('UserController@show', $actions->first()->user->id) }}">
						{{ $actions->first()->user->name }}
						<span>{{ $actions->first()->updated_at->diffForHumans() }}</span>
					</a>
				</div>

				@if($actions->first()->post->image)
				<div class="tile-content image" style="background-image: url({{ $actions->first()->post->image }})">
				@else
				<div class="tile-content">
				@endif
					<p class="longtext">{{ strlen($actions->first()->post->text) ? $actions->first()->post->text : $actions->first()->post->link }}</p>
				</div>
				@unless($actions->first()->user->id == $user->id)
				<div class="tile-footer">
					@foreach(config('br.actions.' . $actions->first()->provider) as $action => $settings)
					<?php if(!is_array($settings)) continue; ?>
					@if(isset($actions[$action]))
					<div class="action">
						<a target="_blank" href="{{ action('SiteController@getAction', [$actions->first()->post_id, $action]) }}" class="btn btn-primary btn-block btn-icon icon-left" title="@lang("app.actions.$action")">
							<i class="fa fa-{{ $settings['icon'] }}"></i>
							@lang("app.actions.$action")
							<strong class="label label-success pull-right">
								+{{ $actions[$action]->reward }}
							</strong>
						</a>
					</div>
					@endif
					@endforeach
				</div>
				@endunless
			</div>
		</div>
		@endforeach
	</div>
</div>
@else
	@lang('app.market_empty')
@endif

{{-- Boost edit modals --}}
@foreach(config('br.actions') as $provider => $actions)
@unless(isset($actions['authOnly']))
@include('modals.boost', compact('provider'))
@endunless
@endforeach

@include('modals.buy')
@include('layout.boost-js')

@stop
