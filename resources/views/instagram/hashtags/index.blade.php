@extends('layouts.app')

@section('favicon' , 'instagram.png')

@section('title', 'Instagram Info')


@section('styles')
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
 #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
</style>
@endsection
@section('content')
 <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram HashTags</h2>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
        <div>
            <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    
                            <form action="{{ route('hashtag.index') }}" method="GET">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input name="term" type="text" class="form-control"
                                                   value="{{ isset($term) ? $term : '' }}"
                                                   placeholder="Tag Name"style="width: 300px;">

                                        </div>
                                        <div class="col-md-3 ml-5"style="display: flex;">
                                             <label class="mt-2 col-ml-sm-3">Priority </label>
                                            <input type="checkbox" name="priority"class=" mt-3"style="width: 300px;"> 
                                        </div>
                                       <div class="col-md-1 mt-2">
                                        <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                    </div>
                <div class="col-md-6">
                      <form method="post" action="{{ action([\App\Http\Controllers\HashtagController::class, 'store']) }}">
                @csrf
                <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="name" id="name" placeholder="sololuxuryindia (without hash)" class="form-control"style="width: 300px;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button class="btn-block btn btn-default">Add Hashtag</button>
                        </div>
                </div>
            </form>
        </div>
            </div>
           </div> 
           
        </div>
        <div class="col-md-12">
            <table class="table-striped table-bordered table table-sm"style="table-layout: fixed;">
                <tr>
                    <th width="1%">S.N</th>
                    <th width="2%">Tag Name</th>
                    <th width="2%">Comments</th>
                    <th width="2%">Users</th>
                    <th width="2%">Post Count</th>
                    <th width="4%">User Links</th>
                    <th width="1%">Priority</th>
                </tr>
                @foreach($hashtags as $key=>$hashtag)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td >
                            <a href="{{ action([\App\Http\Controllers\HashtagController::class, 'showGrid'],$hashtag->id) }}"style="color:black;">
                                {{ $hashtag->hashtag }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ action([\App\Http\Controllers\HashtagController::class, 'showGridComments'],$hashtag->id) }}"style="color:black;">
                                Link
                            </a>
                        </td>
                        <td>
                            <a href="{{ action([\App\Http\Controllers\HashtagController::class, 'showGridUsers'],$hashtag->hashtag) }}"style="color:black;">
                                Link
                            </a>
                        </td>
                        <td>{{$hashtag->instagramPost->count()}}</td>
                        <td></td>
                        <!-- <td>{{ $hashtag->rating }}</td> -->
                        <!-- <td>
                            <form method="post" action="{{ action([\App\Http\Controllers\HashtagController::class, 'destroy'], $hashtag->id) }}">
                                <a class="btn btn-default btn-image" href="{{ action([\App\Http\Controllers\HashtagController::class, 'showGrid'], $hashtag->id) }}">
                                    <img src="{{ asset('images/view.png') }}" alt="">
                                </a>
                                <a class="btn btn-default btn-image" href="{{ action([\App\Http\Controllers\HashtagController::class, 'edit'], $hashtag->hashtag) }}">
                                    <i class="fa fa-info"></i> Relavent Hashtags
                                </a>
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-default btn-image btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                                
                            </form>
                        </td> -->
                        <td style="display: flex; justify-content: space-between; border-bottom: none;">
<!--                           <label class="switch">
                                  @if($hashtag->priority == 1)
                                  <input type="checkbox" checked class="checkbox" value="{{ $hashtag->id }}">
                                  @else
                                  <input type="checkbox" class="checkbox" value="{{ $hashtag->id }}">
                                  @endif
                                  <span class="slider round"></span>
                                </label> -->
                          <a onclick="runCommand('{{ $hashtag->id }}')"title="start">
                              <i class="fa fa-play-circle"></i>
                          </a> 
                          <a onclick="killCommand('{{ $hashtag->id }}')"title="Kill Script">
                              <i class="fa fa-trash-o" aria-hidden="true"></i>
                          </a>
                          <a onclick="checkStatusCommand('{{ $hashtag->id }}')"title="Check Status">
                              <i class="fa fa-check-circle" aria-hidden="true"></i>
                          </a>
                                
                        </td>
                    </tr>
                @endforeach
            </table>
             {!! $hashtags->appends(Request::except('page'))->links() !!}
        </div>
    </div>
