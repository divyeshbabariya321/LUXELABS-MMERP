<div id="vendor-flowchart-header-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Flow charts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            @csrf
                            <div class="row">
                                <div class="col-12 pb-3">
                                    <select class="form-control col-md-6 mr-3" name="fc_vendor_id" id="fc_vendor_id">
                                        <option value="">Select Vendor</option>
                                        @if(isset($vendorFlowcharts) && $vendorFlowcharts !== NULL)
                                            @foreach ($vendorFlowcharts as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-secondary btn-vendor-search-flowchart" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12 show-vendor-search-flowchart-list" id=""></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.modals.vendor-action-modal')