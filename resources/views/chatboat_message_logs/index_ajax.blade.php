
             @foreach($logListMagentos as $item)
          <tr>
		   {{ html()->form('POST', url('pushwaston'))->id('form_' . $item->id)->open() }}
                  <td>
                    <a class="show-product-information" data-id="{{ $item->id }}" href="/products/{{ $item->id }}" target="__blank">{{ $item->id }}</a>
                  </td>
                  <td> {{$item->cname}} </td>
                  
                  
                 
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    @php
                         $message=$item->message;
                         $msg=json_decode($message);

                         if ($msg)
                         {
                           if ($msg->message)
                             $message=$msg->message;
                             
                         }
                    @endphp
                    <span class="show-short-message-{{$item->id}}">{{ Str::limit($message, 6, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$message}}</span>
                  </td>
                  <td> <select name="keyword_or_question" id="" class="form-control view_details_div">
                <option value="intent">Intent</option>
                <option value="entity">Entity</option>
                <option value="simple">Simple Text</option>
                <option value="priority-customer">Priority Customer</option>
            </select> </td>
            <td> 
            <input type="text" name="value"  placeholder="Enter your value" required>
			<input type="hidden" name="log_id" value="{{$item->id}}" >
           </td>
           <td> 
            <input type="text" name="suggested_reply"  placeholder="Suggested Reply" required>
           </td>
           
                  <td>  <select name="category_id" id="" class="form-control">
                <option value="">Select</option>
                @foreach($allCategoryList as $cat)
                    <option value="{{$cat['id']}}">{{$cat['text']}}</option>
                @endforeach
            </select> </td>
                 
                  
                  <td> {{$item->status}} </td>
                  <td> <select name="erp_or_watson" id="" class="form-control">
                <option value="watson">Watson</option>
                <option value="erp">ERP</option>
            </select> </td>
            
                  <td> <select name="watson_account" class="form-control" required>
                <option value="0">All account </option>
                @if(!empty($watson_accounts))
                    @foreach($watson_accounts as $acc)
                        <option value="{{$acc->id}}" > {{$acc->id}} - {{$acc->storeWebsite->title}}</option>
                    @endforeach
                @endif
            </select></td>
                  
                  <td>
                    @if(isset($item->updated_at))
                      {{ date('M d, Y',strtotime($item->updated_at))}}
                    @endif
                  </td>
                  
                  <td style="padding: 1px 7px"> 
                    <button class="btn btn-xs btn-none-border chatbot-log-list" data-id="{{$item->id}}"><i class="fa fa-eye"></i></button>
                    <button type="button" onclick="submitForm('{{$item->id}}')"><i class="fa fa-save"></i></button> 
                  </td>
                  </form>       
                </tr>
              @endforeach()
           