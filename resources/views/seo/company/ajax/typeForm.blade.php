<form action="{{ $submitUrl }}" id="companyForm" autocomplete="off"> @csrf
    <div class="row">
        <div class="form-group col-md-8 mt-3">
            <label class="form-label">Company Type</label>
            <input type="text" name="name" class="form-control" required data-msg-required="Please enter type." value="{{ $seoCompany->name ?? '' }}">
        </div>
    </div>
</form>