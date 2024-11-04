<!-- <div id="gtmetrix-stats-modal" class="modal fade" >
    <div class="modal-dialog modal-md model-width w-100">
      <div class="modal-content" style="width: 100%;"> -->
        <div class="modal-header">
          <h4 class="modal-title">{{$title}}</span></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class ="row">
            <?php 
             if(array_key_exists("type",$g_typeData))
              {
                if($g_typeData['type'] == 'PageSpeed')
                {
            ?>
                <div class =col-md-12>
                  <h4 class="p-3 m-0 text-center bg-dark text-white">{{$g_typeData['type']}}</h4>
                    <table class="table fixed_header table-responsive" id="latest-remark-records">
                      <thead class="thead-dark">
                        <tr>
                          <th class="w-100">Recommendation</th>
                          <th class="w-100">Impact</th>
                          <th class="w-100">Grade</th>
                        </tr>
                      </thead>
                      <tbody class="show-list-records" >
                        @if(!empty($pagespeedData))
                        <?php $price = array_column($pagespeedData, 'score');
                        array_multisort($price, SORT_ASC, $pagespeedData);
                        ?>
                          @foreach ($pagespeedData  as $statsdata)
                            <tr>
                              <td class="w-100">{{$statsdata['name']}}</td>
                              <td class="w-100">{{$statsdata['impact']}}</td>
                             
                              <td class="w-100"> 
                              @php $color="white" @endphp
                              @if($statsdata['score'] >= 89)
                              @php $color = 'bg-success' ; @endphp
                              @endif
                              @if($statsdata['score'] <= 48)
                              @php $color = 'bg-danger' ; @endphp
                              @endif
                              @if($statsdata['score'] <= 60 && $statsdata['score'] >= 48 )
                              @php $color = 'bg-warning' ; @endphp
                              @endif
                              <div class="progress">
                                <div class="progress-bar {{$color}} progress-bar-striped " role="progressbar" style="width: {{$statsdata['score']}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$statsdata['score']}}%</div>
                              </div>
                              </td>
                          </tr>
                          @endforeach
                        @else
                          <tr><td colspan=2>No Results found !!</td></tr>
                        @endif
                      </tbody>
                    </table>
                  </div>

            <?php }}
             if(array_key_exists("type",$y_typeData))
              {
                if($y_typeData['type'] == 'YSlow')
                { ?>

                <div class =col-md-12>
                  <h4 class="p-3 m-0 text-center bg-dark text-white">{{$y_typeData['type']}}</h4>
                    <table class="table fixed_header table-responsive" id="latest-remark-records">
                      <thead class="thead-dark">
                        <tr>
                          <th class="w-100">Recommendation</th>
                          <th class="w-100">Grade</th>
                        </tr>
                      </thead>
                      <tbody class="show-list-records" >
                        @if(!empty($yslowData))
                        <?php $price = array_column($yslowData, 'score');
                        array_multisort($price, SORT_ASC, $yslowData);
                        ?>
                          @foreach ($yslowData  as $statsdata)
                            <tr>
                              <td class="w-100">{{$statsdata['name']}}</td>
                              <td class="w-100"> 
                              @php $color="white" @endphp
                              @if($statsdata['score'] >= 89)
                              @php $color = 'bg-success' ; @endphp
                              @endif
                              @if($statsdata['score'] <= 48)
                              @php $color = 'bg-danger' ; @endphp
                              @endif
                              @if($statsdata['score'] <= 60 && $statsdata['score'] >= 48 )
                              @php $color = 'bg-warning' ; @endphp
                              @endif
                              <div class="progress">
                                <div class="progress-bar {{$color}} progress-bar-striped " role="progressbar" style="width: {{$statsdata['score']}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$statsdata['score']}}%</div>
                              </div>
                              </td>
                          </tr>
                          @endforeach
                        @else
                          <tr><td colspan=2>No Results found !!</td></tr>
                        @endif
                      </tbody>
                    </table>
                  </div>

                <?php    
                }
              } ?>

            
            <?php 
             if(array_key_exists("type",$InsightTypeData))
              {
                if($InsightTypeData['type'] == 'PageSpeed Insight')
                {
            ?>
                  <div class ="col-lg-12">
                  <h4 class="p-3 m-0 text-center bg-dark text-white">{{$InsightTypeData['type']}}</h4>
                  <div class="table-responsive">
                    <table class="table fixed_header " id="latest-remark-records">
                      <thead class="thead-dark">
                        <tr>
                          <th class="w-100">Recommendation</th>
                          <th class="w-100">Display Mode</th>
                          <th class="w-100">Numeric Value</th>
                          <th class="w-100">Numeric Unit</th>
                          <th class="w-100">Grade</th>
                        </tr>
                      </thead>
                      <tbody class="show-list-records" >
                        @if(!empty($Insightdata))
                          @foreach ($Insightdata  as $statsdata)
                            <tr>
                              <td class="w-100">{{$statsdata['name']}}</td>
                              <td class="w-100">{{$statsdata['scoreDisplayMode']}}</td>
                              <td class="w-100">{{$statsdata['numericValue']}}</td>
                              <td class="w-100">{{$statsdata['numericUnit']}}</td>
                              <td class="w-100">{{$statsdata['score']}}</td>
                          </tr>
                          @endforeach
                        @else
                          <tr><td colspan=3>No Results found !!</td></tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                    
                  </div>
            <?php 
                }
              } 
            ?>
          </div>
        </div>
      <!-- </div>
    </div>
</div> -->