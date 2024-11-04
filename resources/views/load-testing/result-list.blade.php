@extends('layouts.app')
@section('content')
<style>
  .v-scroll{
    overflow-x: auto;
  }  
  .pagination-container {
    /* Assuming you want the pagination at the bottom of the container */
    position: relative;
    position: fixed;
}

.pagination {
    /* Adjust the margin-top if needed */
    margin-top: 90px;
    position: fixed;
}

/* For Bootstrap 4 */
.justify-content-end {
    justify-content: flex-end !important;
}
</style>
@include('partials.loader')

    <div class="container-fluid">
        <div class="row" id="load-test-page">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Load Testing Result</h2>
                <div class="pull-right">
                </div>
            </div>
        </div>
        <div class="row pb-2">
            <form action="{{route('load-testing.result.filter')}}" id="filterForm" method="post">
                @csrf
                <div class="col-md-3 py-2">
                    <strong>Search domain name :</strong>
                    <input type="text" name="domain_name" id="domain_name" placeholder="Search domain name" class="form-control" value="{{isset($inputsData['domain_name']) ? $inputsData['domain_name']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>Search apdex :</strong>
                    <input type="text" name="apdex" id="apdex" placeholder="Search Apdex" class="form-control" value="{{isset($inputsData['apdex']) ? $inputsData['apdex']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>T (Toleration threshold) :</strong>
                    <input type="text" name="toleration_threshold" id="toleration_threshold" placeholder="Search T (Toleration threshold)" class="form-control" value="{{isset($inputsData['toleration_threshold']) ? $inputsData['toleration_threshold']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>F (Frustration threshold) :</strong>
                    <input type="text" name="frustration_threshold" id="frustration_threshold" placeholder="Search F (Frustration threshold)" class="form-control" value="{{isset($inputsData['frustration_threshold']) ? $inputsData['frustration_threshold']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>Label :</strong>
                    <input type="text" name="label" id="label" placeholder="Search Label" class="form-control" value="{{isset($inputsData['label']) ? $inputsData['label']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>Samples :</strong>
                    <input type="text" name="samples" id="samples" placeholder="Search Samples" class="form-control" value="{{isset($inputsData['samples']) ? $inputsData['samples']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>Fail :</strong>
                    <input type="text" name="fail" id="fail" placeholder="Search fail" class="form-control" value="{{isset($inputsData['fail']) ? $inputsData['fail']: ''}}">
                </div>
                <div class="col-md-3 py-2">
                    <strong>Average :</strong>
                    <input type="text" name="average" id="average" placeholder="Search average" class="form-control" value="{{isset($inputsData['average']) ? $inputsData['average']: ''}}">
                </div>
                {{-- <div class="col-md-2 pd-sm">
                    <div class="form-group">
                        <label>Search by Proxy :</label>
                        <select class="form-control select2 multiselect" name="need_proxy"  >
                            <option value="" disabled selected hidden>Search by Proxy</option>
                            <option value="1" <?php  if(@$inputsData['need_proxy'] == '1'){ echo 'selected';} ?>>Yes</option>
                            <option value="0" <?php  if(@$inputsData['need_proxy'] == '0'){ echo 'selected';} ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 pd-sm">
                    <div class="form-group">
                        <label>Search by AWS Moved :</label>
                        <select class="form-control select2 multiselect" name="aws_moved"  >
                            <option value="" disabled selected hidden>Search by AWS Moved</option>
                            <option value="1" <?php  if(@$inputsData['aws_moved'] == '1'){ echo 'selected';} ?>>Yes</option>
                            <option value="0" <?php  if(@$inputsData['aws_moved'] == '0'){ echo 'selected';} ?>>No</option>
                        </select>
                    </div>
                </div> --}}
                <div class="col-md-2 pt-5">
                    <strong></strong>
                    <button type="submit" class="btn btn-image search" onclick="#">
                        <img src="/images/search.png" alt="Search">
                    </button>
                    <a href="{{route('load-testing.result')}}" class="btn btn-image" >
                        <img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                    <input type="hidden" id="download" name="download" value="1">
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="v-scroll">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">Domain</th>
                                <th width="10%">Apdex</th>
                                <th width="6%">T (Toleration threshold)</th>
                                <th width="5%">F (Frustration threshold)</th>
                                <th width="5%">Label</th>
                                <th width="4%">Samples</th>
                                <th width="20%">Fail</th>
                                <th width="5%">Error %</th>
                                <th width="20%">Average</th>
                                <th width="6%">Min</th>
                                <th width="6%">Max</th>
                                <th width="6%">Median</th>
                                <th width="6%">90th pct</th>
                                <th width="6%">95th pct</th>
                                <th width="6%">99th pct</th>
                                <th width="6%">Transactions/s</th>
                                <th width="6%">Received</th>
                                <th width="6%">Sent</th>
                                <th width="6%">Type of error</th>
                                <th width="6%">Number of errors</th>
                                <th width="6%">% in errors</th>
                                <th width="6%">% in all samples</th>
                                <th width="6%">#Samples</th>
                                <th width="6%">#Errors</th>
                                <th width="6%">Error</th>
                                <th width="6%">#Errors</th>
                                <th width="6%">Error</th>
                                <th width="6%">#Errors</th>
                                <th width="6%">Error</th>
                                <th width="6%">#Errors</th>
                                <th width="6%">Error</th>
                                <th width="6%">#Errors</th>
                                <th width="6%">Error</th>
                                <th width="6%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getRecords as $record)
                                <tr>
                                    <td>{{$record->domain_name}}</td>
                                    <td>{{$record->apdex}}</td>
                                    <td>{{$record->toleration_threshold}}</td>
                                    <td>{{$record->frustration_threshold}}</td>
                                    <td>{{$record->label}}</td>
                                    <td>{{$record->samples}}</td>
                                    <td>{{$record->fail}}</td>
                                    <td>{{$record->error}}</td>
                                    <td>{{$record->avg}}</td>
                                    <td>{{$record->min}}</td>
                                    <td>{{$record->max}}</td>
                                    <td>{{$record->median}}</td>
                                    <td>{!! $record['90th_pct'] !!}</td>
                                    <td>{{$record['95th_pct']}}</td>
                                    <td>{{$record['99th_pct']}}</td>
                                    <td>{{$record->transactions}}</td>
                                    <td>{{$record->received}}</td>
                                    <td>{{$record->sent}}</td>
                                    <td>{{$record->type_of_error}}</td>
                                    <td>{{$record->no_of_error}}</td>
                                    <td>{{$record->percentage_of_error}}</td>
                                    <td>{{$record->percentage_in_all_samples}}</td>
                                    <td>{{$record->samples}}</td>
                                    <td>{{$record->errors_1}}</td>
                                    <td>{{$record->error_1}}</td>
                                    <td>{{$record->errors_2}}</td>
                                    <td>{{$record->error_2}}</td>
                                    <td>{{$record->errors_3}}</td>
                                    <td>{{$record->error_3}}</td>
                                    <td>{{$record->errors_4}}</td>
                                    <td>{{$record->error_4}}</td>
                                    <td>{{$record->errors_5}}</td>
                                    <td>{{$record->error_5}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                </div>
                {{ $getRecords->links() }}    
            </div>
            <div class="col-md-12">
                
            </div>
        </div>
    </div>

<script>
    $.ajax({
    url: "{{route('load-testing.result')}}",
    type: 'post',
    data: { filter: 'your_filter_criteria' },
    success: function(response) {
        // Assuming response is an array of merged records
        if (response.length > 0) {
            // Assuming you have a div with id "mergedRecords" to display the results
            $('#mergedRecords').html(response.join(', '));
        } else {
            $('#mergedRecords').html('No records found.');
        }
    },
    error: function(xhr, status, error) {
        console.error('Error fetching records:', error);
    }
});
</script>
@endsection