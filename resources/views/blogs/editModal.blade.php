

  <script type="text/javascript" src="{{ mix('webpack-dist/js/fm-tagator.js') }} "></script>

  <script>
    if (CKEDITOR.instances['editcontent']) {
        CKEDITOR.instances['editcontent'].destroy(true);
    }

    CKEDITOR.replace( 'editcontent' );
  </script>
    
 

            <form action="{{ route('update-blog.submit', $blog->id) }}" method="POST" id="EditBlog" autocomplete="off">
            @csrf
            <input type="hidden" value="{{$blog->id}}" name='id'>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Select user </label>
                            <select name="user_id" class="form-control">
                                <option value="">-- Select --</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $blog->user_id ? 'selected' : '' }} >{{ $user->name }}</option>
                                @endforeach
                               
                            </select>
                        @error('user_id')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>

                      <div class="col-md-4">
                        <label class="form-label">Idea</label>
                        <input type="text" name="idea" class="form-control" value="{{$blog->idea}}">
                        @error('idea')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                     <div class="col-md-4">
                        <label class="form-label">Keyword</label>
                        <input type="text" name="keyword" class="form-control" value="{{$blog->keyword}}">
                         @error('keyword')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                </div>

                <div class="row mt-3">
                        
                      
                     <div class="col-md-4">
                        <label class="form-label">Content</label>
                        <br>
                         <div class="text-danger" id="EditcontentValidation" style="display:none">Content Field is required.</div>
                        <div> <button type="button" data-toggle="modal" id ="EditContentDataModal"  class="btn btn-primary custom-button">Content</button></div>   
                    </div>



                    <div class="col-md-4">
                        <label class="form-label">Select Plaglarism</label>
                        <select name="plaglarism" class="form-control">
                            <option value="yes" {{ $blog->plaglarism == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option {{ $blog->plaglarism == 'no' ? 'selected' : '' }} value="no">No</option>
                        </select>

                         @error('plaglarism')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Internal link</label>
                       <select name="internal_link" class="form-control">
                                        <option value="yes" {{ $blog->internal_link == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option {{ $blog->internal_link == 'no' ? 'selected' : '' }} value="no">No</option>
                                    </select>
                         @error('internal_link')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                     
                  
                </div>



                <div class="row mt-3">
                    <div class="col-md-4">
                        <label class="form-label">External link</label>
                        <select name="external_link" class="form-control">
                            <option value="yes" {{ $blog->external_link == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option {{ $blog->external_link == 'no' ? 'selected' : '' }} value="no">No</option>
                        </select>
                        @error('external_link')
                        <div class="alert text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Title tag</label>
                        <br>
                        <input  type="text" name="title_tag" value="{{$blog->title_tag}}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Meta Desc</label>
                        <input type="text" name="meta_desc" class="form-control" value="{{ $blog->meta_desc}}">
                     </div>
                  
                </div>



                  <div class="row mt-3">
                   
                    <div class="col-md-4">
                       <label class="form-label">Url Structure</label>
                        <br>
                        <input  name="url_structure" type="text"  value="{{ $blog->url_structure}}" class="form-control">
                    
                    </div>
                    <div class="col-md-4">
                       <label class="form-label">Header tag</label>
                        <br>
                        <input  name="header_tag" type="text"  value="{{$blog->header_tag}}">
                    
                    </div>

                       <div class="col-md-4">
                       <label class="form-label">Italic Tag</label>
                        <br>
                        <select name="italic_tag" class="form-control">
                            <option value="yes" {{ $blog->italic_tag == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option {{ $blog->italic_tag == 'no' ? 'selected' : '' }} value="no">No</option>
                        </select>
                    
                    </div>
                    
                </div>
    
                <div class="row mt-3">
                  

                     <div class="col-md-4">
                       <label class="form-label">Url To Xml</label>
                        <br>
                        <input  name="url_xml" type="text"  value="{{$blog->url_xml}}" class="form-control">
                    
                    </div>

                    <div class="col-md-4">
                       <label class="form-label">Strong Tag</label>
                        <br>
                        <select name="strong_tag" class="form-control">
                            <option value="yes" {{ $blog->strong_tag == 'yes' ? 'selected' : '' }}>Yes</option>
                            <option {{ $blog->strong_tag == 'no' ? 'selected' : '' }} value="no">No</option>
                        </select>
                    
                    </div>

                     <div class="col-md-4">
                            <div class="form-check form-check-inline mt-4">
                             <label class="form-check-label" for="priceApprove">No Follow</label>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" {{ $blog->no_follow == '1' ? 'checked' : ''}}  name="no_follow" value="1">
                                <label for="css">Yes</label><br>
                            </div>

                            <div class="col-md-6">
                                <input class="form-check-input" type="radio"  {{ $blog->no_follow == '0' ? 'checked' : ''}} name="no_follow" value="0">
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
                                <div class="col-md-5"> <button type="button" data-toggle="modal" data-target="#EditsocialShare" class="btn btn-primary custom-button">Social Share</button></div>
                                 <div class="col-md-5"> <button type="button" data-toggle="modal" data-target="#edit_google_bingo" class="btn btn-primary custom-button">Google And Bing</button></div>
                            </div>
                           
                           
                            </div>
                            <div class='col-md-4'>
                                    <label class="form-label">Publish Blog Date</label>
                                    <div class='input-group date' id='edit-blog-datetime'>
                                            <input type='date' class="form-control" name="publish_blog_date" value="{{!empty($blog->publish_blog_date) ? date('Y-m-d', strtotime($blog->publish_blog_date)): '' }}" />
                                           

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
                                    <div class='input-group date' id='edit_date'>
                                            <input type='date' class="form-control" name="date" value="{{!empty($blog->date) ? date('Y-m-d', strtotime($blog->date)): '' }}" />

                                    </div>
                            </div>

                             <div class="col-md-4">
                                  <label class="form-label">Canonical URL</label>
                                  <br>
                                  <input  name="canonical_url" type="text" name="canonical_url" value="{{$blog->canonical_url}}" class="form-control">
                        
                                  </div>
                                  <div class="col-md-4">
                                 
                                  <label class="form-label">CheckMobile Friendliness</label>

                                    <select name="checkmobile_friendliness" class="form-control">
                                        <option value="yes" {{ $blog->checkmobile_friendliness == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option {{ $blog->checkmobile_friendliness == 'no' ? 'selected' : '' }} value="no">No</option>
                                    </select>
                                 
                        
                                  </div>

                              
                </div> 
                           
                <div class="row mt-3">
                                

                               
                    <div class="col-md-4">
                   
                    <label class="form-label">Select Website</label>
                    <select class="browser-default custom-select"  required="required" name="store_website_id" style="height:auto">
                      <option disabled value="" selected>---Selecty store websites---</option>
                      @foreach($store_website as $sw)
                          <option value="{{ $sw->id }}" {{ $sw->id == $blog->store_website_id ? 'selected' : '' }} >{{$sw->website}}</option>
                      @endforeach
                  </select>
                   
          
                    </div>


                    
      </div> 
                <hr>
                <div class="row mt-3">
                    <div class="col-md-12">
                          <button type="button" class="btn btn-secondary custom-button" data-dismiss="modal">Close</button>
                        <button type="submit" class="pull-right btn btn-success btn-rounded btn-lg" id="UpdateBlogdata">Update Blog</button>

                    </div>
                </div>


                <!-- Edit Content Added -->
            <div class="modal fade" id="EditContentModal"  role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
                  <div class="modal-dialog" role="document">
                        <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title">Content</h5>
                                      
                                  </div>
                                  <div class="modal-body">
                                           <input type="text" name="content" value="{{$blog->content}}" id="editcontent"> 
                                        

                                                 
                                  </div>     
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" id="EditContentModalCloseButton">Cancel</button>
                                      <button type="button" class="btn btn-success"  id="EditContentModalClose">Add</button>

                                  </div>
                        </div>
                  </div>
          
            </div>  


                <!-- Social Share -->
                <div class="modal fade" id="EditsocialShare" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
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
                                     <div class='input-group date' id='edit_facebook_date'>
                                    <input type='date' class="form-control" name="facebook_date" value="{{!empty($blog->facebook_date) ? date('Y-m-d', strtotime($blog->facebook_date)): '' }}" />

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
                                    <input type='text' name="instagram" class="form-control" value="{{$blog->instagram}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='edit_instagram_date'>
                                    <input type='date' class="form-control" name="instagram_date" value="{{!empty($blog->instagram_date) ? date('Y-m-d', strtotime($blog->instagram_date)): '' }}" />

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
                                    <input type='text' name="twitter" class="form-control" value="{{$blog->twitter}}" />
                                </div>
                                 <div class="col-md-5">
                                     <div class='input-group date' id='edit_twitter_date'>
                                    <input type='date' class="form-control" name="twitter_date" value="{{!empty($blog->twitter_date) ? date('Y-m-d', strtotime($blog->twitter_date)): '' }}" />

                                    </div>

                                </div>
                            </div>
                           
                    </div>       
            </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="EditSharemodalClose">Close</button>
                        <button type="button" class="btn btn-success" id="EditSharemodalClose" >Add</button>
 
                    </div>
                </div>
            </div>
        </div>


            <!-- Google And Bing -->
            <div class="modal fade" id="edit_google_bingo" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-rowid="">
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
                                                    <option value="yes" {{ $blog->google == 'yes' ? 'selected' : '' }}>Yes</option>
                                                    <option {{ $blog->google == 'no' ? 'selected' : '' }} value="no">No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <div class='input-group date' id='edit_google_date'>
                                                <input type='date' class="form-control" name="google_date" value="{{!empty($blog->google_date) ? date('Y-m-d', strtotime($blog->google_date)): '' }}" />

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
                                                <select name="bing" class="form-control">
                                                    <option value="yes" {{ $blog->bing == 'yes' ? 'selected' : '' }}>Yes</option>
                                                    <option {{ $blog->bing == 'no' ? 'selected' : '' }} value="no">No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <div class='input-group date' id='edit_bing_date'>
                                                <input type='date' class="form-control" name="bing_date" value="{{!empty($blog->bingo_date) ? date('Y-m-d', strtotime($blog->bingo_date)): '' }}" />

                                                </div>

                                            </div>
                                        </div>
                                    
                                    
                                </div>       
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="EditGoogleBingo">Cancel</button>
                            <button type="button" class="btn btn-success"  id="EditGoogleBingo">Add</button>

                        </div>
                    </div>
                </div>
            </div>


            </form>