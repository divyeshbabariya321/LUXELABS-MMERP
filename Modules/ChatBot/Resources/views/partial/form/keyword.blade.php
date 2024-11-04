<div class="form-group">
	<label for="Keyword">Entity</label>
	{{ html()->text('keyword', isset($keyword) ? $keyword : '')->class('form-control')->placeholder('Enter entity name') }}
</div>