@endsection

<div class="modal fade" id="instagram-user-selections" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <form method="POST" id="instagram-user-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="hash_tag_id" id="hash-tag-id-input">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Select Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                  <div class="row mt-4">
                    <div class="col-md-6">
                      <label>Account Name</label>
                      <select name="instagram_accounts" id="instagram-account-select" class="form-control">
                        @foreach(\App\Marketing\InstagramConfig::where('provider','instagram')->get() as $instagramConfig) 
                          <option value="{{$instagramConfig->id}}">{{$instagramConfig->username}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="instagram-user-form-submit" class="btn btn-secondary">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection

@section('scripts')
    <script>
        var cid = null;
        $(function(){
            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });

        });

        $(".checkbox").change(function() {
            id = $(this).val();
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('hashtag.priority') }}',
                    data: {
                        id:id,
                        type: 1
                    },success: function (data) {
                      console.log(data);
                        if(data.status == 'error'){
                           alert('Priority Limit Exceded'); 
                           location.reload(true);
                           
                        }else{
                           alert('Hashtag Priority Added');  

                        }
                      
                    },
                    error: function (data) {
                       alert('Priority Limit Exceded');
                    }
                        });
            }else{
                 $.ajax({
                    type: 'GET',
                    url: '{{ route('hashtag.priority') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Hashtag Removed Priority');    
                    }); 
            }
        });

        $(document).on("click","#instagram-user-form-submit",function(e){ 
           e.preventDefault();
           var id = $("#hash-tag-id-input").val();
           var account = $("#instagram-account-select").val();
           $.ajax({
                type: 'POST',
                url: '{{ route('hashtag.command') }}',
                data: {
                    id:id,
                    _token: "{{ csrf_token() }}",
                    account:account
                },beforeSend: function() {
                   $("#loading-image").show();
                },success: function (data) {
                  $("#loading-image").hide();
                    if(data.status == 'error'){
                       alert('Something went wrong'); 
                       location.reload(true);
                    }else{
                       alert(data.message); 
                    }
                },
                error: function (data) {
                   alert('Something went wrong');
                }
            });
        });

        function runCommand(id) {
            $("#hash-tag-id-input").val(id);
            $("#instagram-user-selections").modal("show");
        }

        function killCommand(id) {
             $.ajax({
                    type: 'POST',
                    url: '{{ route('hashtag.command.kill') }}',
                    data: {
                        id:id,
                         _token: "{{ csrf_token() }}"
                    },beforeSend: function() {
                       $("#loading-image").show();
                    },success: function (data) {
                      $("#loading-image").hide();
                        if(data.status == 'error'){
                           alert('Something went wrong'); 
                           location.reload(true);
                           
                        }else{
                           alert(data.message);

                        }
                      
                    },
                    error: function (data) {
                       alert(data.message);
                    }
                        });
        }

        function checkStatusCommand(id) {
             $.ajax({
                    type: 'POST',
                    url: '{{ route('hashtag.command.status') }}',
                    data: {
                        id:id,
                         _token: "{{ csrf_token() }}"
                    },beforeSend: function() {
                       $("#loading-image").show();
                    },success: function (data) {
                      $("#loading-image").hide();
                        if(data.status == 'error'){
                           alert('Something went wrong'); 
                           location.reload(true);
                           
                        }else{
                           alert(data.message);

                        }
                      
                    },
                    error: function (data) {
                       alert(data.message);
                    }
                        });
        }

      $(function() {
          $('.selectpicker').selectpicker();
      });
    </script>
@endsection