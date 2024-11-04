@extends('layouts.app')

@section('title', 'Blog Listing')

@section('styles')


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

    #edit_activate_tagator2 {
			width: 300px;
		}
    #tagator_activate_tagator2, #tagator_edit_activate_tagator2{
      width:237px !important;
    }
    .blogTableDatalist tr td:last-child {
    display: flex;
    align-items: center;
    gap: 5px;
}
  #blog_listing_filter {
    margin-right: 7px;
}
    
	</style>


    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('large_content')

<div class="row">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">
            Blog Listing

            <div style="float: right;">
                <a class="btn btn-success custom-button btn-publish mt-0 pull-right" style="color:#fff"  data-toggle="modal" data-target="#addBlogModal" >Add Blog <i class="fa fa-plus" aria-hidden="true"></i></a>

                <a class="btn btn-success custom-button btn-publish mt-0 pull-right" style="margin-right: 20px; color:#fff" href="{{route('view-blog-all.history')}}">View History  <i class="fa fa-history" aria-hidden="true"></i></a>

                <button type="button" class="btn btn-success custom-button mt-0 pull-right" style="margin-right: 20px; color:#fff" data-toggle="modal" data-target="#bdatatablecolumnvisibilityList">Column Visiblity</button>
            </div>
        </h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            
            <div class="modal fade" id="UpdateBlogModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">       
                <div class="modal-dialog modal-lg" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Blog</h5>  
                        </div>
                        <div class="modal-body UpdateBlogModalDataAppend">
                        </div>
                    </div>
                </div> 
            </div>

            <div class="modal fade" id="ViewContentModal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">       
                <div class="modal-dialog modal-lg" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            Content View
                        </div>
                        <div class="modal-body DataContentView">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="ViewContentModalHide">Close</button>
                        </div>
                    </div>
                </div> 
            </div>    

            <div class="modal fade" id="addBlogModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Blog</h5>
                    
                        </div>
                        <div class="modal-body">
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

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Content</label>
                                        <div class="text-danger" id="AddcontentValidation" style="display:none">Content Field is required.</div>
                                        <br>
                                        <div class="col-md-5"> <button type="button" data-toggle="modal" data-target="#ContentModal" class="btn btn-primary custom-button">Content</button></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Select Plaglarism</label>
                                        <select name="plaglarism" class="form-control">
                                            <option selected disabled value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>

                                        @error('plaglarism')
                                        <div class="alert text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Internal link</label>
                                     
                                        <select name="internal_link" class="form-control">
                                            <option selected disabled value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>

                                        @error('internal_link')
                                            <div class="alert text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>                      
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">External link</label>
                                        <select name="external_link" class="form-control">
                                            <option selected disabled value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                        @error('external_link')
                                            <div class="alert text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Title tag</label>
                                        <br>
                                        <input  type="text" name="title_tag" value="{{ old('title_tag') }}">
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
                                        <input  name="header_tag" type="text" value="{{ old('header_tag') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Italic Tag</label>
                                        <br>
                                        <select name="italic_tag" class="form-control">
                                            <option selected disabled value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
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
                                        <select name="strong_tag" class="form-control">
                                            <option selected disabled value="">Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
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
                                        <div class="row">
                                            <div class="col-md-5"> <button type="button" data-toggle="modal" data-target="#socialShare" class="btn btn-secondary custom-button">Social Share</button></div>
                                            <div class="col-md-5"><button type="button" data-toggle="modal" data-target="#google_bingo" class="btn btn-secondary custom-button">Google And Bing</button></div>
                                        </div>
                                    </div>

                                    <div class='col-md-4'>
                                        <label class="form-label">Publish Blog Date</label>
                                        <div class='input-group date' id='blog-datetime'>
                                            <input type='date' class="form-control" name="publish_blog_date" value="{{old('publish_blog_date')}}" />
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
                                            <input type='date' class="form-control" name="date" value="" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Canonical URL</label>
                                        <br>
                                        <input  name="canonical_url" type="text" name="canonical_url" value="{{ old('canonical_url') }}" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">CheckMobile Friendliness</label>
                                        <select name="checkmobile_friendliness" class="form-control">
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>                                  
                                </div> 

                       
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Select Website</label>
                                        <select class="browser-default custom-select"  required="required" name="store_website_id" style="height:auto">
                                            <option disabled value="" selected>---Selecty store websites---</option>
                                            @foreach($store_website as $sw)
                                                <option value="{{$sw->id}}" >{{$sw->website}}</option>
                                            @endforeach
                                        </select>
                                    </div>          
                                </div> 

                                <hr>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-secondary custom-button" data-dismiss="modal">Close</button>
                                        <button type="submit" class="pull-right  btn btn-success btn-rounded btn-lg" id="NewBlogCreate">Add Blog</button>
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
                                                                <input type='date' class="form-control" name="facebook_date" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                            
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Instagram</label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type='text' name="instagram" class="form-control" value="" />
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class='input-group date' id='instagram_date'>
                                                                <input type='date' class="form-control" name="instagram_date" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                            
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                          <label class="form-label">Twitter</label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type='text' name="twitter" class="form-control" value="" />
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class='input-group date' id='twitter_date'>
                                                                <input type='date' class="form-control" name="twitter_date" value="" />
                                                            </div>
                                                        </div>
                                                    </div>                                      
                                                </div>       
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss-modal="socialModal">Cancel</button>
                                                <button type="button" class="btn btn-success"  data-dismiss-modal="socialModal">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                   
                                <!-- Content Added -->
                                <div class="modal fade" id="ContentModal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Content</h5>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="content_html" id="content_html">
                                                <input type="text" name="content" id="content">
                                            </div>     
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" id="ContentModalHideButton">Cancel</button>
                                                <button type="button" class="btn btn-success"  id="ContentModalHide">Add</button>
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
                                                            <select name="google" class="form-control">
                                                                <option selected disabled value="">Select</option>
                                                                <option value="yes">Yes</option>
                                                                <option value="no">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class='input-group date' id='google_date'>
                                                                <input type='date' class="form-control" name="google_date" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                            
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                            <label class="form-label">
                                                                <img src="{{ asset('social/Bing-Logo.png') }}" style="width:50px; height:50px"/>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select name="bing" class="form-control">
                                                                <option selected disabled value="">Select</option>
                                                                <option value="yes">Yes</option>
                                                                <option value="no">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class='input-group date' id='bing_date'>
                                                                <input type='date' class="form-control" name="bing_date" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>       
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss-modal="googleBingo">Close</button>
                                                <button type="button" class="btn btn-success"  data-dismiss-modal="googleBingo">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-2 pd-3 pl-0">
                <label class="form-label">Search keyword</label>                
                <input type="text" name="keyword" class="form-control" id="keyword" placeholder="Search keyword">
            </div>
                
            <div class="form-group col-md-2 pd-3 pl-0">
                <label class="form-label">Select User</label>                
                <select class="form-control" name="user_id" id="userId">
                    <option value="">Select User</option>
                    @foreach ($users as  $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-2 pd-3 pl-0">
                <label class="form-label">Select Website</label>
                <select class="browser-default custom-select"  required="required" name="storeWebsiteId" style="height:auto">
                    <option disabled value="" selected>---Selecty store websites---</option>
                    @foreach($store_website as $sw)
                        <option value="{{$sw->id}}" >{{$sw->website}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-1 pd-3 status-select-cls select-multiple-checkbox">
                <label class="form-label">Select Date</label>
                <div class='input-group date' id='blog-datetime'>
                    <input type='date' class="form-control" name="created_at" id="created_at" value="" />
                </div>
            </div>

            <div class="form-group col-md-1 pd-3">
                <label class="form-label" style=" width: 100%;">&nbsp;</label>
                <button id="BlogFilter" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                <button  class="btn btn-image refreshTable"><i class="fa fa-history" aria-hidden="true"></i></button>
            </div>

            <div class="form-group col-md-1 pd-3">
                
            </div>
            
            <table class="table-striped table-bordered table-responsive table blogTableDatalist"
                id="blog_listing" style="overflow-x: auto !important">
                <thead>
                    <tr>
                        <th>userName</th>
                        <th>Idea</th>
                        <th>Keyword</th>
                        <th>Website</th>
                        <th>Canonical URL</th>
                        <th>CheckMobile Friendliness</th>
                        <th>Content</th>
                        <th>No Follow</th>
                        <th>No Index</th>
                        <th>Meta Desc</th>
                        <th>Plaglarism</th>
                        <th>Internal Link</th>
                        <th>External Link</th>
                        <th>Url Structure</th>
                        <th>Url XML</th>
                        <th>Header Tag</th>
                        <th>Strong Tag</th>
                        <th>Title Tag</th>
                        <th>Italic Tag</th>
                        <th>Facebook</th>
                        <th>Facebook Date</th>
                        <th>Google</th>
                        <th>Google Date</th>
                        <th>Twitter</th>
                        <th>Twitter Date</th>
                        <th>Bing</th>
                        <th>Bing Date</th>
                        <th>Instagram</th>
                        <th>Instagram Date</th>
                        <th>Publish Date</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include("blogs.column-visibility-modal")
@endsection
@section('scripts')
  <script type="text/javascript" src="{{ mix('webpack-dist/js/fm-tagator.js') }} "></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script type="text/javascript">
    @if(Session::has('message'))
   toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
  		toastr.success("{{ session('message') }}");
  @endif

 @if(Session::has('error'))
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
  		toastr.error("{{ session('error') }}");
  @endif

        $(function() {
                 
            var table = $('#blog_listing').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
              ajax: {
                  url: "{{ route('blog.index') }}",
                  data: function (d) {
                      d.user_id = $('#userId').val(),
                      d.keyword = $('#keyword').val(),
                      d.storeWebsiteId = $('#storeWebsiteId').val(),
                      d.date = $('#created_at').val()
                    
                  }
              },      
                columns: [        
                    @if(!empty($dynamicColumnsToShowb))    
                        @if (!in_array('userName', $dynamicColumnsToShowb))
                            {data: 'userName', name: 'userName',orderable: false, searchable: true},
                        @else
                            {data: 'userName', name: 'userName',orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Idea', $dynamicColumnsToShowb))
                            {data: 'idea', name: 'idea', orderable: false, searchable: true},
                        @else
                            {data: 'idea', name: 'idea', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Keyword', $dynamicColumnsToShowb))
                            {data: 'keyword', name: 'keyword', orderable: false, searchable: false},
                        @else
                            {data: 'keyword', name: 'keyword', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Website', $dynamicColumnsToShowb))
                            {data: 'store_website_id', name: 'store_website_id', orderable: false, searchable: false},
                        @else
                            {data: 'store_website_id', name: 'store_website_id', orderable: false, searchable: false, 'visible': false },
                        @endif
                        
                        @if (!in_array('Canonical URL', $dynamicColumnsToShowb))
                            {data: 'canonical_url', name: 'canonical_url', orderable: false, searchable: false},
                        @else
                            {data: 'canonical_url', name: 'canonical_url', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('CheckMobile Friendliness', $dynamicColumnsToShowb))
                            {data: 'checkmobile_friendliness', name: 'checkmobile_friendliness', orderable: false, searchable: false},
                        @else
                            {data: 'checkmobile_friendliness', name: 'checkmobile_friendliness', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Content', $dynamicColumnsToShowb))
                            {data: 'content', name: 'content', orderable: false, searchable: true},
                        @else
                            {data: 'content', name: 'content', orderable: false, searchable: true, 'visible': false },
                        @endif
                        
                        @if (!in_array('No Index', $dynamicColumnsToShowb))
                            {data: 'no_index', name: 'no_index', orderable: false, searchable: false},
                        @else
                            {data: 'no_index', name: 'no_index', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('No Follow', $dynamicColumnsToShowb))
                            {data: 'no_follow', name: 'no_follow', orderable: false, searchable: false},
                        @else
                            {data: 'no_follow', name: 'no_follow', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Meta Desc', $dynamicColumnsToShowb))
                            {data: 'meta_desc', name: 'meta_desc', orderable: false, searchable: true},
                        @else
                            {data: 'meta_desc', name: 'meta_desc', orderable: false, searchable: true, 'visible': false },
                        @endif
                        
                        @if (!in_array('Plaglarism', $dynamicColumnsToShowb))
                            {data: 'plaglarism', name: 'plaglarism', orderable: false, searchable: true},
                        @else
                            {data: 'plaglarism', name: 'plaglarism', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Internal Link', $dynamicColumnsToShowb))
                            {data: 'internal_link', name: 'internal_link', orderable: false, searchable: true},
                        @else
                            {data: 'internal_link', name: 'internal_link', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('External Link', $dynamicColumnsToShowb))
                            {data: 'external_link', name: 'external_link', orderable: false, searchable: true},
                        @else
                            {data: 'external_link', name: 'external_link', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Url Structure', $dynamicColumnsToShowb))
                            {data: 'url_structure', name: 'url_structure', orderable: false, searchable: true},
                        @else
                            {data: 'url_structure', name: 'url_structure', orderable: false, searchable: true, 'visible': false },
                        @endif
                        
                        @if (!in_array('Url XML', $dynamicColumnsToShowb))
                            {data: 'url_xml', name: 'url_xml', orderable: false, searchable: true},
                        @else
                            {data: 'url_xml', name: 'url_xml', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Header Tag', $dynamicColumnsToShowb))
                            {data: 'header_tag', name: 'header_tag', orderable: false, searchable: false},
                        @else
                            {data: 'header_tag', name: 'header_tag', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Strong Tag', $dynamicColumnsToShowb))
                            {data: 'strong_tag', name: 'strong_tag', orderable: false, searchable: false},
                        @else
                            {data: 'strong_tag', name: 'strong_tag', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Title Tag', $dynamicColumnsToShowb))
                            {data: 'title_tag', name: 'title_tag', orderable: false, searchable: false},
                        @else
                            {data: 'title_tag', name: 'title_tag', orderable: false, searchable: false, 'visible': false },
                        @endif
                            
                        @if (!in_array('Italic Tag', $dynamicColumnsToShowb))
                            {data: 'italic_tag', name: 'italic_tag', orderable: false, searchable: false},
                        @else
                            {data: 'italic_tag', name: 'italic_tag', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Facebook', $dynamicColumnsToShowb))
                            {data: 'facebook', name: 'facebook', orderable: false, searchable: true},
                        @else
                            {data: 'facebook', name: 'facebook', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Facebook Date', $dynamicColumnsToShowb))
                            {data: 'facebook_date', name: 'facebook_date', orderable: false, searchable: true},
                        @else
                            {data: 'facebook_date', name: 'facebook_date', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Google', $dynamicColumnsToShowb))
                            {data: 'google', name: 'google', orderable: false, searchable: true},
                        @else
                            {data: 'google', name: 'google', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Google Date', $dynamicColumnsToShowb))
                            {data: 'google_date', name: 'google_date', orderable: false, searchable: true},
                        @else
                            {data: 'google_date', name: 'google_date', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Twitter', $dynamicColumnsToShowb))
                            {data: 'twitter', name: 'twitter', orderable: false, searchable: true},
                        @else
                            {data: 'twitter', name: 'twitter', orderable: false, searchable: true, 'visible': false },
                        @endif
                        
                        @if (!in_array('Twitter Date', $dynamicColumnsToShowb))
                            {data: 'twitter_date', name: 'twitter_date', orderable: false, searchable: true},
                        @else
                            {data: 'twitter_date', name: 'twitter_date', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Bing', $dynamicColumnsToShowb))
                            {data: 'bing', name: 'bing', orderable: false, searchable: true},
                        @else
                            {data: 'bing', name: 'bing', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Bing Date', $dynamicColumnsToShowb))
                            {data: 'bing_date', name: 'bing_date', orderable: false, searchable: true},
                        @else
                            {data: 'bing_date', name: 'bing_date', orderable: false, searchable: true, 'visible': false },
                        @endif
                        
                        @if (!in_array('Instagram', $dynamicColumnsToShowb))
                            {data: 'instagram', name: 'instagram', orderable: false, searchable: true},
                        @else
                            {data: 'instagram', name: 'instagram', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Instagram Date', $dynamicColumnsToShowb))
                            {data: 'instagram_date', name: 'instagram_date', orderable: false, searchable: true},
                        @else
                            {data: 'instagram_date', name: 'instagram_date', orderable: false, searchable: true, 'visible': false },
                        @endif

                        @if (!in_array('Publish Date', $dynamicColumnsToShowb))
                            {data: 'publish_blog_date', name: 'publish_blog_date', orderable: false, searchable: false},
                        @else
                            {data: 'publish_blog_date', name: 'publish_blog_date', orderable: false, searchable: false, 'visible': false },
                        @endif
                        
                        @if (!in_array('Created Date', $dynamicColumnsToShowb))
                            {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                        @else
                            {data: 'created_at', name: 'created_at', orderable: false, searchable: false, 'visible': false },
                        @endif

                        @if (!in_array('Action', $dynamicColumnsToShowb))
                            {data: 'action', name: 'action', orderable: false, searchable: false},
                        @else
                            {data: 'action', name: 'action', orderable: false, searchable: false, 'visible': false },
                        @endif
                        
                    @else      
                        {data: 'userName', name: 'userName',orderable: false, searchable: true},
                        {data: 'idea', name: 'idea', orderable: false, searchable: true},
                        {data: 'keyword', name: 'keyword', orderable: false, searchable: false},
                        {data: 'store_website_id', name: 'store_website_id', orderable: false, searchable: false},
                        {data: 'canonical_url', name: 'canonical_url', orderable: false, searchable: false},
                        {data: 'checkmobile_friendliness', name: 'checkmobile_friendliness', orderable: false, searchable: false},
                        {data: 'content', name: 'content', orderable: false, searchable: true},
                        {data: 'no_index', name: 'no_index', orderable: false, searchable: false},
                        {data: 'no_follow', name: 'no_follow', orderable: false, searchable: false},
                        {data: 'meta_desc', name: 'meta_desc', orderable: false, searchable: true},
                        {data: 'plaglarism', name: 'plaglarism', orderable: false, searchable: true},
                        {data: 'internal_link', name: 'internal_link', orderable: false, searchable: true},
                        {data: 'external_link', name: 'external_link', orderable: false, searchable: true},
                        {data: 'url_structure', name: 'url_structure', orderable: false, searchable: true},
                        {data: 'url_xml', name: 'url_xml', orderable: false, searchable: true},
                        {data: 'header_tag', name: 'header_tag', orderable: false, searchable: false},
                        {data: 'strong_tag', name: 'strong_tag', orderable: false, searchable: false},
                        {data: 'title_tag', name: 'title_tag', orderable: false, searchable: false},
                        {data: 'italic_tag', name: 'italic_tag', orderable: false, searchable: false},
                        {data: 'facebook', name: 'facebook', orderable: false, searchable: true},
                        {data: 'facebook_date', name: 'facebook_date', orderable: false, searchable: true},
                        {data: 'google', name: 'google', orderable: false, searchable: true},
                        {data: 'google_date', name: 'google_date', orderable: false, searchable: true},
                        {data: 'twitter', name: 'twitter', orderable: false, searchable: true},
                        {data: 'twitter_date', name: 'twitter_date', orderable: false, searchable: true},
                        {data: 'bing', name: 'bing', orderable: false, searchable: true},
                        {data: 'bing_date', name: 'bing_date', orderable: false, searchable: true},
                        {data: 'instagram', name: 'instagram', orderable: false, searchable: true},
                        {data: 'instagram_date', name: 'instagram_date', orderable: false, searchable: true},
                        {data: 'publish_blog_date', name: 'publish_blog_date', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    @endif
                ]
          });

           $('#blog_listing').on('click','#BlogEditModal',function(){
            
            var id = $(this).data('blog-id');

            

            // AJAX request
            $.ajax({
                url: 'edit/'+id,
                type: 'GET',
                data: {id: id},
                dataType: 'json',
                success: function(response){
                  console.log(response);
                    if(response.status == 'success'){
                      $('.UpdateBlogModalDataAppend').html("");
                     $('.UpdateBlogModalDataAppend').append(response.data.html);
                   
                      $('#UpdateBlogModal').modal("show");
                      
                    
                        
                    }else{
                         alert("Invalid ID.");
                    }
                }
            });

       });

       $('#blog_listing').on('click','#ViewContent',function(){
           
        var id = $(this).data('blog-id');

        // AJAX request
        $.ajax({
            url: 'contentview/'+id,
            type: 'GET',
            data: {id: id},
            dataType: 'json',
            success: function(response){
              console.log(response);
                if(response.status == 'success'){
                  $('.DataContentView').html("");
                 $('.DataContentView').append(response.data.html);
               
                  $('#ViewContentModal').modal("show");
                  
                
                    
                }else{
                     alert("Something went wrong!");
                }
            }
        });

   });


          $('.refreshTable').click(function(){
             
                $('#userId').val('');
                $('#keyword').val('');
                $('#storeWebsiteId').val('');
                $('#created_at').val('');
              $('#blog_listing').DataTable().ajax.reload();
            });

            $("#BlogFilter").on("click",function(e){
              table.draw();
              e.preventDefault();
            
            });
          
          });

   


        $("button[data-dismiss-modal=socialModal]").click(function () {
         
        $('#socialShare').modal('hide');
        });

       
        

        $("button[data-dismiss-modal=googleBingo]").click(function () {
        $('#google_bingo').modal('hide');
        });

        $("button[data-dismiss-modal=EditGoogleBing]").click(function () {
        
        $('#edit_google_bingo').modal('hide');
        });

      

          $(document).on('click', '#EditSharemodalClose', function(e){
           $('#EditsocialShare').modal('hide');
          });

           $(document).on('click', '#UpdateBlogdata', function(e){
          var content = $('#editcontent').val().trim();
           if(content === ''){
            $('#EditcontentValidation').css('display','block');
              e.preventDefault();
             return false;
           }
            
          });


        $("#addBlog").submit(function(e){
               var content = $('#content').val().trim();
               
           if(content === ''){
            $('#AddcontentValidation').css('display','block');
              e.preventDefault();
             return false;
           }
            
        });

         

           $(document).on('click', '#EditContentModalClose', function(e){
            var editorText = CKEDITOR.instances.editcontent.getData();
            
            $(document).find('#editcontent').val(editorText);
            $('#EditContentModal').modal('hide');
          });
          $(document).on('click', '#EditContentModalCloseButton', function(e){
            
            $('#EditContentModal').modal('hide');
          });

          

          
           $(document).on('click', '#ContentModalHide', function(e){
            var editorText = CKEDITOR.instances.content.getData();
            
            $(document).find('#content').val(editorText);
           $('#ContentModal').modal('hide');
          });
          
          $(document).on('click', '#ContentModalHideButton', function(e){
            
           $('#ContentModal').modal('hide');
          });

          $(document).on('click', '#ViewContentModalHide', function(e){
            
            $('#ViewContentModal').modal('hide');
           });

          

          

          $(document).on('click', '#EditContentDataModal', function(e){

            var Editcontentvalue = $(document).find('#editcontent').val();
            
            CKEDITOR.instances.editcontent.setData(Editcontentvalue)
      
            $('#EditContentModal').modal('show');
          });
          

           $(document).on('click', '#EditGoogleBingo', function(e){
           $('#edit_google_bingo').modal('hide');
          });
       

    

        $(document).on("click",".delete-blog",function(e){
          e.preventDefault();
          var id = $(this).data('blog-id');
          
          var x = window.confirm("Are you sure, you want to delete ?");
          if(!x) {
            return;
          }
          
          $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "delete/"+id,
              type: "DELETE",
              data: {id : id}
            }).done(function(response) {
                
              toastr['success'](response.message);
           $('#blog_listing').DataTable().ajax.reload();
            }).fail(function(errObj) {
            });
        });


        

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

        $(document).find('#edit-blog-datetime input').datetimepicker({
        format: 'YYYY-MM-DD'
        });
       $('#edit_facebook_date').datetimepicker({
        format: 'YYYY-MM-DD'
        });
        $('#edit_instagram_date').datetimepicker({
          format: 'YYYY-MM-DD'
        });
        $('#edit_twitter_date').datetimepicker({
          format: 'YYYY-MM-DD'
        });
        $('#edit_google_date').datetimepicker({
          format: 'YYYY-MM-DD'
        });
        $('#edit_bing_date').datetimepicker({
          format: 'YYYY-MM-DD'
        });

        $('#edit_date').datetimepicker({
          format: 'YYYY-MM-DD'
        });


      

        });
    </script>
@endsection
