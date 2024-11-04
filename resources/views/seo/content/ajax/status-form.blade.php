<form action="{{ $actionUrl }}" autocomplete="off" id="statusForm"> @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="label" class="form-control" value="{{ $status->label ?? '' }}">
        </div>
    
        <div class="col-md-6">
            <label class="form-label">Type</label>
            <select name="type" class="form-control">
                <option value="">-- SELECT --</option>
                <option value="seo_approval" {{ ($status->type ?? '') == 'seo_approval' ? 'selected' : '' }}>SEO Team</option>
                <option value="publish" {{ ($status->type ?? '') == 'publish' ? 'selected' : '' }}>Publish Team</option>
            </select>
        </div>
    </div>
</form>