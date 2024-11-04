<div class="form-group">
	<label for="{{ $params['name'] }}">{{ $params['title'] }}</label>
	{{ html()->text($params['name'], isset($params['value']) ? $params['value'] : null)->class('form-control')->placeholder($params['placeholder']) }}
</div>