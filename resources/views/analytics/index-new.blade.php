@extends('layouts.app')
@section('title', 'Analytics Data')
@section('content')

<style>
    /** only for the body of the table. */
    table.table tbody td {
        padding:5px;
    }
    .chat-msg{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
    }
    .chat-msg:hover{
        white-space: normal;
        overflow: visible;
    }
</style>
<!-- COUPON Rule Edit Modal -->
<div class="modal fade" id="fullUrlModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="couponModalLabel">Full URL</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div><strong><p class="url"></p></strong></div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<div class="row ">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">New Google Analytics</h2>
    </div>
</div>
<form action="" method="get">   
    
        <div class="col-md-1">
           
            <div class="form-group">
                <select name="website" class="form-control">
                    <option value=""> website</option>
                    @foreach ($website_list as $item)
                        <option value="{{ $item['id'] }}" {{ request('website') == $item['id'] ? 'selected' : null }}> {{ $item['website'] }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
            
            <div class="form-group">
                <select name="browser" class="form-control">
                    <option value=""> Browser</option>
                    @foreach ($browsers as $item)
                        <option value="{{ $item }}" {{ request('browser') == $item ? 'selected' : null }}> {{ $item }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
           
            <div class="form-group">
                <select name="os" class="form-control">
                    <option value=""> Os</option>
                    @foreach ($os as $item)
                        <option value="{{ $item }}" {{ request('os') == $item ? 'selected' : null }}> {{ $item }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
            
            <div class="form-group">
                <select name="country" class="form-control">
                    <option value=""> Country</option>
                    @foreach ($countries as $item)
                        <option value="{{ $item }}" {{ request('country') == $item ? 'selected' : null }}> {{ $item }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
           
            <div class="form-group">
                <select name="user_type" class="form-control">
                    <option value=""> User Type</option>
                    @foreach ($user_types as $item)
                        <option value="{{ $item }}" {{ request('user_type') == $item ? 'selected' : null }}> {{ $item }} </option>
                    @endforeach
                </select>
            </div>
        </div>
     
        <div class="col-md-2">
            
            <div class="form-group">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
        </div>
        <div class="col-md-2">
            
            <div class="form-group">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
        </div>
        <div class="form-group col-md-1 pt-2" style="">
           
            <button class="btn btn-image search"><img src="https://erp.theluxuryunlimited.com/images/search.png" alt="Search" style="cursor: nwse-resize; width: 0px;"></button>
            <a href="{{ url('/display/analytics-data') }}" class="btn btn-image">
                <i class="fa fa-history" aria-hidden="true"></i>
            </a>
        </div>
</form>

<div class="mt-1">
    <button class="btn btn-default show-history"> Show history </button>
</div>



@include('partials.flash_messages')
@include('analytics.history')



<div class="col-md-12">
    <table class="table table-bordered table-striped" style="table-layout: fixed;">
        <thead>
            <tr>
                <th class="chat-msg" style="word-break: break-all; width:3.5%">Website</th>
                <th class="chat-msg" style="word-break: break-all; width:3.5%">Browser</th>
                <th class="chat-msg" style="word-break: break-all; width:3.5%">OS</th>
                <th class="chat-msg" style="word-break: break-all; width:3.5%">Country</th>
                <th class="chat-msg" style="word-break: break-all; width:4.5%">Iso Code</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">User Type</th>
                <th class="chat-msg" style="word-break: break-all; width:6%">Page</th>
                <th class="chat-msg" style="word-break: break-all; width:4.5%">Avg Time</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Page Views</th>
                <th class="chat-msg" style="word-break: break-all; width:6%">U. Page Views</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Exist Rate</th>
                <th class="chat-msg" style="word-break: break-all; width:4%">Entrances</th>
                <th class="chat-msg" style="word-break: break-all; width:6%">Entrance Rate</th>
                <th class="chat-msg" style="word-break: break-all; width:3%">Age</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Gender</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Session</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Exception</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Log</th>
                <th class="chat-msg" style="word-break: break-all; width:5%">Device</th>
                <th class="chat-msg" style="word-break: break-all; width:4%">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($google_analytics_data as $data)
                <tr>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->website) > 4)
                            @php
                                $dns = $data->website;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->browser) > 4)
                            @php
                                $dns = $data->browser;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->os) > 4)
                            @php
                                $dns = $data->os;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->country) > 4)
                            @php
                                $dns = $data->country;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->iso_code) > 4)
                            @php
                                $dns = $data->iso_code;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->user_type) > 4)
                            @php
                                $dns = $data->user_type;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->page) > 4)
                            @php
                                $dns = $data->page;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen(round($data->avg_time_page, 4)) > 4)
                            @php
                                $dns = round($data->avg_time_page, 4);
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->page_view) > 4)
                            @php
                                $dns = $data->page_view;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->unique_page_views) > 4)
                            @php
                                $dns = $data->unique_page_views;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->exit_rate) > 4)
                            @php
                                $dns = $data->exit_rate;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->entrances) > 4)
                            @php
                                $dns = $data->entrances;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->entrance_rate) > 4)
                            @php
                                $dns = $data->entrance_rate;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->age) > 4)
                            @php
                                $dns = $data->age;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->gender) > 4)
                            @php
                                $dns = $data->gender;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->session) > 4)
                            @php
                                $dns = $data->session;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->exception) > 4)
                            @php
                                $dns = $data->exception;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->log) > 4)
                            @php
                                $dns = $data->log;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->device) > 4)
                            @php
                                $dns = $data->device;
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                    
                    <td  style="word-break: break-all"; class="expand-row">
                        @if(strlen($data->created_at->format('Y-m-d')) > 4)
                            @php
                                $dns = $data->created_at->format('Y-m-d');
                                $dns = str_replace('"[', '', $dns);
                                $dns = str_replace(']"', '', $dns);
                            @endphp

                            <div class="td-mini-container brand-supplier-mini-{{ $data->id }}">
                                {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                            </div>
                            <div class="td-full-container hidden brand-supplier-full-{{ $data->id }}">
                                {{ $dns }}
                            </div>
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        @if (count($google_analytics_data) === 0)
            <div style="text-align:center"><h3 style="color: gray">No Data Availble</h3></div>    
        @endif
    <div>
        <tr>{{ $google_analytics_data->links() }}</tr>
    </div>
</div>
<div class="row mt-5">
    <div class="container-fluid">
        
    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">

    $(document).on('click', '.expand-row', function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on("click",".show-history",function(e) {
        e.preventDefault();
        $.ajax({
            url: '/display/analytics-history',
            type: 'POST',
            data : { _token: "{{ csrf_token() }}"},
            dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function(result){
                $("#loading-image").hide();
                if(result.code == 200) {
                    var t = '';
                    $.each(result.data,function(k,v) {
                        t += `<tr><td>`+v.id+`</td>`;
                        t += `<td>`+v.website+`</td>`;
                        t += `<td data-title = "`+v.title+`">`+v.title+`</td>`;
                        t += `<td>`+v.description+`</td>`;
                        t += `<td>`+v.created_at+`</td></tr>`;
                    });
                    if( t == '' ){
                        t = '<tr><td colspan="5" class="text-center">No data found</td></tr>';
                    }
                }
                $("#category-history-modal").find(".show-list-records").html(t);
                $("#category-history-modal").modal("show");
            },
            error: function (){
                $("#loading-image").hide();
            }
        });
    });

    $(document).ready(function() {
    });
    function displayFullPath(ele){
        let fullpath = $(ele).attr('data-path');
        $('.url').text(fullpath);
        $('#fullUrlModal').modal('show');
    }
    

    $(document).on('change','.category-history-filter',function(){
        var value = $(this).val();
        if (value == 'error') {
            $('#category-history-modal').find('td[data-title="error"]').closest('tr').show();
            $('#category-history-modal').find('td[data-title="success"]').closest('tr').hide();
        }
        if (value == 'success') {
            $('#category-history-modal').find('td[data-title="success"]').closest('tr').show();
            $('#category-history-modal').find('td[data-title="error"]').closest('tr').hide();
        }
        if (!value) {
            $('#category-history-modal').find('td[data-title="success"]').closest('tr').show();
            $('#category-history-modal').find('td[data-title="error"]').closest('tr').show();
        }
    })

    $('#category-history-modal').on('hidden.bs.modal', function () {
        $('#category-history-modal').find('.category-history-filter').val('');
    })
</script>
@endsection
