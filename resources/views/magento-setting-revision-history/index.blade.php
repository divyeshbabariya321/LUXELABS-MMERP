@extends('layouts.app')

@section('content')
@php
    $magentoSettingRevisionHistoryModel = new App\Models\MagentoSettingRevisionHistory();
@endphp
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento Setting Revision Histories ({{ $magentoSettingRevisionHistories->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('magento-setting-revision-history.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="date" name="date" value="{{ request()->get('date') }}">
                            </div>
                            <div class="col-md-2 pd-sm">
                                <select id="status" class="form-control h-100" name="status">
                                    <option value="">--Select Status--</option>
                                    @foreach ($magentoSettingRevisionHistoryModel::$status as $key => $status)
                                    <option value="{{$key}}" @if(request()->get('status') != '' && request()->get('status') == $key) selected @endif>{{$status}}</option>
                                    @endforeach
                                </select>		
                            </div>
                            <div class="col-md-2 pd-sm">
                                <select id="active" class="form-control h-100" name="active">
                                    <option value="">--Select Active--</option>
                                    @foreach ($magentoSettingRevisionHistoryModel::$active as $key => $active)
                                    <option value="{{$key}}" @if(request()->get('active') != '' && request()->get('active') == $key) selected @endif>{{$active}}</option>
                                    @endforeach
                                </select>		
                            </div>
                         
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('magento-setting-revision-history.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">Setting</th>
                            <th width="12%">Date</th>
                            <th width="5%">Status</th>
                            <th width="10%">Log</th>
                            <th width="10%">Config revision</th>
                            <th width="5%">Active</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($magentoSettingRevisionHistories as $key => $magentoSettingRevisionHistory)
                            <tr data-id="{{ $magentoSettingRevisionHistory->id }}">
                                <td>{{ $magentoSettingRevisionHistory->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoSettingRevisionHistory->setting) > 15 ? substr($magentoSettingRevisionHistory->setting, 0, 15).'...' :  $magentoSettingRevisionHistory->setting }}</a>
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoSettingRevisionHistory->setting }}
                                    </span>
                                </td>
                                <td style="word-break: break-all">{{ $magentoSettingRevisionHistory->date }}</td>
                                <td>
                                    <span class="badge {{ $magentoSettingRevisionHistory->status == $magentoSettingRevisionHistoryModel::SUCCESSFUL ? "badge-success" : "badge-danger"}}">{{ $magentoSettingRevisionHistoryModel::$status[$magentoSettingRevisionHistory->status] }}</span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoSettingRevisionHistory->log) > 15 ? substr($magentoSettingRevisionHistory->log, 0, 15).'...' :  $magentoSettingRevisionHistory->log }}</a>
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoSettingRevisionHistory->log }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoSettingRevisionHistory->config_revision) > 15 ? substr($magentoSettingRevisionHistory->config_revision, 0, 15).'...' :  $magentoSettingRevisionHistory->config_revision }}</a>
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoSettingRevisionHistory->config_revision }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $magentoSettingRevisionHistory->active == $magentoSettingRevisionHistoryModel::ACTIVE ? "badge-success" : "badge-danger"}}">{{ $magentoSettingRevisionHistoryModel::$active[$magentoSettingRevisionHistory->active] }}</span>
                                </td>
                                <td class="Website-task"title="">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$magentoSettingRevisionHistory->id}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>
                            </tr>
                            
                            <tr class="action-btn-tr-{{$magentoSettingRevisionHistory->id}} d-none">
                                <td>Action</td>
                                <td id="magento_setting_revision_history_action"  colspan="7" >
                                    <button type="button" class="btn btn-xs show-server-uptimes" title="Action colums" data-id="{{$magentoSettingRevisionHistory->id}}" data-type="developer">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                    {!! $magentoSettingRevisionHistories->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '.show-server-uptimes', function () {
            $(this).parent().parent().prev().find('.td-mini-container').toggleClass('hidden');
            $(this).parent().parent().prev().find('.td-full-container').toggleClass('hidden');
        });
    });

    function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass('d-none')
    }
</script>
@endsection