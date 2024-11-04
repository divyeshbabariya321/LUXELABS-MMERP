@extends('layouts.app')

@section('title', 'Account Info')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Models</h2>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-12 border">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                                data-target="#modal_name"
                                title="Add new Model"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody id="tbodayRow">
                            @forelse ($modelName as $model)
                                <tr id="trrow{{$model->id}}">
                                    <td width="10%">{{ $model->id }}</td>
                                    <td width="60%">{{ $model->name }}</td>
                                    <td width="10%">{{$model->updated_at}}</td>
                                    <td width="10%">
                                            <button type="button" class="btn btn-image edit-model-name" data-toggle="modal" data-target="#modal_name_update" data-id="{{$model->id}}"><img src="/images/edit.png"/></button>
                                            <button type="submit" class="btn btn-image"><img src="/images/delete.png" class="deleteModelName" data-id="{{$model->id}}"/></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <th colspan="15" class="text-center text-danger">No data History Found.</th>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')


    {!! $modelName->appends(Request::except('page'))->links() !!}

    <div id="modal_name" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Model</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- name -->
                            <div class="col-md-12 col-lg-12 @if($errors->has('name')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                                <div class="form-group">
                                    {{ html()->label('Name', 'name')->class('form-control-label') }}
                                    {{ html()->text('name')->class('form-control  ' . ($errors->has('name') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                    @if($errors->has('name'))
                                        <div class="form-control-feedback">{{$errors->first('name')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary addModelName">Add</button>
                    </div>
                
            </div>

        </div>
    </div>

    <div id="modal_name_update" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Model</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <input type="hidden" name="model_id" id="model_id" />
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- name -->
                            <div class="col-md-12 col-lg-12 @if($errors->has('name')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                                <div class="form-group">
                                    {{ html()->label('Name', 'name')->class('form-control-label') }}
                                    {{ html()->text('name_update')->id("name_update")->class('form-control  ' . ($errors->has('name_update') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : ''))) }}
                                    @if($errors->has('name_update'))
                                        <div class="form-control-feedback">{{$errors->first('name_update')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary update-model-name">Update</button>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).on('click','.addModelName',function(e){
          e.preventDefault();
          var name  = $('#name').val();
          if(!name){
            alert('please add model name input box !');
            return false;
          }
          $.ajax({
                type: "POST",
                url: "{{route('model.name.store')}}",
                data: {name:name},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
                $('.ajax-loader').hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                    $('#name').val("");
                    var id = response.data.id;
                    var name = response.data.name;
                    var updated_at = response.data.updated_at;
                    $("#tbodayRow").prepend("<tr><td>"+id+"</td><td>"+name+"</td><td>"+updated_at+"</td><td><button type='button' class='btn btn-image edit-model-name' data-target='#modal_name_update' data-toggle='modal' data-id='"+id+"'><img src='/images/edit.png'/></button> <button type='submit' class='btn btn-image'><img src='/images/delete.png' class='deleteModelName' data-id='"+id+"'/></button></td></tr>");
					
                } else {
					toastr['error'](response.message, 'Error');
				}
            }).fail(function (response) {
              $('.ajax-loader').hide();
              toastr['error'](response.message, 'Error');
            });
          
        });

        $(document).on('click','.deleteModelName',function(e){
          e.preventDefault();
          var id  = $(this).data('id');
          $.ajax({
                type: "DELETE",
                url: "{{route('model.name.delete')}}",
                data: {id:id},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
              if (response.code == 200) {
                    $("#trrow"+id).hide();
					toastr['success'](response.message, 'success');
				} else {
					toastr['error'](response.message, 'Error');
				}
            }).fail(function (response) {
              $('.ajax-loader').hide();
              toastr['error'](response.message, 'Error');
            });
          
        });

        $(document).on('click','.edit-model-name',function(e){
          e.preventDefault();
          var id  = $(this).data('id');
          $.ajax({
                type: "post",
                url: "{{route('model.name.edit')}}",
                data: {id:id},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
              if (response.code == 200) {
                    $('#name_update').val(response.data.name);
                    $('#model_id').val(response.data.id);
                    $('#modal_name_update').show();
                    toastr['success'](response.message, 'success');
				} else {
					toastr['error'](response.message, 'Error');
				}
            }).fail(function (response) {
              $('.ajax-loader').hide();
              toastr['error'](response.message, 'Error');
            });
        });

        $(document).on('click','.update-model-name',function(e){
          e.preventDefault();
          var name  = $('#name_update').val();
          var model_id  = $('#model_id').val();
          if(!name){
            alert('please add model name input box !');
            return false;
          }
          $.ajax({
                type: "POST",
                url: "{{route('model.name.update')}}",
                data: {name:name, model_id:model_id},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
                $('.ajax-loader').hide();
                if (response.code == 200) {
                    toastr['success'](response.message, 'success');
                    $('#name').val("");
                    var id = response.data.id;
                    var name = response.data.name;
                    var updated_at = response.data.updated_at;
                    $("#trrow"+id).html("<td>"+id+"</td><td>"+name+"</td><td>"+updated_at+"</td><td><button type='button' class='btn btn-image edit-model-name' data-target='#modal_name_update' data-toggle='modal' data-id='"+id+"'><img src='/images/edit.png'/></button> <button type='submit' class='btn btn-image'><img src='/images/delete.png' class='deleteModelName' data-id='"+id+"'/></button></td>");
			    } else {
					toastr['error'](response.message, 'Error');
				}
            }).fail(function (response) {
              $('.ajax-loader').hide();
              toastr['error'](response.message, 'Error');
            });
          
        });
    </script>
@endsection