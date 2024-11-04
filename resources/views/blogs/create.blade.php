@extends('layouts.app')
@section('title', __('Add Blog'))
@section("styles")
     <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/fm-tagator.css') }}">
    <style>
		
		#wrapper {
			padding: 15px;
      margin:100px auto;
      max-width:728px;
		}
		#input_tagator1 {
			width: 300px;
		}
		#activate_tagator2 {
			width: 300px;
		}
	</style>
@endsection
@section('scripts')
 <script type="text/javascript" src="{{ mix('webpack-dist/js/fm-tagator.js') }} "></script>

@endsection
@section('content')
@php
    $auth = auth()->user();
@endphp

<div class="container-fluid">
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="text-center">Add Blog</h3>
            <hr>
        </div>
        <div class="card-body">
            <form action="{{route('store-blog.submit')}}" method="POST" id="addBlog" autocomplete="off">
            @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Select user </label>
                            <select name="user_id" required class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                @endforeach
                            </select>
                        @error('user_id')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>

                      <div class="col-md-4">
                        <label class="form-label">Idea</label>
                        <input type="text" name="idea" class="form-control" value="{{ old('idea') }}">
                        @error('idea')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                     <div class="col-md-4">
                        <label class="form-label">Keyword</label>
                        <input type="text" name="keyword" class="form-control" value="{{ old('keyword') }}">
                         @error('keyword')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                </div>

                <div class="row mt-3">
                    
                   

                     <div class="col-md-4">
                        <label class="form-label">Content</label>
                        <br>
                        <textarea  name="content" rows="4" cols="50"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Select Plaglarism</label>
                        <select name="plaglarism" class="form-control">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>

                         @error('plaglarism')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Internal link</label>
                        <input type="text" name="internal_link" class="form-control" value="{{ old('internal_link') }}">
                         @error('internal_link')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                     
                  
                </div>



                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">External link</label>
                        <input type="text" name="external_link" class="form-control" value="{{ old('external_link') }}">
                        @error('external_link')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Title tag</label>
                        <br>
                        <input id="activate_tagator2" type="text" name="title_tag" class="tagator" value="{{ old('title_tag') }}" data-tagator-show-all-options-on-focus="true" data-tagator-autocomplete={{$tagName}}>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Meta Desc</label>
                        <input type="text" name="meta_desc" class="form-control" value="{{ old('meta_desc') }}">
                     </div>
                  
                </div>



                  <div class="row mt-3">
                   
                    <div class="col-md-4">
                       <label class="form-label">Url Structure</label>
                        <br>
                        <input  name="url_structure" type="text"  value="{{ old('url_structure') }}" class="form-control">
                    
                    </div>
                    <div class="col-md-4">
                       <label class="form-label">Header tag</label>
                        <br>
                        <input id="activate_tagator2" name="header_tag" type="text" class="tagator" value="{{ old('header_tag') }}" data-tagator-show-all-options-on-focus="true" data-tagator-autocomplete={{$tagName}}>
                    
                    </div>

                       <div class="col-md-4">
                       <label class="form-label">Italic Tag</label>
                        <br>
                       <input id="activate_tagator2" name="italic_tag" type="text" class="tagator" value="{{ old('italic_tag') }}" data-tagator-show-all-options-on-focus="true" data-tagator-autocomplete={{$tagName}}>
                    
                    </div>
                    
                </div>
    
                <div class="row mt-3">
                  

                     <div class="col-md-4">
                       <label class="form-label">Url To Xml</label>
                        <br>
                        <input  name="url_xml" type="text"  value="{{ old('url_xml') }}" class="form-control">
                    
                    </div>

                    <div class="col-md-4">
                       <label class="form-label">Strong Tag</label>
                        <br>
                       <input id="activate_tagator2" name="strong_tag" type="text" class="tagator" value="{{ old('strong_tag') }}" data-tagator-show-all-options-on-focus="true" data-tagator-autocomplete={{$tagName}}>
                    
                    </div>

                     <div class="col-md-4">
                            <div class="form-check form-check-inline mt-4">
                             <label class="form-check-label" for="priceApprove">No Follow</label>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio"  name="no_follow" value="1">
                                <label for="css">Yes</label><br>
                            </div>

                            <div class="col-md-6">
                                <input class="form-check-input" type="radio"  name="no_follow" value="0">
                                <label for="css">No</label><br>
                            </div>
                                
                                
                            </div>
                        </div>
                </div>    

               


                

                  <div class="row mt-3">
                        <div class="col-md-12">
                                <label class="form-label">Social Share</label>
                            </div>
                         
                            
                            <div class="col-md-4">
                            <button type="button" data-toggle="modal" data-target="#socialShare" class="btn btn-primary">Social Share</button>
                            <button type="button" data-toggle="modal" data-target="#google_bingo" class="btn btn-primary">Google And Bing</button>
                            </div>
                            <div class='col-md-4'>
                                    <label class="form-label">Publish Blog Date</label>
                                    <div class='input-group date' id='blog-datetime'>
                                            <input type='text' class="form-control" name="publish_blog_date" value="{{old('publish_blog_date')}}" />
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                            

                                    </div>
                                     @error('publish_blog_date')
                                            <div class="alert text-danger">{{ $message }}</div>
                                            @enderror
                            </div>
                      
                         <div class="col-md-4">
                            <div class="form-check form-check-inline mt-4">
                             <label class="form-check-label" for="priceApprove">No Index</label>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio"  name="no_index" value="1">
                                <label for="css">Yes</label><br>
                            </div>

                            <div class="col-md-6">
                                <input class="form-check-input" type="radio"  name="no_index" value="0">
                                <label for="css">No</label><br>
                            </div>
                                
                                
                            </div>
                        </div>
                     
                  </div>
                
                
                <div class="row mt-3">
                            

                            <div class='col-md-4'>
                                    <label class="form-label">Date</label>
                                    <div class='input-group date' id='date'>
                                            <input type='text' class="form-control" name="date" value="" />
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                            </div>

                              
                </div> 

                <hr>
                <div class="row mt-3">
                    <div class="col-md-12">
                        
                        <button type="submit" class="pull-right btn btn-success btn-rounded btn-lg">Add Blog</button>

                    </div>
                </div>

                <!-- Social Share -->
