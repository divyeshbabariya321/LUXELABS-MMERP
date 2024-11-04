 @extends('layouts.app')

@section('title', 'Vendor Info')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@section('large_content')

    
    @include('partials.flash_messages')

    <div class="row">
      <div class="col-lg-12 margin-tb mb-3">
        <h2 class="page-heading">Vendor CV </h2>
        <div class="pull-left ">
          <form class="form-inline" action="{{ route('vendor.cv.search') }}" method="GET">
            <div class="form-group  my-3">
              <input name="vendoe_id" type="text" class="form-control"
                     value="{{ old('vendoe_id') ?? '' }}"
                     placeholder="Vendor Id">
            </div> &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="first_name" type="text" class="form-control"
                     value="{{ old('first_name') ?? '' }}"
                     placeholder="First Name">
            </div>&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="second_name" type="text" class="form-control"
                     value="{{ old('second_name') ?? '' }}"
                     placeholder="Second Name">
            </div>&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="email" type="text" class="form-control"
                     value="{{ old('email') ?? '' }}"
                     placeholder="Email">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="mobile" type="text" class="form-control"
                     value="{{ old('mobile') ?? '' }}"
                     placeholder="Mobile">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="expected_salary_in_usd" type="text" class="form-control"
                     value="{{ old('expected_salary_in_usd') ?? '' }}"
                     placeholder="Expected Salary">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
          </form>
        </div>
      </div>
    </div>
    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="5%">Vendor ID</th>
            <th width="5%">Mr/Miss</th>
            <th width="10%">First Name</th>
            <th width="10%">Second Name</th>
            <th width="10%">Email</th>
            <th width="10%">Mobile</th>
            <th width="10%">Expected Salary</th>
            <th width="10%">Work Hour</th>
            <th width="10%">Time Zone</th>
            <th width="10%">Start Day</th>
            <th width="10%">End Day</th>
            <th width="10%">Career Objective</th>
            <th width="10%">Work Experience</th>
            <th width="10%">Education</th>
            <th width="10%">Address</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($resumes as $resume)
            <tr>
                <td>{{ $resume->id }}</td>
                <td>{{$resume->vendor_id}}</td>
                <td>{{$resume->pre_name}}</td>
                <td>{{$resume->first_name}}</td>
                <td>{{$resume->second_name}}</td>
                <td>{{$resume->email}}</td>
                <td>{{$resume->mobile }}</td>
                <td>{{$resume->expected_salary_in_usd}}</td>
                <td>{{$resume->preferred_working_hours }}</td>
                <td>{{$resume->time_zone}}</td>
                <td>{{$resume->start_day}}</td>
                <td>{{$resume->end_day}}</td>
                <td class="expand-row table-hover-cell" style="word-break: break-all;">
                <span class="td-mini-container">
                  {{ strlen($resume->career_objective) > 10 ? substr($resume->career_objective, 0, 10) : $resume->career_objective }}
                </span>

                    <span class="td-full-container hidden">
                  {{ $resume->career_objective }}
                </span>
                </td>
                <td><a href="#" class="see_work_experience" data-id="{{ $resume->id }}">See</a></td>
                <td><a href="#" class="education" data-id="{{ $resume->id }}">See</a></td>
                <td><a href="#" class="address" data-id="{{ $resume->id }}">See</a></td>
              
              
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $resumes->appends(Request::except('page'))->links() !!}

    

    <div id="see_work_experience_model" class="modal fade" role="dialog">
        <div class="modal-dialog  modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Work Experience</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                      <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                          <thead>
                            
                          </thead>
                  
                          <tbody id="workExpTbody">
                          </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>

        </div>
      </div>
    </div>


    <div id="education_details_model" class="modal fade" role="dialog">
      <div class="modal-dialog  modal-lg">
          <!-- Modal content-->
          <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title">Educational Qualifications</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <div class="table-responsive mt-3">
                      <table class="table table-bordered">
                        <thead>
                          
                        </thead>
                
                        <tbody id="eductionTbody">
                        </tbody>
                      </table>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
          </div>

      </div>
  </div>
</div>

<div id="address_model" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Address</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="table-responsive mt-3">
                  <table class="table table-bordered">
                    <thead>
                      
                    </thead>
            
                    <tbody id="addressTbody">
                    </tbody>
                  </table>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
      </div>

  </div>
</div>
</div>

   

@endsection

@section('scripts')
  <script type="text/javascript">
      $('#paymentShowModal').on('show.bs.modal', function (event) {
          var modal = $(this)
          var button = $(event.relatedTarget)
          var payment = button.data('payment')
          var status = payment.status ? 'Paid' : 'Pending';
          var currency = button.data('currency');
          var html = '<div class="row">' +
              '<div class="col-12">Currency : '+currency+'</div>' +
              '<div class="col-6">Payment Date : '+payment.payment_date+'</div>' +
              '<div class="col-6">Amount : '+payment.payable_amount+'</div>' +
              '<div class="col-6">Service Provided : '+payment.service_provided+'</div>' +
              '<div class="col-6">Module : '+payment.module+'</div>' +
              '<div class="col-6">Work Hour : '+payment.work_hour+'</div>' +
              '<div class="col-6">Status: '+status+'</div>' +
              '<div class="col-6">Paid Date: '+payment.paid_date+'</div>' +
              '<div class="col-6">Paid Amount: '+payment.paid_amount+'</div>' +
              '<div class="col-6">Description: <p>'+payment.description+'</p> </div>' +
              '</div>'
          modal.find('.modal-body').html(html);
      });

      $(document).on('click', '.see_work_experience', function(){
          var id = $(this).data('id');
          $.ajax({
              url: '{{ route("vendors.cv.get-work-experience") }}',
              method: 'post',
              data: {
                  _token : '{{ csrf_token() }}',
                  id : id
                  },
          beforeSend: function() {
            // $("#loading-image").show();
          }
          }).done(function(response) {
              //$("#loading-image").hide();
              
              $('#workExpTbody').html(response.data);
              $('#see_work_experience_model').modal("show");
              toastr['success'](response.message);
          }).fail(function() {
              $("#loading-image").hide();
              toastr['error'](response.msg);
          });
      });

      $(document).on('click', '.education', function(){
          var id = $(this).data('id');
          $.ajax({
              url: '{{ route("vendors.cv.education") }}',
              method: 'post',
              data: {
                  _token : '{{ csrf_token() }}',
                  id : id
                  },
          beforeSend: function() {
            // $("#loading-image").show();
          }
          }).done(function(response) {
              //$("#loading-image").hide();
              
              $('#eductionTbody').html(response.data);
              $('#education_details_model').modal("show");
              toastr['success'](response.message);
          }).fail(function() {
              $("#loading-image").hide();
              toastr['error'](response.msg);
          });
      });

      $(document).on('click', '.address', function(){
          var id = $(this).data('id');
          $.ajax({
              url: '{{ route("vendors.cv.address") }}',
              method: 'post',
              data: {
                  _token : '{{ csrf_token() }}',
                  id : id
                  },
          beforeSend: function() {
            // $("#loading-image").show();
          }
          }).done(function(response) {
              //$("#loading-image").hide();
              
              $('#addressTbody').html(response.data);
              $('#address_model').modal("show");
              toastr['success'](response.message);
          }).fail(function() {
              $("#loading-image").hide();
              toastr['error'](response.msg);
          });
      });


      
  </script>
@endsection
