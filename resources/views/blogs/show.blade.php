@extends('layouts.app')
@section('title', __('View Blog'))
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

@endsection
@section('content')
@php
    $auth = auth()->user();
@endphp

<div class="container-fluid">
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="text-center">View Blog</h3>
            <hr>
        </div>
        <div class="card-body">
         
            <input type="hidden" value="{{$blog->id}}" name='id'>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Select user </label>
                            <select name="user_id" class="form-control">
                              
                                @foreach ($users as $user)
                                <option disabled value="{{ $user->id }}" {{ $user->id == $blog->user_id ? 'selected' : '' }} >{{ $user->name }}</option>
                                @endforeach
                               
                            </select>
                        @error('user_id')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>

                      <div class="col-md-4">
                        <label class="form-label">Idea</label>
                        <input type="text" readonly name="idea" class="form-control" value="{{$blog->idea}}">
                        @error('idea')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                     <div class="col-md-4">
                        <label class="form-label">Keyword</label>
                        <input type="text" readonly name="keyword" class="form-control" value="{{$blog->keyword}}">
                         @error('keyword')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                </div>

                <div class="row mt-3">
                
                     <div class="col-md-4">
                        <label class="form-label">Content</label>
                        <br>
                        <textarea readonly  name="content" rows="4" cols="50">{{$blog->content}}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Select Plaglarism</label>
                        <select name="plaglarism" class="form-control">
                            <option disabled value="yes" {{ $blog->plaglarism == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option disabled {{ $blog->plaglarism == 'no' ? 'selected' : '' }} value="no">No</option>
                        </select>

                         @error('plaglarism')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Internal link</label>
                        <input type="text" readonly name="internal_link" class="form-control" value="{{$blog->internal_link}}">
                         @error('internal_link')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                     
                  
                </div>



                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">External link</label>
                        <input type="text" readonly name="external_link" class="form-control" value="{{$blog->external_link}}">
                        @error('external_link')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Title tag</label>
                        <br>
                        <input id="activate_tagator2" type="text" readonly name="title_tag" class="tagator" value="{{$titleTagEditValue}}" data-tagator-show-all-options-on-focus="true">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Meta Desc</label>
                        <input type="text" readonly name="meta_desc" class="form-control" value="{{ $blog->meta_desc}}">
                     </div>
                  
                </div>



                  <div class="row mt-3">
                   
                    <div class="col-md-4">
                       <label class="form-label">Url Structure</label>
                        <br>
                        <input  name="url_structure" readonly type="text"  value="{{ $blog->url_structure}}" class="form-control">
                    
                    </div>
                    <div class="col-md-4">
                       <label class="form-label">Header tag</label>
                        <br>
                        <input id="activate_tagator2" readonly name="header_tag" type="text" class="tagator" value="{{$headerTagEditValue}}" data-tagator-show-all-options-on-focus="true">
                    
                    </div>

                       <div class="col-md-4">
                       <label class="form-label">Italic Tag</label>
                        <br>
                       <input id="activate_tagator2" readonly name="italic_tag" type="text" class="tagator" value="{{$headerTagEditValue}}" data-tagator-show-all-options-on-focus="true">
                    
                    </div>
                    
                </div>
    
                <div class="row mt-3">
                  

                     <div class="col-md-4">
                       <label class="form-label">Url To Xml</label>
                        <br>
                        <input  name="url_xml" type="text" readonly value="{{$blog->url_xml}}" class="form-control">
                    
                    </div>

                    <div class="col-md-4">
                       <label class="form-label">Strong Tag</label>
                        <br>
                       <input id="activate_tagator2" readonly name="strong_tag" type="text" class="tagator" value="{{$headerTagEditValue}}" data-tagator-show-all-options-on-focus="true" >
                    
                    </div>

                     <div class="col-md-4">
                            <div class="form-check form-check-inline mt-4">
                             <label class="form-check-label" for="priceApprove">No Follow</label>
                            <div class="col-md-6">
                                <input class="form-check-input" disabled type="radio" {{ $blog->no_follow == '1' ? 'checked' : ''}}  name="no_follow" value="1">
                                <label for="css">Yes</label><br>
                            </div>

                            <div class="col-md-6">
                                <input class="form-check-input" disabled type="radio"  {{ $blog->no_follow == '0' ? 'checked' : ''}} name="no_follow" value="0">
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
                                            <input type='text' class="form-control" name="publish_blog_date" value="{{$blog->publish_blog_date}}" />
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
                                <input class="form-check-input" type="radio" {{ $blog->no_index == '1' ? 'checked' : ''}}  name="no_index" value="1">
                                <label for="css">Yes</label><br>
                            </div>

                            <div class="col-md-6">
                                <input class="form-check-input" type="radio"  name="no_index" {{ $blog->no_index == '0' ? 'checked' : ''}} value="0">
                                <label for="css">No</label><br>
                            </div>
                                
                                
                            </div>
                        </div>
                     
                  </div>
                
                
                <div class="row mt-3">
                            

                            <div class='col-md-4'>
                                    <label class="form-label">Date</label>
                                    <div class='input-group date' id='date'>
                                            <input type='text' class="form-control" name="date" value="{{$blog->date}}" />
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                            </div>

                              
                </div> 

                <hr>
                

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
                                    <input type='text' name="facebook"  class="form-control" value="{{$blog->facebook}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='facebook_date'>
                                    <input type='text' class="form-control" name="facebook_date" value="{{$blog->facebook_date}}" />
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
                                    <input type='text' readonly name="instagram" class="form-control" value="{{$blog->instagram}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='instagram_date'>
                                    <input type='text' class="form-control" readonly name="instagram_date" value="{{$blog->instagram_date}}" />
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
                                    <input type='text' name="twitter" readonly class="form-control" value="{{$blog->twitter}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='twitter_date'>
                                    <input type='text' class="form-control" readonly name="twitter_date" value="{{$blog->twitter_date}}" />
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
                                    <input type='text' name="google" class="form-control" value="{{$blog->google}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='google_date'>
                                    <input type='text' class="form-control" name="google_date" value="{{$blog->google_date}}" />
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
                                    <input type='text' name="bing" class="form-control" value="{{$blog->bing}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='bing_date'>
                                    <input type='text' class="form-control" name="bing_date" value="{{$blog->bing_date}}" />
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
            </div>
        </div>
    </div>
</div>


            </form>
        </div>
    </div>
</div>
@endsection
