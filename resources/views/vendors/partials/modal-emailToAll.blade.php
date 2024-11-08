<div id="emailToAllModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Email to Multiple Suppliers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('vendors.email.send.bulk') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="vendor_ids" value="">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <strong>Vendors</strong>
                        <select class="form-control select-multiple" name="vendors[]" multiple>                            

                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>From Mail</strong>
                        <select class="form-control" name="from_mail">
                        <?php $emailAddressArr = getEmailAddress(); ?>
                          @foreach ($emailAddressArr as $emailAddress)
                            <option value="{{ $emailAddress->id }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group text-right">
                        <a class="add-cc mr-3" href="#">Cc</a>
                        <a class="add-bcc" href="#">Bcc</a>
                    </div>

                    <div id="cc-label" class="form-group" style="display:none;">
                        <strong class="mr-3">Cc</strong>
                        <a href="#" class="add-cc">+</a>
                    </div>

                    <div id="cc-list" class="form-group">

                    </div>

                    <div id="bcc-label" class="form-group" style="display:none;">
                        <strong class="mr-3">Bcc</strong>
                        <a href="#" class="add-bcc">+</a>
                    </div>

                    <div id="bcc-list" class="form-group">

                    </div>

                    <div class="form-group">
                        <input type="checkbox" name="not_received" id="notReceived">
                        <label for="notReceived">Send to all who haven't received an email</label>
                    </div>

                    <div class="form-group">
                        <input type="checkbox" name="received" id="received">
                        <label for="received">Send to all who haven't replied to an email</label>
                    </div>

                    <div class="form-group">
                        <strong>Subject *</strong>
                        <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
                    </div>

                    <div class="form-group">
                        <strong>Message *</strong>
                        <textarea name="message" class="form-control" rows="8" cols="80" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="form-group">
                        <strong>Files</strong>
                        <input type="file" name="file[]" value="" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>
