@extends('layouts.app')

@section('title', 'Redis Jobs')

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
    
    <div class="row m-0">
      <div class="col-lg-12 margin-tb p-2">
        <h2 class="page-heading">Redis Jobs</h2>
        <a href="#" class="btn btn-sm btn-primary add_redis_job">
          Add Redis Job
      </a>
      <br/><br/>
      </div>
      <div class="col-lg-12 margin-tb p-3" >

        <table id="" class="table table-bordered table-striped"  style="width:100%;">
          <thead>
            <th width="15%">ID</th>
            <th width="35%">Name</th>
            <th width="20%" >Type</th>
            <th width="25%" >Action</th>
          </thead>
          <tbody class="redis_jobsTR">

          @foreach($redis_data as $rjData)
			      <tr>
                  <td>
                    <a class="show-product-information text-dark" data-id="{{ $rjData->id }}" href="#">{{ $rjData->id }}</a>
                  </td>
                  <td class="expand-row-msg" data-name="{{$rjData->name}}" data-id="{{$rjData->id}}">
                        {{$rjData->name}}
                  </td>
                  <td class="expand-row-msg" data-name="{{$rjData->type}}" data-id="{{$rjData->id}}">
                    {{$rjData->type}}
                  </td>
                  <td>
                      <a href="javascript::void(0)" class="deleteQue" data-id="{{$rjData->id}}"><i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a> | 
                      <a href="javascript::void(0)" class="clearQ" data-id="{{$rjData->id}}"  data-name="{{$rjData->name}}">Clear Queue</a> | 
                      <a href="javascript::void(0)" class="restartManagement" data-id="{{$rjData->id}}"  data-name="{{$rjData->name}}">Restart Management</a>
                  </td>
                </tr>
              @endforeach()
            </tbody>
          </table>
        </div>
     </div>

      <!-- Modal -->
      <div class="modal fade" id="add_redis_job_modal" tabindex="-1" role="dialog" aria-labelledby=""
      aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content modal-lg">
              <div class="modal-header">
                  <h5 class="modal-title" id="">Add Radis Job</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                    <div class="form-group">
                      <label>Name</label>
                      <input class="form-control" type="text" name="name" id="name" />
                    </div>
                    <div class="form-group">
                      <label>Type</label>
                      <select name="type" id="type" class="form-control">
                          <option value="website">Website</option>
                          <option value="main_queue">main_queue</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <button id="save_radits_btn">Save</button>
                    </div>
              </div>
          </div>
      </div>
  </div>
     @endsection

     @section('scripts')
     <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
     <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
         <script>
              $(document).on('click', '.expand-row-msg', function () {
                var name = $(this).data('name');
                var id = $(this).data('id');
                var full = '.expand-row-msg .show-short-'+name+'-'+id;
                var mini ='.expand-row-msg .show-full-'+name+'-'+id;
                $(full).toggleClass('hidden');
                $(mini).toggleClass('hidden');
              });

              /** infinite loader **/
	var isLoading = false;
	var page = 1;
	$(document).ready(function () {
		$(window).scroll(function() {
			if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
				loadMore();
			}
		});

		function loadMore() {
			if (isLoading)
				return;
			isLoading = true;
			var $loader = $('.infinite-scroll-products-loader');
			page = page + 1;
			$.ajax({
				url: '{{route("redis.jobs.list")}}?&page='+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					//console.log(data);
					$loader.hide();				
					$('.redis_jobsTR').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});
	//End load more functionality
   //START - Purpose : Get data - DEVTASK-20123
  $(document).on('click', '.add_redis_job', function(e) {
    $('#add_redis_job_modal').modal('show');
  });

  $(document).on('click', '#save_radits_btn', function(e) {
    
    e.preventDefault();
    var name = $("#name").val();
    var type = $("#type").val();
    $.ajax({
      type: 'POST',
      headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      },
      url: "{{route('redis.add_radis_job')}}/",
      dataType: "json",
      data: {name: name, type: type},
      beforeSend : function() {
          $("#loading-image").show();
      },
      success: function(response) {
          $("#loading-image").hide();
          toastr["success"](response.message);
          location.reload();
      },
      error: function(response) {
          $("#loading-image").hide();
          toastr['error'](response.message);
      }
    });
  });

  $(document).on('click', '.clearQ', function(e) {
    if(confirm("Are you sure you want to clear Queue?")){
      var id = $(this).data('id');
      var name = $(this).data('name');
      $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('redis.clear_que') }}",
        dataType: "json",
        data: {id: id, name:name},
        beforeSend : function() {
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            toastr["success"](response.message);
            location.reload();
        },
        error: function(response) {
            $("#loading-image").hide();
            toastr['error'](response.message);
        }
      });
    }
  });

  $(document).on('click', '.restartManagement', function(e) {
    if(confirm("Are you sure you want to clear Queue?")){
      var id = $(this).data('id');
      var name = $(this).data('name');
      $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('redis.restart_management')}}/"+id,
        dataType: "json",
        data: {id: id, name:name},
        beforeSend : function() {
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            toastr["success"](response.message);
            location.reload();
        },
        error: function(response) {
            $("#loading-image").hide();
            toastr['error'](response.message);
        }
      });
    }
  });
</script>

    @endsection