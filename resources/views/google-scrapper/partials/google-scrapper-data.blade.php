@foreach($contents as $content) 
    <tr>
        
        <td>
            <input    type="checkbox" 
                      class="selectedInfluencers" 
                      name="selectedInfluencers" 
                      value="">
         </td>

         <td style="white-space: nowrap;">
          {{ isset($content->title) ? $content->title : "" }} 
        </td>
	      
        <td style="white-space: nowrap;">
          {{ isset($content->date) ? date('d-m-y', strtotime($content->date)) : "" }}
        </td>

        <td style="white-space: nowrap;">
          <img src='{{ isset($content->image) ? $content->image : "" }}' width="100px" height="100px" > 
        </td>

        <td>
            <a href="{{$content->url}}" target="_blank">
             {{ isset($content->url) ? $content->url : "" }}
            </a>
        </td>

        <td class="expand-row-msg" data-name="email" data-id="{{--$content->email--}}">
          {{ isset($content->email) ? $content->email : "" }}
        </td>

        <td>
         {{ isset($content->number) ? $content->number : "" }}
        </td>

        <td>
        {{ isset($content->about_us) ? $content->about_us : "" }}
        </td>

        <td>
          <div class="row" style="margin-bottom:8px">
            <div class="col-md-12">
                <select class="form-control account-search-{{--$influencer->id--}} select2" name="account_id" data-placeholder="Sender...">
                    <option value="">Select sender...</option>
                    
                      <option value="{{-- $key --}}" {{-- isset($thread) && $thread->account_id == $key ? 'selected' : '' --}}>{{-- $account --}}</option>
                    
                </select>
            </div>
          </div>
        </td>

        <td>
		        <div class="d-flex" style="flex-direction: column;">
              
                   <div class="typing-indicator" id="typing-indicator"> 
                       
                    </div>
            <div class="row">

              <div class="col-md-12 form-inline d-flex ">
                  <textarea placeholder="Message..." style="width: 110px;
                  /* border: none; */
                  padding: 2px;
                  height: 30px;
                   border-radius: 5px;" name="" class="quick-message-field" 
                            id="message{{-- $influencer->id --}}"></textarea>
                 <input type="hidden" id="message-id" name="message-id" />
                  <a style="margin-right: -16px;
                  margin-left: 18px;" class="btn btn-sm btn-image send-message" href="javascript:void(0)">
                    <span class="send_btn" data-id="{{--$influencer->id--}}">
                      <img src="{{--asset('images/filled-sent.png')--}}"/>
                    </span>
                  </a>
                  <a class="btn btn-image delete_quick_comment">
                    <img src="<?php 
                    //echo url('/');
                    ?>/images/delete.png" style="cursor: default; width: 16px;">
                  </a>
              </div>

              <div class="col-md-4"  style="margin-top:10px" >
              
                
  
              
                          <a href="{{--route('attachImages', ['direct', @$thread->id, 1]) .'?'.http_build_query(['return_url' => 'instagram/influencers'])--}}" 
                             class="btn btn-image px-1">
                            <img src="{{--asset('images/attach.png')--}}"/>
                          </a>
                
                  <button type="button" class="btn btn-xs btn-image load-direct-chat-model" data-object="direct" data-id="{{-- $thread->id  --}}" title="Load messages"><img src="{{--asset('images/chat.png')--}}" alt=""></button>
                
              </div>

              <div class="col-md-3 " tyle="padding:0px;">
                  <div class="input-group-append">
                     
                      	<a href="{{--route('attachImages', ['direct', @$thread->id, 1]) .'?'.http_build_query(['return_url' => 'instagram/influencers'])--}}" 
                           class="btn btn-image px-1">
                          <img src="{{--asset('images/attach.png')--}}"/>
                        </a>
                      
                     <a class="btn btn-image " href="javascript:;"><span class="send_btn" data-id="{{--$influencer->id--}}"><img src="{{--asset('images/filled-sent.png')--}}"/></span></a>
                     
                      	<button type="button" class="btn btn-xs btn-image load-direct-chat-model" data-object="direct" data-id="{{-- $thread->id  --}}" title="Load messages"><img src="{{--asset('images/chat.png')--}}" alt=""></button>
                     
                  </div>
              </div>   

            </div>

	  	</div>
    </td>
    
    <td>
        <select style="border:1px;padding:2px" name="quickComment">
          <option  data-vendorid="{{-- $influencer->id --}}"  value="">Auto Reply</option>
          <?php
          // foreach ($replies ?? [] as $key_r => $value_r) { 
            ?>
             <option title="<?php 
             //echo $value_r;
             ?>" data-vendorid="{{-- $influencer->id --}}" value="<?php 
             //echo $key_r;
             ?>">
              <?php
          //     $reply_msg = strlen($value_r) > 12 ? substr($value_r, 0, 12) : $value_r;
          //     echo $reply_msg;
          //     ?>
             </option>
           <?php //}
          ?>
        </select>
    </td> 

      <td>
       {{-- $influencer->facebook --}} 
      </td>
      <td>
      {{-- $influencer->instagram --}} 
      </td>
      
   
    <td>
      <div class="d-flex">

        <button title="Retrieve latest post and comment." 
                class="btn btn-image latest-post pd-2 action-icon" data-id="{{-- $influencer->id --}}">
          <img src="{{asset('images/add.png')}}"/>
        </button>
            
        <button class="btn btn-image expand-row-btn pd-2 action-icon" 
                data-id="{{-- $influencer->id --}}">
                <img src="{{asset('images/forward.png')}}">
        </button>
        
          <a href="{{--$url--}}" target="_blank" class="btn" 
                data-id="{{-- $influencer->id --}}">
                <i class="fa fa-picture-o"></i>
          </a>
        
      </div>
    </td>
</tr>
@endforeach


<tr class="dis-none" id="expand-{{-- $influencer->id --}}">

  <td colspan="6">
 
  <div class="row">
  
    &nbsp;<img style="width:75px;height:75px;" src="{{--$m->url--}}" alt="">&nbsp;&nbsp;
    
  </div>
  
  </td>
  <td colspan="5">
    
    <p>Latest comment : {{--$influencer->comment--}} by {{--$influencer->comment_user_full_name--}} on {{--date('d-m-Y', strtotime($influencer->comment_posted_at))--}}
    
  </td>
</tr> 






<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modal-body-content">
     
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<script>
  function showModal( body , name ){
      $('#exampleModalLabel').html( name );
      $('#modal-body-content').html( body );
      $('#exampleModal').modal();
  }
</script>