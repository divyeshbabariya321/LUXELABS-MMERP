<!-- <div id="gtmetrix-history-modal" class="modal fade" >
    <div class="modal-dialog modal-xl model-width w-100">
      <div class="modal-content" style="width: 100%;"> -->
        <div class="modal-header">
          <h4 class="modal-title">history</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <table class="table fixed_header table-responsive" id="latest-remark-records">
            <thead class="thead-dark">
              <tr>
                <th>Store view id</th>
                <th>Test id</th>
                <th>Status</th>
                <th>Error</th>
                <th>Website</th>
                <th>Report URL</th>
                <th>Html load time</th>
                <th>Html bytes</th>
                <th>Page load time</th>
                <th>Page bytes</th>
                <th>Page elements</th>
                <th>Pagespeed score</th>
                <th>Yslow score</th>
                <th style="width: 12%;">Resources</th>
                <th style="width: 7.5%;">Date</th>
              </tr>
            </thead>
            <tbody class="show-list-records" >
            @if(!empty($history))
            
                @foreach ($history as $data)
                @if(!empty($data['test_id']))
                  <tr>
                    <td>{{$data['store_view_id']}}</td>
                    <td>{{$data['test_id']}}</td>
                    <td>{{$data['status']}}</td>
                    <td>{{$data['error']}}</td>
                    <td><a href="{{$data['website_url']}}" target="_blank" title="Goto website"> Website </a></td>
                    <td><a href="{{$data['report_url']}}" target="_blank" title="Show report"></td>
                    <td>{{$data['html_load_time']}}</td>
                    <td>{{$data['html_bytes']}}</td>
                    <td>{{$data['page_load_time']}}</td>
                    <td>{{$data['page_bytes']}}</td>
                    <td>{{$data['page_elements']}}</td>
                    <td>{{$data['pagespeed_score']}}</td>
                    <td>{{$data['yslow_score']}}</td>
                    <td>
                    @if(!empty($data['resources']) && is_array($data['resources']))
                      <ul>
                        @foreach ($data['resources'] as $keyresource => $resource)
                          <li> <a href="{{$resource}}" target="_blank" > {{$keyresource}} </a> </li>
                        @endforeach
                      </ul>
                    @else
                      
                    @endif
                    </td>
                    
                  
                    
                    <td>{{$data['created_at']}}</td>
                </tr>
                @endif
                @endforeach
              @else
                <tr><td colspan=2>No Results found !!</td></tr>
              @endif
            </tbody>
          </table>
          @if(!empty($history))
        {{ $history->links() }}
        @endif
        </div>
        
      <!-- </div>
    </div>
</div> -->
