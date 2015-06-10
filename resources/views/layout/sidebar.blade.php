<ul id="main-menu" class="main-menu">
	<li class="{{ Request::is('/') ? 'active' : '' }}">
		<a href="{{ url('/') }}">
			<i class="fa fa-th"></i>
			@lang('app.home')
		</a>
	</li>

	@if($user)

	@foreach(config('adsocial.actions') as $provider => $settings)
		@if(in_array($provider, $user->providers))
		<li class="{{ Request::is("feed/$provider", "feed/$provider/*") ? 'active' : '' }}">
			<a href="{{ action('HomeController@getFeed', $provider) }}">
				<i class="{{ $settings['icon'] }}"></i>
				@lang("app.providers.$provider")
			</a>
		</li>
		@endif
	@endforeach

	@foreach(config('adsocial.actions') as $provider => $settings)
		@if(!in_array($provider, $user->providers))
		<li class="{{ Request::is("feed/$provider", "feed/$provider/*") ? 'active' : '' }}">
			<a href="{{ action('AuthController@getSocial', $provider) }}">
				<i class="fa fa-plus"></i>
				<i class="{{ $settings['icon'] }}"></i>
				Connect @lang("app.providers.$provider")
			</a>
		</li>
		@endif
	@endforeach

	<li class="has-sub opened {{ Request::is('me', 'me/*') ? 'active' : '' }}">
		<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
			<i class="fa fa-user"></i>
			{{ $user->name }}
		</a>
		<ul>
			<li class="{{ Request::is('me') ? 'active' : '' }}">
				<a href="{{ action('UserController@edit') }}">
					<i class="fa fa-pencil"></i>
					@lang('app.profile')
				</a>
			</li>
			<li>
				<a href="{{ action('AuthController@getLogout') }}">
					<i class="fa fa-sign-out"></i>
					@lang('app.logout')
				</a>
			</li>
		</ul>
	</li>
	@endif
</ul>