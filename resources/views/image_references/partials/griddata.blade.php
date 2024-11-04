@foreach($products as $product)
    <div>
        @php
            $productModel = $product->product;
        @endphp
        <td><input type="checkbox" name="issue" value="{{ $product->id }}" class="checkBox" data-id="{{ $product->product_id }}">
            {{ $product->id }}</td>
        <td>{{ $product->product_id  }} <br><b> {{ (isset($productModel) && $productModel->status_id == 42 ) ? 'Auto reject' : null }}  </b></td>
        <td>@if($productModel) @if (isset($productModel->product_category)) {{ $productModel->product_category->title }} @endif @endif</td>
        <td>@if($productModel) {{ $productModel->supplier }} @endif</td>
        <td>@if($productModel)  @if ($productModel->brands) {{ $productModel->brands->name }} @endif @endif</td>
        @php
            $websites = [];
            if($productModel) {
               $listofWebsite = $productModel->getWebsites();
               if(!$listofWebsite->isEmpty()) {
                    foreach($listofWebsite as $lw) {
                        $websites[] = $lw->title;
                    }
               }
            }
        @endphp
        <td>{!! implode("</br>",$websites) !!}</td>

        <td>
            <div style="width: 100px;margin-top: 25px; display: inline-block;">
{{--                <img src="{{ $product->media ? getMediaUrl($product->media) : '' }}" alt="" height="100" width="100" onclick="bigImg('{{ $product->media ? getMediaUrl($product->media) : '' }}')">--}}
                <img src="{{ $product->media ? getMediaUrl($product->media) : 'http://localhost/erp/public/uploads/product/29/296559/123.webp' }}" alt="" height="100" width="100" onclick="bigImg('{{ getMediaUrl($product->media ? $product->media) : '' }}')">
            </div>
        <td>
        @if($product->newMedia)
            <table class="table-striped table-bordered table" id="log-table">
                <tbody>
                <tr>
            @foreach($product->differentWebsiteImages as $images)
                <td>
                    <div style="width: 100px;margin: 0px;display: inline-block;">
                        {{ ($images->newMedia) ? $images->getDifferentWebsiteName($images->newMedia->id) : "N/A" }}
{{--                        <img src="{{ $images->newMedia ? getMediaUrl($images->newMedia) : '' }}" alt="" height="100" width="100" onclick="bigImg('{{ $images->newMedia ? getMediaUrl($images->newMedia) : '' }}')">--}}
                        <img src="{{ $images->newMedia ? "http://localhost/erp/public/uploads/product/29/296559/123.webp" : '' }}" alt="" height="100" width="100" onclick="bigImg('{{ $images->newMedia ? getMediaUrl($images->newMedia) : '' }}')">
                    </div>
                </td>
            @endforeach
                </tr>
                </tbody>
            </table>
        @endif
        </td>
        <td>{{ number_format((float)str_replace('0:00:','',$product->speed), 4, '.', '') }} sec</td>
        <td>{{ $product->updated_at->format('d-m-Y : H:i:s') }}</td>
        <td><select class="form-control-sm form-control reject-cropping bg-secondary text-light" name="reject_cropping" data-id="{{ $product->product_id }}">
                    <option value="0">Reject Product</option>
                    <option value="Images Not Cropped Correctly">Images Not Cropped Correctly</option>
                    <option value="No Images Shown">No Images Shown</option>
                    <option value="Grid Not Shown">Grid Not Shown</option>
                    <option value="Blurry Image">Blurry Image</option>
                    <option value="First Image Not Available">First Image Not Available</option>
                    <option value="Dimension Not Available">Dimension Not Available</option>
                    <option value="Wrong Grid Showing For Category">Wrong Grid Showing For Category</option>
                    <option value="Incorrect Category">Incorrect Category</option>
                    <option value="Only One Image Available">Only One Image Available</option>
                    <option value="Image incorrect">Image incorrect</option>
            </select>

            <button 
                style="float:right;padding-right:0px;" 
                type="button" 
                class="btn btn-xs show-http-status" 
                title="Http Status" 
                data-toggle="modal" data-target="#show-http-status"
                data-request="{{ $product->httpRequestData ? $product->httpRequestData->response : 'N/A' }}"
                data-response="{{ $product->httpRequestData ? $product->httpRequestData->requestData : 'N/A' }}"
                >
                <i class="fa fa-info-circle"></i>
            </button>

        </td>
        <td>{!! $product->getProductIssueStatus($product->id) !!}</td>

    </tr>
@endforeach