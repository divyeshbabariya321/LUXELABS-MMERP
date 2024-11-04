<style>
    .card-remove {
        position: absolute;
        top: 0px;
        right: 0px;
    }

</style>
@php
$auth = auth()->user();
@endphp
<div class="container-fluid">
    <div class="mt-3">
        <div class="">
            <form action="{{ route('seo.content.store')}}" method="POST" id="seoForm" autocomplete="off"> @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Select Website </label>
                            <select name="website_id" class="form-control" required data-msg-required="Please select website.">
                                <option value="">-- Select --</option>
                                @foreach ($storeWebsites as $item)
                                <option value="{{ $item->id }}">{{ $item->website }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Word count</label>
                            <input type="number" name="word_count" class="form-control" required data-msg-required="Please enter word count.">
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Suggestion</label>
                            <input type="text" name="suggestion" class="form-control" required data-msg-required="Please enter suggestion.">
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-control" required data-msg-required="Please select user.">
                                <option value="">-- Select --</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" required data-msg-required="Please enter price.">
                        </div>
                    </div>
                    @if($auth->hasRole([config('site.role.admin')]))
                    <div class="col-md-3">
                        <div class="form-check form-check-inline mt-4">
                            <input class="form-check-input" type="checkbox" name="is_price_approved" id="priceApprove" value="1">
                            <label class="form-check-label" for="priceApprove">Approve</label>
                        </div>
                    </div>
                    @endif
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Document link</label>
                            <input type="text" name="google_doc_link" class="form-control" required data-msg-required="Please enter document link.">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Seo Status</label>
                            <select name="seo_process_status_id" class="form-control" required data-msg-required="Please select seo status.">
                                <option value="">-- Select --</option>
                                @foreach ($seoProcessStatus as $item)
                                    @if($item->type == 'seo_approval')
                                        <option value="{{ $item->id }}">{{ $item->label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Live status link</label>
                            <input type="text" name="live_status_link" class="form-control" required data-msg-required="Please enter live status link.">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Publish date</label>
                            <input type="datetime-local" name="published_at" class="form-control" required data-msg-required="Please select publish date.">
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="planned">Planned</option>
                                <option value="admin_approve">Admin approve</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <section class="keywordSec col-12">
                        <div class="col-12 mb-1">
                            <div class="row">
                                <label class="form-label">Keywords <button type="button" class="badge btn addKeywordBtn"><i class="fa fa-plus" aria-hidden="true"></i>
                                    </button></label>
                            </div>
                        </div>

                        <div class="table kwRowSec">
                            <div class="kwRow">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-md-11">
                                            <input type="text" name="keyword[]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-remove">
                                        <button type="button" class="mt-1 btn btn-sm kwRmBtn">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Seo team status modal -->
<div class="modal fade" id="kwSeoModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seo team status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary btnSave">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Publish team status modal -->
<div class="modal fade" id="kwPublishModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Publish team status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($seoProcessStatus as $item)
                @if($item->type == 'publish')
                <div class="row mt-2">
                    <div class="col-md-8 statusSec">
                        <label class="form-label">{{ $item->label }}</label>
                        <input type="text" class="form-control" data-id="{{ $item->id }}">
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
