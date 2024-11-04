 @if($todolists->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
        @else

          @foreach ($todolists as $todolist)

            <tr style="background-color: {{$todolist->color?->color}}";>
			        <td>{{ $todolist->id ?? '' }}</td>
             <td>{{ $todolist->title ?? '' }}</td>
             <td>{{ $todolist->subject ?? '' }}</td>              
             <td>
                <select name="status" id="status" class="form-control" onchange="todoCategoryChange({{$todolist->id}}, this.value)" data-id="{{$todolist->id}}">
                    <option>--Select--</option>
                    @foreach ($todoCategories as $todoCategory )
                       <option value="{{$todoCategory->id}}" @if ($todolist->todo_category_id == $todoCategory->id) selected @endif>{{$todoCategory->name}}</option>
                    @endforeach
                </select>
              </td>
              <td>
                <select name="status" id="status" class="form-control" onchange="statusChange({{$todolist->id}}, this.value)" data-id="{{$todolist->id}}">
                  <option>--Select--</option>
                  @foreach ($statuses as $status )
                    <option value="{{$status['id']}}" @if ($todolist->status == $status['id']) selected @endif>{{$status['name']}}</option>
                    @endforeach
                </select>
              </td>
              <td>{{ $todolist->todo_date ?? '' }}</td>
              <!-- <td>{{ $todolist->remark ?? '' }}</td> -->
              <td>
                <div class="row">
                    <div class="col-md-11 form-inline cls_remove_rightpadding">
                        <div class="d-flex cls_textarea_subbox" style="justify-content: space-between;">
                            
                            <textarea rows="1" class="form-control mr-1" id="remarks_{{ $todolist->id }}" name="remarks" placeholder="Message"></textarea>
                     
                            <button class="btn btn-sm btn-xs remarks-message mr-1" data-todolistid="{{ $todolist->id }}"><i class="fa fa-paper-plane"></i></button>

                            <a onclick="getRemarkHistoryData({{ $todolist->id }})" class="btn" title="Remark history"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                            
                        </div>
                    </div>
                </div>                
            </td>
              <td>
                <a class="btn btn-image" onclick="changetodolist({{ $todolist->id }})" ><img src="{{asset('/images/edit.png')}}" style="cursor: nwse-resize; width: 14px !important;"></a>
                
                {{ html()->form('DELETE', route('todolist.destroy', [$todolist->id]))->style('display:inline')->open() }}
                <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="{{asset('/images/delete.png')}}" /></button>
                {{ html()->form()->close() }}
              </td>
            </tr>


          @endforeach

          @endif
