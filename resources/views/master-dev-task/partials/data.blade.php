<div class="row">
  <div class="col-md-12">
  <div class="table-responsive">
  <table class="table table-bordered" id="master-table1">
      <thead>
        <tr>
          <th width="20%">Component</td>
          <th width="80%">Data</td>
        </tr>
      </thead>  
      <tbody>
          <tr>
            <td>Database</td>
            <td colspan="6">
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
              <table class="table table-bordered">
                  <tr>
                    <td>Current Size</td>
                    <td>Size Before</td>
                  </tr>
                  <tr>
                      <td>{{ ($currentSize) ? $currentSize->size : "N/A" }}</td>
                      <td>{{ ($sizeBefore) ? $sizeBefore->size : "N/A" }}</td>
                  </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td>Database Table</td>
            <td colspan="6">
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                  <tr>
                    <td>Table</td>
                    <td>Size</td>
                  </tr>
                  @if(!empty($topFiveTables))
                    @foreach($topFiveTables as $tft)
                      <tr>
                          <td>{{ $tft->database_name }}</td>
                          <td>{{ number_format($tft->size/1024,2,'.','') }}</td>
                      </tr>
                    @endforeach
                  @endif
              </table>
            </td>
          </tr>

          <tr>
            <td>Development</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
              <?php 
              if(!empty($repoArr)){
                foreach($repoArr as $k => $v){
                  $totalRequest = !empty($v['pulls']) ? count($v['pulls']) : 0;
                  if($totalRequest <= 0) {
                    unset($repoArr[$k]);
                  }
                }
                $max = ceil(count($repoArr) / 4); 
                $max = $max<=0 ? 1 : $max;
                $repoArrChunks = array_chunk($repoArr, $max);
                if(!empty($repoArrChunks)){
                  foreach($repoArrChunks as $repoArrChunk){
                    if(!empty($repoArrChunk)){
                    ?>
                    <table class="table table-bordered w-25 pull-left">
                      <tr>
                        <td>Repository</td>
                        <td>Open PR</td>
                      </tr>
                      <?php if(!empty($repoArrChunk)) { ?>
                        <?php foreach($repoArrChunk as $repo) { ?>
                        <?php $totalRequest = !empty($repo['pulls']) ? count($repo['pulls']) : 0 ?>
                        <?php if($totalRequest > 0) { ?>  
                            <tr>
                                <td>{{ !empty($repo['name']) ? $repo['name'] : "N/A" }}</td>
                                <td>{{ !empty($repo['pulls']) ? count($repo['pulls']) : 0 }}</td>
                            </tr>
                        <?php } ?>    
                        <?php } ?>  
                      <?php } ?>  
                    </table>
                    <?php
                  }}
                }
              }
              ?>
            </td>
          </tr>

          <tr>
            <td>Whatsapp</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                  <tr>
                    <td>Last 3 hours</td>
                    <td>Last 24 hours</td>
                  </tr>
                  <tr>
                      <td>{{ isset($last3HrsMsg) ? $last3HrsMsg->cnt : 0 }}</td>
                      <td>{{ isset($last24HrsMsg) ? $last24HrsMsg->cnt : 0 }}</td>
                  </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td>Crop Reports</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                  <tr>
                    <td>Last 1 hours</td>
                    <td>Last 24 hours</td>
                  </tr>
                  <tr>
                      <td>{{ !empty($scraper1hrsReports) ? $scraper1hrsReports->cnt : 0 }}</td>
                      <td>{{ !empty($scraper24hrsReports) ? $scraper24hrsReports->cnt : 0 }}</td>
                  </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td>Cron jobs</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
              <?php 
              if(!empty($cronjobReports)){
                $cronjobReports = $cronjobReports->toArray();
                $max = ceil(count($cronjobReports) / 2); 
                $max = $max<=0 ? 1 : $max;
                $cronjobReportsChunks = array_chunk($cronjobReports, $max);
                if(!empty($cronjobReportsChunks)){
                  foreach($cronjobReportsChunks as $cronjobReportsChunk){
                    if(!empty($cronjobReportsChunk)){
                    ?>
                    <table class="table table-bordered w-50 pull-left">
                      <tr>
                        <td>Signature</td>
                        <td>Start time</td>
                        <td>Last error</td>
                      </tr>
                      <?php if(!empty($cronjobReportsChunk)){ ?>
                          <?php foreach($cronjobReportsChunk as $cronLastError) { ?>
                            <tr>
                              <td width="25%">{{ $cronLastError['signature'] }}</td>
                              <td width="20%">{{ $cronLastError['start_time'] }}</td>
                              <td width="50%">{{ $cronLastError['last_error'] }}</td>
                            </tr>
                          <?php } ?>
                      <?php } ?>
                    </table>
                    <?php
                  }}
                }
              }
              ?>
            </td>
          </tr>

          <tr>
            <td>Scrap</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                  <tr>
                    <td>Total</td>
                    <td>Failed</td>
                    <td>Validated</td>
                    <td>Errors</td>
                  </tr>
                  <tr>
                      <td>{{ isset($scrapeData[0]->total) ? $scrapeData[0]->total : 0 }}</td>
                      <td>{{ isset($scrapeData[0]->failed) ? $scrapeData[0]->failed : 0 }}</td>
                      <td>{{ isset($scrapeData[0]->validated) ? $scrapeData[0]->validated : 0 }}</td>
                      <td>{{ isset($scrapeData[0]->errors) ? $scrapeData[0]->errors : 0 }}</td>
                  </tr>
              </table>
            </td>
          </tr>

			     <tr>
            <td>Jobs</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                  <tr>
                    <td>Last 3 hours</td>
                    <td>Last 24 hours</td>
                  </tr>
                  <tr>
                      <td>{{ isset($last3HrsJobs) ? $last3HrsJobs->cnt : 0 }}</td>
                      <td>{{ isset($last24HrsJobs) ? $last24HrsJobs->cnt : 0 }}</td>
                  </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td>Failed Jobs</td>
            <td colspan="6">
              
            </td>
          </tr>
          <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                  <tr>
                    <td>Name</td>
                    <td>Queue</td>
                    <td>Status</td>
                    <td>Failed at</td>
                  </tr>
                  @foreach($failedJobs as $fj) 
                    <tr>
                        <td>{{ $fj->name }}</td>
                        <td>{{ $fj->queue }}</td>
                        <td>{{ $fj->status }}</td>
                        <td>{{ date("Y-m-d H:i:s",$fj->failed_at) }}</td>
                    </tr>
                  @endforeach
                  
              </table>
            </td>
          </tr>

			<tr>
				<td>Project Directory Size Management</td>
				<td colspan="6">
					
				</td>
			</tr>
      <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
						<tr>
							<td>Directory Name</td>
							<td>Parent</td>
							<td>Size (In MB)</td>
							<td>Expected (In MB)</td>
						</tr>
						<tr>
							@foreach($projectDirectoryData as $val)
                <tr>
								<td>{{ isset($val->name) ? $val->name : "" }}</td>
								<td>{{ isset($val->parent) ? $val->parent : "" }}</td>
                <td>{{ $val->size }}</td>
								
								<td>{{ isset($val->notification_at) ? number_format($val->notification_at/1048576,0) : "" }}</td>
              </tr>
							@endforeach
						</tr>
					</table>
            </td>
          </tr>

        <tr>
            <td>Memory Usage</td>
            <td colspan="6">

                

            </td>
        </tr>
        <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                    <tr>
                        <td>Total</td>
                        <td>Used</td>
                        <td>Free</td>
                        <td>Buff & Cache</td>
                        <td>Available</td>
                    </tr>
                    <tr>

                        <td>{{ isset($memory_use) ? $memory_use->total  :  "" }}</td>
                        <td>{{ isset($memory_use) ? $memory_use->used : ""}}</td>
                        <td>{{ isset($memory_use) ? $memory_use->free : ""}}</td>
                        <td>{{ isset($memory_use) ? $memory_use->buff_cache : ""}}</td>
                        <td>{{ isset($memory_use) ? $memory_use->available : ""}}</td>

                    </tr>
                </table>
            </td>
          </tr>

        <tr>
            <td>API error</td>
            <td colspan="6">

                
            </td>
        </tr>
        <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                    <tr>
                        <td>Code</td>
                        <td>Total Error</td>
                    </tr>
                    @if(!empty($logRequest))
                      @foreach($logRequest as $lr)
                        <tr>
                            <td>{{ $lr->status_code}}</td>
                            <td>{{ $lr->total_error}}</td>
                        </tr>
                      @endforeach
                    @endif
                </table>
            </td>
          </tr>

        <tr>
            <td>More Than 24 Hr</td>
            <td colspan="6">

                
            </td>
        </tr>
        <tr>
            <td colspan="7" class="sub-table p-0">
            <?php 
              if(!empty($scraper_process)){
                $scraper_process = $scraper_process->toArray();
                $max = ceil(count($scraper_process) / 4); 
                $max = $max<=0 ? 1 : $max;
                $scraper_process_chunks = array_chunk($scraper_process, $max);
                if(!empty($scraper_process_chunks)){
                  foreach($scraper_process_chunks as $scraper_process_chunk){
                    if(!empty($scraper_process_chunk)){
                    ?>
                    <table class="table table-bordered w-25 pull-left">
                        <tr>
                            <td>No</td>
                            <td>Name</td>
                        </tr>
                        <?php 
                          if(!empty($scraper_process_chunk)){
                            foreach($scraper_process_chunk as $i => $lr){
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $lr['scraper_name']; ?></td>
                            </tr>
                        <?php 
                            } 
                          } 
                        ?>
                    </table>
                    <?php
                  }}
                }
              }
            ?>
            </td>
          </tr>

        <tr>
            <td>Not Run In Last 24 Hr</td>
            <td colspan="6">

                
            </td>
        </tr>
        <tr>
            <td colspan="7" class="sub-table p-0">
            <?php 
              if(!empty($scrapers)){
                $scrapers = $scrapers->toArray();
                $max = ceil(count($scrapers) / 4); 
                $max = $max<=0 ? 1 : $max;
                $scrapers_chunks = array_chunk($scrapers, $max);
                if(!empty($scrapers_chunks)){
                  foreach($scrapers_chunks as $scrapers_chunk){
                    if(!empty($scrapers_chunk)){
                    ?>
                    <table class="table table-bordered w-25 pull-left">
                        <tr>
                            <td>No</td>
                            <td>Name</td>
                        </tr>
                        <?php 
                          if(!empty($scrapers_chunk)){
                            foreach($scrapers_chunk as $i => $lr){
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $lr['scraper_name']; ?></td>
                            </tr>
                        <?php 
                            } 
                          } 
                        ?>
                    </table>
                    <?php
                  }}
                }
              }
            ?>
            </td>
          </tr>

         <tr>
            <td>Product push error log</td>
            <td colspan="6">

                
            </td>
        </tr>
        <tr>
            <td colspan="7" class="sub-table p-0">
            <table class="table table-bordered">
                    <tr>
                        <td>Count</td>
                        <td>Status</td>
                        <td>Message</td>
                    </tr>
                    @if(!empty($productErrors))
                      @foreach($productErrors as $i => $lr)
                        <tr>
                            <td>{{ $lr->count }}</td>
                            <td>{{ $lr->response_status }}</td>
                            <td>{{ $lr->message}}</td>
                        </tr>
                      @endforeach
                    @endif
                </table>
            </td>
          </tr>
       </tbody>
    </table>
</div>
  </div>
</div>