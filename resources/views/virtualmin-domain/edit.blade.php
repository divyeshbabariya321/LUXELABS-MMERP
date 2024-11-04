<form action="<?php echo route('virtualmin.domains.dnsupdate'); ?>">
    <input type="hidden" name="id" value="{{ $VirtualminDomainDnsRecords->id }}">
    @csrf
    @method('POST')
    <div class="modal-body">
        <div class="form-group">
            {{ html()->label('Content', 'ip_address')->class('form-control-label') }}
            {{ html()->text('ip_address', $VirtualminDomainDnsRecords->content)->class('form-control')->required() }}
        </div>
        <div class="form-group">
            {{ html()->label('DNS Name', 'name')->class('form-control-label') }}
            {{ html()->text('name', $VirtualminDomainDnsRecords->name)->class('form-control')->required() }}
            {{ html()->hidden('Virtual_min_domain_id', $VirtualminDomainDnsRecords->Virtual_min_domain_id) }}
            {{ html()->hidden('dns_type', 'A') }}         
        </div>
        <div class="form-group">
            {{ html()->label('Select DNS Type', 'type')->class('form-control-label') }}
            <select name="type" id="type" class="form-control select2">
                <option value="A" @if($VirtualminDomainDnsRecords->type=='A') {{'selected'}} @endif>A</option>
                <option value="cname" @if($VirtualminDomainDnsRecords->type=='cname') {{'selected'}} @endif>CNAME</option>
            </select>
        </div>
        <div class="form-group">
            {{ html()->label('Select Proxied Type', 'proxied')->class('form-control-label') }}
            <select name="proxied" id="proxied" class="form-control select2">
                <option value="1" @if($VirtualminDomainDnsRecords->proxied==1) {{'selected'}} @endif>Enable</option>
                <option value="2" @if($VirtualminDomainDnsRecords->proxied==0) {{'selected'}} @endif>Disable</option>
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary a-dns-update-btn">Update</button>
        </div>
    </div>
</form>
