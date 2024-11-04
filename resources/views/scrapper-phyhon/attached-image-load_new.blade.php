
            @foreach ($images  as $item)
           
                <tr class="expand-{{ $item->id }}">
                    <td>{{ \Carbon\Carbon::parse($item->date_created_at)->format('d-m-y') }}</td>
                    <?php /*<td class="expand-row-msg" data-name="storeWebsite" data-id="{{$store->id}}">
                        <span class="show-short-storeWebsite-{{$store->id}}">{{ str_limit($list->storeWebsite->website, 30, '..')}}</span>
                        <span style="word-break:break-all;" class="show-full-storeWebsite-{{$store->id}} hidden">{{ $list->storeWebsite->website ?? '' }}</span>
                    </td>*/?>
                    <!-- <td>{{ $item->id }} </td> -->
                    <td>{{ isset($storewebsiteUrls[$item->store_website])?$storewebsiteUrls[$item->store_website]:'' }}</td>
                    <td>{{$item->store_name}}</td>
                    <td>{{ $item->lang }}({{ $item->website_id }})</td>
                    
                        <td> @if($item->desktop > 0) {{ $item->desktop }} @else {{ '0' }} @endif</td>
                        <td>@if($item->mobile > 0) {{ $item->mobile }} @else {{ '0' }} @endif</td>
                        <td>@if($item->tablet > 0) {{ $item->tablet }} @else {{ '0' }} @endif</td>
                        <td>
                             <span class="btn p-0"> <input type="checkbox" class="defaultInput" {{ $item->website_stores_default ? 'checked' : '' }}
                                onclick="setStoreAsDefault(this)" data-website-id="{{ $item->website_table_id }}"
                                data-store-id="{{ $item->website_stores_id }}" /></span> 
                        </td>
                        <td>
                             <span class="btn p-0"> <input type="checkbox" class="defaultInput {{ $item->website_stores_flag }}" {{ $item->website_stores_flag ? 'checked' : '' }}
                                onclick="setStoreAsFlag(this)" data-website-id="{{ $item->website_table_id }}"
                                data-store-id="{{ $item->website_stores_id }}" /></span> 
                        </td>
                    <td>
                        <!-- <button data-website={{ $list->storeWebsite->website ?? '' }} type="button" class="btn btn-xs btn-image scrapper-python-modal" title="Scrapper action" data-toggle="modal" data-target="#scrapper-python-modal">
                            <img src="/images/add.png" alt="" style="cursor: pointer">
                        </button> -->

                         <!-- <button data-url="{{ route('scrapper.image.urlList', ['id' => $item->website_stores_id,'web_id' => $item->website_store_views_id,'code' => $item->website_id, 'startDate' => $item->date_created_at, 'endDate' => $item->date_created_at ]) }}" title="Open Urls"
                            type="button" class="btn show-scrape-images btn-image no-pd"
                            data-suggestedproductid="{{ $item->website_stores_id }}">
                            <img src="{{config('app.url')}}/images/view.png" style="cursor: default;">
                        </button> -->

                         {{-- <button data-url="{{ route('scrapper.phyhon.listImages', ['id' => $item->website_stores_id,'web_id' => $item->website_store_views_id,'code' => $item->website_id, 'startDate' => $item->date_created_at, 'endDate' => $item->date_created_at ]) }}" title="Open Images"
                            type="button" class="btn show-scrape-images btn-image no-pd"
                            data-suggestedproductid="{{ $item->website_stores_id }}"> --}}
                            {{-- @dd($item->store_website) --}}
                            <button data-url="{{ route('scrapper.phyhon.listImages', ['id' => $item->store_website,'web_id' => $item->id,'code' => $item->website_id, 'startDate' => $item->date_created_at, 'endDate' => $item->date_created_at ]) }}" title="Open Images"
                                type="button" class="btn show-scrape-images btn-image no-pd"
                                data-suggestedproductid="{{ $item->website_stores_id }}">
                            <img src="{{config('app.url')}}/images/forward.png" style="cursor: default;">
                        </button> 

                   

    
                       
                       
                </tr>

            @endforeach
            




<!-- Modal -->
<div class="modal fade" id="scrapper-python-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="scrapper-python-title"> Site scrapper action </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">



                <form action="" method="POST" id="scrapper-python-form">
                    @csrf

                    <div class="d-flex justify-content-between mt-2 mb-4">
                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="name" id="start" value="start" checked>
                                <label class="form-check-label" for="start">
                                    Start
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" id="desktop" value="desktop" checked>
                                <label class="form-check-label" for="desktop">
                                    Desktop
                                </label>
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="name" id="stop" value="stop">
                                <label class="form-check-label" for="stop">
                                    Stop
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" id="mobile" value="mobile">
                                <label class="form-check-label" for="mobile">
                                    Mobile
                                </label>
                            </div>

                        </div>

                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="name" id="get-status" value="get-status">
                                <label class="form-check-label" for="get-status">
                                    Get status
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" id="tablet" value="tablet">
                                <label class="form-check-label" for="tablet">
                                    Tablet
                                </label>
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="is_flag" id="is_flag" value="is_flag">
                                <label class="form-check-label" for="is_flag">
                                    Is Flag
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Send request</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<script>
    let websiteName = null;

    $(document).on('click', '.scrapper-python-modal', function(e) {

        websiteName = $(this).data('website').replace('www.', '').replace('.com', '')

    })
    $(document).on("change","#store_website", function(e) {
        $('#store_website').removeClass('c-error');
    });


    $(document).on('submit', '#scrapper-python-form', function(e) {

        e.preventDefault()

        $this = $(this)
        const formData = $this.serialize()

        const store_website = $('#store_website').val();
        const websiteName_lowercase = store_website.toLowerCase();
        const websiteName = websiteName_lowercase.replace('www.', '').replace('.com', '');

        const typeOfData = $('input[name="type"]:checked').val();
        const nameOfData = $('input[name="name"]:checked').val();

        // console.log("+++++++++++++++++++++++++>>>");
        // console.log(store_website);
        console.log(websiteName, 'aaaaaaaaa')

        if(store_website!=""){

        $.ajax({
            type: 'POST',
            url: "{{route('scrapper.call')}}",
            beforeSend: function() {
                $("#loading-image").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                webName: websiteName,
                type: typeOfData,
                data_name: nameOfData,
                is_flag: $('input[name="is_flag"]:checked').val()
            },
            dataType: "json"
        }).done(function(response) {
            $("#loading-image").hide();
            if (response.message) {
                toastr['success'](response.message, 'success');
            } else {
                toastr['error'](response.err, 'error');
            }
            $('#scrapper-python-modal').modal('hide')
        }).fail(function(response) {
            $("#loading-image").hide();
            $('#scrapper-python-modal').modal('hide')

            console.log("Sorry, something went wrong");
        });
        }else{
            toastr['error']("Please select store website", 'error');
            $('#store_website').addClass('c-error');
        }

    })
</script>