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
            {{ html()->hidden('dns_type', 'TXT') }}  
            {{ html()->hidden('type', $VirtualminDomainDnsRecords->type) }}
            {{ html()->hidden('proxied', 2) }}       
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary a-dns-update-btn">Update</button>
        </div>
    </div>
</form>
