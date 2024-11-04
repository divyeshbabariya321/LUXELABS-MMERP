@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento CSS Variable Logs ({{ $magentoCssVariableJobLogs->total() }})</h2>
        @if($errors->any())
        <div class="row m-2">
        {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
        @endif
        @include('partials.flash_messages', ['extraDiv' => true])
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="mt-3 col-md-12">
                    <form action="{{route('magento-css-variable.logs')}}" method="get" class="search">
                        <div class="col-lg-2">
                            <input class="form-control" type="text" id="search_error" placeholder="Search Error" name="search_error" value="{{ $error ?? '' }}">
                        </div>
                        <div class="col-lg-2">
                            <input class="form-control" type="text" id="search_command" placeholder="Search Command" name="search_command" value="{{ $command ?? '' }}">
                        </div>
                        <div class="col-lg-2">
                            <input class="form-control" type="text" id="search_message" placeholder="Search Message" name="search_message" value="{{ $message ?? '' }}">
                        </div>
                        <div class="col-lg-2">
                            <input class="form-control" type="date" name="date" value="{{ $date ?? '' }}">
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                               <img src="{{ asset('images/search.png') }}" alt="Search">
                           </button>
                           <a href="{{route('magento-css-variable.logs')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                        </div>
                    </form>
                </div>
                <div class="col-12" style="margin-top: 10px;">
                    <div class="pull-right" style="display: flex">
                        <a class="btn btn-secondary" href="{{ route('magento-css-variable.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="magento-css-variable-logs-list">
                        <tr>
                            <th width="3%">ID</th>
                            <th width="10%">Project</th>
                            <th width="10%">Command</th>
                            <th width="10%">Message</th>
                            <th width="10%">Status</th>
                            <th width="10%">Date</th>
                            <th width="10%">CSV Path</th>
                        </tr>
                        @foreach ($magentoCssVariableJobLogs as $key => $magentoCssVariableJobLog)
                            <tr data-id="{{ $magentoCssVariableJobLog->id }}">
                                <td>{{ $magentoCssVariableJobLog->id }}</td>
                                <td class="expand-row" style="word-break: break-all">     
                                    {{ $magentoCssVariableJobLog->formatted_magento_css_variable_id }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariableJobLog->command) > 30 ? substr($magentoCssVariableJobLog->command, 0, 30).'...' :  $magentoCssVariableJobLog->command }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariableJobLog->command }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($magentoCssVariableJobLog->message) > 30 ? substr($magentoCssVariableJobLog->message, 0, 30).'...' :  $magentoCssVariableJobLog->message }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $magentoCssVariableJobLog->message }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariableJobLog->status }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $magentoCssVariableJobLog->created_at }}
                                </td>
                                <td>
                                @if( $magentoCssVariableJobLog->csv_file_path)
                                <a class="btn btn-info mx-3" href="{{ route('admin.download.file', ['id' =>$magentoCssVariableJobLog->id]) }}" title="CSV Downlaod"><i class="fa fa-download"></i></a>
                                @endif
                            </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $magentoCssVariableJobLogs->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection