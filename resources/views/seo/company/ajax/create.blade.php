<div class="container-fluid">
    <div class="mt-3">
        <div class="">
            <form action="{{ route('seo.company.store') }}" method="POST" autocomplete="off" id="companyForm"> @csrf
                <div class="row">
                    <div class="col-md-6 pl-0">
                        <label class="form-label">Type</label>
                        <select name="type_id" class="form-control" required data-msg-required="Please select type.">
                            <option value="">-- SELECT --</option>
                            @foreach ($companyTypes as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
               
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">Website</label>
                        <select name="website_id" class="form-control" required data-msg-required="Please select website.">
                            <option value="">-- SELECT --</option>
                            @foreach ($webistes as $item)
                                <option value="{{ $item->id }}">{{ $item->website }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">DA</label>
                        <input type="text" name="da" class="form-control" required data-msg-required="Please enter DA.">
                    </div>
                
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">PA</label>
                        <input type="text" name="pa" class="form-control" required data-msg-required="Please enter PA.">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">SS</label>
                        <input type="text" name="ss" class="form-control" required data-msg-required="Please enter SS.">
                    </div>
                
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">Username & Password</label>
                        <select name="email_address_id" class="form-control" required data-msg-required="Please select email & password.">
                            <option value="">-- SELECT --</option>
                            @foreach ($emailAddresses as $item)
                                <option value="{{ $item->id }}">{{ 'username:' . $item->username . ', password' . $item->password }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">Live link</label>
                        <input type="text" name="live_link" class="form-control" required data-msg-required="Please enter live link.">
                    </div>
                
                    @php
                        $statusArr = [
                            'pending',
                            'approved',
                            'rejected',
                        ];
                    @endphp
                    <div class="form-group col-md-6 pl-0">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required data-msg-required="Please select status.">
                            @foreach ($statusArr as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>