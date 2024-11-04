@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="{{ mix('webpack-dist/flow/style.css') }} ">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
	  #Collector { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	  #Collector cross { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
	  #Collector li span { position: absolute; margin-left: -1.3em; }
	  /*start toggle button*/
 .cmn-toggle {
    position: absolute;
    margin-left: -9999px;
    visibility: hidden;
  }
  .cmn-toggle + label {
    display: block;
    position: relative;
    cursor: pointer;
    outline: none;
    user-select: none;
  }
input.cmn-toggle-round + label {
    padding: 2px;
    width: 40px;
    height: 20px;
    background-color: #dddddd;
    border-radius: 60px;
}
  input.cmn-toggle-round + label:before,
  input.cmn-toggle-round + label:after {
    display: block;
    position: absolute;
    top: 1px;
    left: 1px;
    bottom: 1px;
    content: "";
  }
  input.cmn-toggle-round + label:before {
    right: 1px;
    background-color: #f1f1f1;
    border-radius: 60px;
    transition: background 0.4s;
  }
  input.cmn-toggle-round + label:after {
    width:18px;
    background-color: #fff;
    border-radius: 100%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transition: margin 0.4s;
  }
  input.cmn-toggle-round:checked + label:before {
    background-color: #333333;
  }
  input.cmn-toggle-round:checked + label:after {
    margin-left: 20px;
  }
  /*end toggle button*/
    </style>
@endsection

@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Flows Condition</h2>
                </div>
            </div>
        </div>
    </div>
	<div class="row mb-3">
		<div class="mt-3 col-md-12">
		    <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">S.No. </th>
                            <th scope="col" class="text-center">Flow </th>
                            <th scope="col" class="text-center">Condition Name</th>
                            <th scope="col" class="text-center">Message</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center task_queue_list">
                    	@php $i=1 @endphp
                        @foreach($flowconditions as $key => $flowcondition)
                            <tr class="worker_row_{{$flowcondition->id}}">
								<td>{{ $i++ }}</td>
								<td>{{ $flowcondition['flow_name'] }}</td>
								<td>{{ $flowcondition['condition_name'] }}</td>
								<td>{{ $flowcondition['message'] }}</td>
								@if($flowcondition['status'] == 1)
                                <td>
									<div class="switch">
					                    <input id="cmn-toggle-{{$flowcondition->id}}" data-id="{{$flowcondition->id}}" class="cmn-toggle cmn-toggle-round" type="checkbox" checked="">
					                    <label for="cmn-toggle-{{$flowcondition->id}}"></label>
					                </div>
								</td>
								@else
								<td>
									<div class="switch">
					                    <input id="cmn-toggle-{{$flowcondition->id}}" data-id="{{$flowcondition->id}}" class="cmn-toggle cmn-toggle-round" type="checkbox">
					                    <label for="cmn-toggle-{{$flowcondition->id}}"></label>
					                </div>
								</td>
								@endif
							</tr>
                        @endforeach
                    </tbody>
            </table>
        </div>
    </div>
	

@endsection

@section('scripts')

<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
});

jQuery(document).ready(function($)
{


});

$(document).on('click','.cmn-toggle',function()
{
    let id = $(this).attr('data-id');
    var showstatus = "";
    $.ajax({
        url: "{{route('flow.conditionliststatus')}}",
        data: {
            id: id
        },
        success: function (response) {
            if(response.status == 1)
            { 
                showstatus = "activated";
            }
            if(response.status == 0)
            {
                showstatus = "deactivated";
            }
            toastr["success"]("Condition "+showstatus+" successfully", "Message")
        },
        error: function (error) {
            toastr["error"](error.responseJSON.message, "Message")
            
        }
    });
});

</script>
@endsection