<div class="modal fade" id="socialShare" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Social Share</h5>
                
            </div>
            <div class="modal-body">
                    <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                  <label class="form-label">Facebook</label>
                                </div>
                                 <div class="col-md-3">
                                    <input type='text' name="facebook" class="form-control" value="" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='facebook_date'>
                                    <input type='text' class="form-control" name="facebook_date" value="" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <br>
                            
                               <div class="col-md-12">
                                <div class="col-md-4">
                                  <label class="form-label">Instagram</label>
                                </div>
                                 <div class="col-md-3">
                                    <input type='text' name="instagram" class="form-control" value="" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='instagram_date'>
                                    <input type='text' class="form-control" name="instagram_date" value="" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <br>
                            
                              <div class="col-md-12">
                                <div class="col-md-4">
                                  <label class="form-label">Twitter</label>
                                </div>
                                 <div class="col-md-3">
                                    <input type='text' name="twitter" class="form-control" value="" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='twitter_date'>
                                    <input type='text' class="form-control" name="twitter_date" value="" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>

                                </div>
                            </div>
                           
                    </div>       
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick=socialShareClick() data-dismiss="modal">Add</button>

            </div>
        </div>
    </div>
</div>


<!-- Google And Bing -->
<div class="modal fade" id="google_bingo" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submission</h5>
                
            </div>
            <div class="modal-body">
                    <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                  <img src="{{ asset('social/gogole_icon.png') }}" style="width:50px; height:50px"/>
                                </div>
                                 <div class="col-md-3">
                                    <input type='text' name="google" class="form-control" value="" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='google_date'>
                                    <input type='text' class="form-control" name="google_date" value="" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <br>
                            
                               <div class="col-md-12">
                                <div class="col-md-4">
                                  <label class="form-label">
                                  <img src="{{ asset('social/Bing-Logo.png') }}" style="width:50px; height:50px"/>
                                  </label>
                                </div>
                                 <div class="col-md-3">
                                    <input type='text' name="bing" class="form-control" value="" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='bing_date'>
                                    <input type='text' class="form-control" name="bing_date" value="" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>

                                </div>
                            </div>
                           
                           
                    </div>       
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick=socialShareClick() data-dismiss="modal">Add</button>

            </div>
        </div>
    </div>
</div>


            </form>
        </div>
    </div>
</div>








<!-- Publish team status modal -->
<div class="modal fade" id="kwPublishModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Publish team status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>

<script>
    
    $(document).ready(function() {

    $('#blog-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    $('#facebook_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $('#instagram_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $('#twitter_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $('#google_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
      $('#bing_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });

      $('#date').datetimepicker({
        format: 'YYYY-MM-DD'
      });




      
        let kwRowIdCount = 1;
        

        $(function() {
            $(document).on('click', ".kwRowSec .kwRow .seoStatusBtn", function() {
                let $kwRow = $(this).closest('.kwRow');
                $.ajax({
                    type: "GET"
                    , url: ""
                    , data: {
                        statusType: "SEO_STATUS"
                        , keywordId: $($kwRow).find('.keywordId').val()
                    }
                    , dataType: "json"
                    , success: function(response) {
                        let $seoModal = $(document).find('#kwSeoModal');
                        $($seoModal).find('.modal-body').html(response.data);
                        $($seoModal).attr('data-rowid', `#${$kwRow.attr('id')}`)
                        $($seoModal).modal('show');
                        $(document).find('input, select').attr('readonly', true)
                    }
                });
            })

            $(document).on('hide.bs.modal', "#kwSeoModal", function() {
                $('input', '#kwSeoModal').val('');
                $('#kwSeoModal').attr('data-rowid', '');
            });
        })

        $(function() {
            $(document).on('click', ".kwRowSec .kwRow .publishStatusBtn", function() {
                let $kwRow = $(this).closest('.kwRow');
                $.ajax({
                    type: "GET"
                    , url: ""
                    , data: {
                        statusType: "PUBLISH_STATUS"
                        , keywordId: $($kwRow).find('.keywordId').val()
                    }
                    , dataType: "json"
                    , success: function(response) {
                        let $publishModal = $(document).find('#kwPublishModal');
                        $($publishModal).find('.modal-body').html(response.data);
                        $($publishModal).attr('data-rowid', `#${$kwRow.attr('id')}`)
                        $($publishModal).modal('show');
                        $(document).find('input, select').attr('readonly', true)
                    }
                });
            })
        })

        $(document).on('hide.bs.modal', "#kwPublishModal", function() {
            $('input', '#kwPublishModal').val('');
            $('#kwPublishModal').attr('data-rowid', '');
        });
    });

</script>
@endsection
