<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="postModalLabel">@lang('app.post_new')</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal validate dontSubmit" action="{{ action('API\PostController@store') }}" method="POST">
					<input type="hidden" name="id" value="">
					<div class="form-group">
						<label class="col-sm-3 control-label">@lang('app.provider')</label>
						<div class="col-sm-4">
							<select class="form-control" name="provider">
								@foreach($user->providers as $provider)
								@unless(config("br.actions.$provider.readOnly"))
								<option value="{{ $provider }}">@lang("app.providers.$provider")</option>
								@endunless
								@endforeach
							</select>
						</div>

						<div class="col-sm-5">
							<div class="date-and-time">
								<input type="text" name="schedule_date" class="form-control datepicker" data-start-date="{{ date('m/d/Y') }}" placeholder="@lang('app.post_schedule')">
								<input type="text" name="schedule_time" class="form-control timepicker" data-template="dropdown" data-show-meridian="false" data-minute-step="5" placeholder="00:00" />
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">@lang('app.categories')</label>
						<div class="col-sm-9">
							<select name="categories[]" class="select2" multiple>
								@foreach($categories as $category)
								<option value="{{ $category->id }}" >{{ $category->name }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">@lang('app.post_text')</label>
						<div class="col-sm-9">
							<textarea name="text" class="form-control" rows="6" data-validate="required"></textarea>
							<p>
								<span id="charcount">0</span>
								@lang('app.characters')
							</p>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">@lang('app.post_link')</label>
						<div class="col-sm-9">
							<input type="url" name="link" class="form-control" data-validate="required,url" required>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">@lang('app.post_image')</label>
						<div class="col-sm-9">
							<input type="url" name="image" class="form-control" data-validate="url">
						</div>
					</div>

					<div style="display: none">
						<input type="submit" />
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					{{ trans('app.cancel') }}
				</button>
				<button type="button" class="btn submit btn-primary">
					{{ trans('app.save') }}
				</button>
			</div>
		</div>
	</div>
</div>
