@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap-datetimepicker.min.css') }} ">
@endsection
@section('content')
<style>
.switch{position:relative;float: right;vertical-align:top;width:75px;height:30px;padding:3px;margin:0 10px 10px 0;background:linear-gradient(to bottom,#FFF,#FFF 25px);background-image:-webkit-linear-gradient(top,#FFF,#FFF 25px);border-radius:18px;box-shadow:inset 0 -1px white,inset 0 1px 1px rgba(0,0,0,0.05);cursor:pointer;box-sizing:content-box}
.switch-input{position:absolute;top:0;left:0;opacity:0;box-sizing:content-box}
.switch-label{position:relative;display:block;height:inherit;font-size:10px;text-transform:uppercase;background:#ff0000;border-radius:inherit;box-shadow:inset 0 1px 2px rgba(0,0,0,0.12),inset 0 0 2px rgba(0,0,0,0.15);box-sizing:content-box}
.switch-label:before,.switch-label:after{position:absolute;top:50%;margin-top:-.5em;line-height:1;-webkit-transition:inherit;-moz-transition:inherit;-o-transition:inherit;transition:inherit;box-sizing:content-box}
.switch-label:before{content:attr(data-off);right:11px;color:#FFF;text-shadow:0 1px rgba(255,255,255,0.5)}
.switch-label:after{content:attr(data-on);left:11px;color:#FFF;text-shadow:0 1px rgba(0,0,0,0.2);opacity:0}
.switch-input:checked ~ .switch-label{background:#008000;box-shadow:inset 0 1px 2px rgba(0,0,0,0.15),inset 0 0 3px rgba(0,0,0,0.2)}
.switch-input:checked ~ .switch-label:before{opacity:0}
.switch-input:checked ~ .switch-label:after{opacity:1}
.switch-handle{position:absolute;top:4px;left:4px;width:28px;height:28px;background:linear-gradient(to bottom,#FFF 40%,#f0f0f0);background-image:-webkit-linear-gradient(top,#FFF 40%,#f0f0f0);border-radius:100%;box-shadow:1px 1px 5px rgba(0,0,0,0.2)}
.switch-handle:before{content:"";position:absolute;top:50%;left:50%;margin:-6px 0 0 -6px;width:12px;height:12px;background:linear-gradient(to bottom,#FFF,#FFF);background-image:-webkit-linear-gradient(top,#FFF,#FFF);border-radius:6px;box-shadow:inset 0 1px rgba(0,0,0,0.02)}
.switch-input:checked ~ .switch-handle{left:50px;box-shadow:-1px 1px 5px rgba(0,0,0,0.2)}
.switch-label,.switch-handle{transition:All .3s ease;-webkit-transition:All .3s ease;-moz-transition:All .3s ease;-o-transition:All .3s ease}
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">
            Virtualmin Domains Manage Cloud - {{$domain->name}} 

            <label class="switch">
                <input class="switch-input" data-id="{{$domain->id}}" type="checkbox" @if($domain->rocket_loader!='off') {{'checked'}} @endif/>
                <span class="switch-label" data-on="On" data-off="Off"></span> 
                <span class="switch-handle"></span> 
            </label>
            <span style="float:right;">Rocket loader settings :</span>
        </h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-7">
                    <form action="{{ route('virtualmin.domains.managecloud', ['id' => $domain->id]) }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>                            
                            <div class="col-md-3 pd-sm">                                
                                <select name="dns_type" id="dns_type" class="form-control select2">
                                    <option value="">-- Select DNS Type --</option> 
                                    <option value="A" {{ request('dns_type') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="MX" {{ request('dns_type') == 'MX' ? 'selected' : '' }}>MX</option>
                                    <option value="TXT" {{ request('dns_type') == 'TXT' ? 'selected' : '' }}>TXT</option>
                                </select>
                            </div>
                            <!-- <div class="col-md-2 pd-sm">                                
                                <select name="proxied" id="proxied" class="form-control select2">
                                    <option value="">-- Select Proxied --</option> 
                                    <option value="1" {{ request('proxied') == 1 ? 'selected' : '' }}>Enable</option>
                                    <option value="0" {{ request('proxied') == 0 ? 'selected' : '' }}>Disable</option>
                                </select>
                            </div> -->
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('virtualmin.domains.managecloud', ['id' => $domain->id]) }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-5">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#a-dns-create">Create A DNS Record</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mx-dns-create">Create MX DNS Record</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#txt-dns-create">Create TXT DNS Record</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="a-dns-create" class="modal fade in" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create A DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form  method="POST" id="a-dns-create-form">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        {{ html()->label('Content', 'ip_address')->class('form-control-label') }}
                        {{ html()->text('ip_address')->class('form-control')->required() }}
                    </div>
                    <div class="form-group">
                        {{ html()->label('DNS Name', 'name')->class('form-control-label') }}
                        {{ html()->text('name')->class('form-control')->required() }}
                        {{ html()->hidden('Virtual_min_domain_id', $domain->id) }}
                        {{ html()->hidden('dns_type', 'A') }}         
                    </div>
                    <div class="form-group">
                        {{ html()->label('Select DNS Type', 'type')->class('form-control-label') }}
                        <select name="type" id="type" class="form-control select2">
                            <option value="A">A</option>
                            <option value="cname">CNAME</option>
                        </select>
                    </div>
                    <div class="form-group">
                        {{ html()->label('Select Proxied Type', 'proxied')->class('form-control-label') }}
                        <select name="proxied" id="proxied" class="form-control select2">
                            <option value="1">Enable</option>
                            <option value="2">Disable</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary a-dns-save-btn">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="mx-dns-create" class="modal fade in" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create MX DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form  method="POST" id="mx-dns-create-form">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        {{ html()->label('Content', 'ip_address')->class('form-control-label') }}
                        {{ html()->text('ip_address')->class('form-control')->required() }}
                    </div>
                    <div class="form-group">
                        {{ html()->label('DNS Name', 'name')->class('form-control-label') }}
                        {{ html()->text('name')->class('form-control')->required() }}
                        {{ html()->hidden('Virtual_min_domain_id', $domain->id) }}
                        {{ html()->hidden('dns_type', 'MX') }}                    
                    </div>
                    <div class="form-group">
                        {{ html()->label('Select Priority', 'priority')->class('form-control-label') }}
                        <select name="priority" id="priority" class="form-control select2">
                            @for ($i = 0; $i <= 100; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor                            
                        </select>
                    </div>
                    {{ html()->hidden('type', 'MX') }}
                    
                    {{ html()->hidden('proxied', 2) }}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary mx-dns-save-btn">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="txt-dns-create" class="modal fade in" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create TXT DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form  method="POST" id="txt-dns-create-form">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        {{ html()->label('Content', 'ip_address')->class('form-control-label') }}
                        {{ html()->text('ip_address')->class('form-control')->required() }}
                    </div>
                    <div class="form-group">
                        {{ html()->label('DNS Name', 'name')->class('form-control-label') }}
                        {{ html()->text('name')->class('form-control')->required() }}
                        {{ html()->hidden('Virtual_min_domain_id', $domain->id) }}
                        {{ html()->hidden('dns_type', 'TXT') }}   
                        {{ html()->hidden('type', 'TXT') }}
                        {{ html()->hidden('proxied', 2) }}                     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary txt-dns-save-btn">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="dns-records-div" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update A DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px;">
            
            </div>
        </div>
    </div>
</div>

<div id="dns-mx-records-div" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update MX DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px;">
            
            </div>
        </div>
    </div>
</div>

<div id="dns-txt-records-div" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update TXT DNS Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px;">
            
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="virtualmin-domains-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">DNS Name</th>
                            <th width="20%">DNS Type</th>
                            <th width="20%">Priority</th>
                            <th width="20%">Proxied</th>
                            <th width="20%">IPV4</th>
                            <th width="20%">Created Date</th>
                            <th width="10%">Action</th>
                        </tr>
                        @foreach ($domainsDnsRecords as $key => $domain)
                            <tr data-id="{{ $domain->id }}">
                                <td>{{ $domain->id }}</td>
                                <td>{{ $domain->domain_with_dns_name }}</td>
                                <td>{{ $domain->type }}</td>
                                <td>{{ $domain->priority }}</td>
                                <td>@if($domain->proxied==1){{'Enable'}} @else {{'Disable'}} @endif</td>
                                <td>{{ $domain->content }}</td>
                                <td>{{ $domain->created_at }}</td>
                                <td>
                                    @if($domain->dns_type=='A')
                                        <button type="button" title="Edit" data-id="{{$domain->id}}" class="btn btn-xs edit-dns-btn">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </button>
                                    @elseif($domain->dns_type=='MX')
                                        <button type="button" title="Edit" data-id="{{$domain->id}}" class="btn btn-xs edit-dns-mx-btn">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </button>
                                    @elseif($domain->dns_type=='TXT')
                                        <button type="button" title="Edit" data-id="{{$domain->id}}" class="btn btn-xs edit-dns-txt-btn">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </button>
                                    @endif

                                    <a href="javascript:;" class="btn virtualmin_domains_dnsdelete" data-id="{{$domain->id}}"><i class="fa fa-trash" style="color: #808080;"></i></a>

                                    <button type="button" class="btn btn-xs domain-history"
                                        data-id="{{ $domain->id }}" title="Domain History" onclick="listdomainhistory()">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                </td>                                
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $domainsDnsRecords->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 999999999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
</div>

<div id="domain-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Virtualmin Domains DNS History</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="domain-history-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(document).on("click", ".a-dns-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $('#loading-image').show();
        $.ajax({
            url: "{{route('virtualmin.domains.createadns')}}",
            type: "post",
            data: $('#a-dns-create-form').serialize()
        }).done(function(response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                toastr['success']('Domain  Created successfully!!!', 'success');
                location.reload();
            } else if (response.code == '500') {
                $('#loading-image').hide();
                toastr['error'](response.message, 'error');
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click", ".mx-dns-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $('#loading-image').show();
        $.ajax({
            url: "{{route('virtualmin.domains.createadns')}}",
            type: "post",
            data: $('#mx-dns-create-form').serialize()
        }).done(function(response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                toastr['success']('Domain  Created successfully!!!', 'success');
                location.reload();
            } else if (response.code == '500') {
                $('#loading-image').hide();
                toastr['error'](response.message, 'error');
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click", ".txt-dns-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $('#loading-image').show();
        $.ajax({
            url: "{{route('virtualmin.domains.createadns')}}",
            type: "post",
            data: $('#txt-dns-create-form').serialize()
        }).done(function(response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                toastr['success']('Domain  Created successfully!!!', 'success');
                location.reload();
            } else if (response.code == '500') {
                $('#loading-image').hide();
                toastr['error'](response.message, 'error');
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });
                                
    function listdomainhistory(pageNumber = 1) {
        var button = document.querySelector('.btn.btn-xs.domain-history'); // Corrected class name
        var id = button.getAttribute('data-id');

            $.ajax({
                url: '{{ route('virtualmin.domains.dnshistories') }}',
                dataType: "json",
                data: {
                    id: id,
                    page:pageNumber,
                },
                beforeSend: function() {
                $("#loading-image-preview").show();
            }
            }).done(function(response) {
                $('#domain-history-modal-html').empty().html(response.html);
                $('#domain-history-modal').modal('show');
                renderdomainPagination(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {
                $('.loading-image-preview').show();
                console.log(response);
            });
    }

    function renderdomainPagination(response) {
        var paginationContainer = $(".pagination-container-domain");
        var currentPage = response.current_page;
        var totalPages = response.last_page;
        var html = "";
        var maxVisiblePages = 10;

        if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            var startPage = 1;
            var endPage = totalPages;

            if (totalPages > maxVisiblePages) {
            if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - Math.floor(maxVisiblePages / 2);
                endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
            }

            if (startPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(1)'>1</a></li>";
                if (startPage > 2) {
                html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }
            }

            for (var i = startPage; i <= endPage; i++) {
            html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changedomainPage(" + i + ")'>" + i + "</a></li>";
            }
            html += "</ul>";
        }
        paginationContainer.html(html);
     }

    function changedomainPage(pageNumber) {
        listdomainhistory(pageNumber);
    }

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('click', '.virtualmin_domains_dnsdelete', function (event) {
        event.preventDefault();
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this DNS?')){

            $('#loading-image').show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('virtualmin.domains.dnsdelete')}}",
                type: "post",
                data: {'id':id}
            }).done(function(response) {
                if (response.code == '200') {
                    $('#loading-image').hide();
                    toastr['success']('Domain DNS Deleted successfully!!!', 'success');
                    location.reload();
                } else if (response.code == '500') {
                    $('#loading-image').hide();
                    toastr['error'](response.message, 'error');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(errObj) {
                $('#loading-image').hide();
                toastr['error'](errObj.message, 'error');
            });
        }
    });

    $(document).on("click", ".edit-dns-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');

        var $this = $(this);
        $.ajax({
            type: "GET",
            data : {
              id :id
            },
            url: "{{ route('virtualmin.domains.dnsedit') }}"
        }).done(function (data) {
           $("#dns-records-div").find(".modal-body").html(data);
           
           $("#dns-records-div").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on("click", ".edit-dns-mx-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');

        var $this = $(this);
        $.ajax({
            type: "GET",
            data : {
              id :id
            },
            url: "{{ route('virtualmin.domains.dnsedit') }}"
        }).done(function (data) {
           $("#dns-mx-records-div").find(".modal-body").html(data);
           
           $("#dns-mx-records-div").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on("click", ".edit-dns-txt-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');

        var $this = $(this);
        $.ajax({
            type: "GET",
            data : {
              id :id
            },
            url: "{{ route('virtualmin.domains.dnsedit') }}"
        }).done(function (data) {
           $("#dns-txt-records-div").find(".modal-body").html(data);
           
           $("#dns-txt-records-div").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on('click', '.a-dns-update-btn', function (e) {
        e.preventDefault();
        var $this = $(this);
        var $form  = $this.closest("form");
        $('#loading-image').show();
        $.ajax({
            type: "POST",
            data : $form.serialize(),
            url: "{{ route('virtualmin.domains.dnsupdate') }}"
        }).done(function (response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                toastr['success']('Domain DNS Updated successfully!!!', 'success');
                location.reload();
            } else if (response.code == '500') {
                $('#loading-image').hide();
                toastr['error'](response.message, 'error');
            } else {
                toastr['error'](response.message, 'error');
            } 
        }).fail(function (response) {
            console.log(response);
        });
    });

    $('.switch-input').change(function() {
        if ($(this).is(':checked')) {
            var value = 'on';
        } else {
            var value = 'off';
        }

        var $this = $(this);
        var id = $this.data('id');

        $('#loading-image').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data : {'id':id, 'value': value},
            url: "{{ route('virtualmin.domains.domainstatusupdate') }}"
        }).done(function (response) {
            if (response.code == '200') {
                $('#loading-image').hide();
                toastr['success']('Domain Rocket loader settings successfully!!!', 'success');
                //location.reload();
            } else if (response.code == '500') {
                $('#loading-image').hide();
                toastr['error'](response.message, 'error');
            } else {
                toastr['error'](response.message, 'error');
            } 
        }).fail(function (response) {
            console.log(response);
        });
    });
</script>
@endsection