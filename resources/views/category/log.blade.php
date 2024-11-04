@extends('layouts.app')

@section('large_content')
    <style type="text/css">
        .btn-secondary {
            margin-top : 2px;
        }
        .category-mov-btn
        {
            min-height : 60px;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Categories Log ({{$logRequestCount}}) <button id="pushCategoryBtn" style="float: right">Push Category</button></h2>   
        </div>
        <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Request</th>
                        <th>Url</th>
                        <th>Status code</th>
                        <th>Time taken</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logRequest as $log)
                    @php
                     $logdata = json_decode($log->request, true);   
                    @endphp
                        <tr>
                            <td>
                                {{ $log->created_at }}
                            </td>
                            <td>
                                @foreach($logdata as $key => $value)
                                @if(is_array($value))
                                    <strong>{{ $key }}:</strong>
                                    <ul>
                                        @foreach($value as $innerKey => $innerValue)
                                            <li><strong>{{ $innerKey }}:</strong> {{ $innerValue }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p><strong>{{ $key }}:</strong> {{ $value }}</p>
                                @endif
                            @endforeach
                            </td>
                            <td>
                                {{ $log->url }}
                            </td>
                            <td>
                                {{ $log->status_code }}
                            </td>
                            <td>
                                {{ $log->time_taken }}
                            </td>
                           
                    @endforeach
                </tbody>
            </table>
            <div class="text-center">
                <div class="text-center">
                    {!! $logRequest->links() !!}
                </div>
            </div>
          </div>
        </div>
    </div>
@endsection
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

@section('scripts')
<script>
    document.getElementById('pushCategoryBtn').addEventListener('click', function() {
        // Make an HTTP request when the button is clicked
        fetch('/push-category-in-live', {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Show a message or handle the response as needed
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet"/> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection