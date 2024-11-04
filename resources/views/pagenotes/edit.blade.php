<form action="<?php echo route('updatePageNote'); ?>">
    <input type="hidden" name="id" value="{{ $pageNotes->id }}">
    @csrf
    <div class="form-group">
        <label for="note">Notes:</label>
        <textarea class="form-control" name="note" id="note">{{ $pageNotes->note }}</textarea>
    </div>
    <div class="form-group">
        <label for="category_id">Category:</label>
        {{ html()->select('category_id', ['' => '-- select --'] + $category, $pageNotes->category_id)->class('form-control')->id('category_id') }}
    </div>
    <button type="button" class="btn btn-secondary ml-3 update-user-notes">Update</button>
</form>
