<?php
?>
@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h4 class="page-heading">
                <a href="{{ route('product.rejected.auto.cropped') }}">Show All</a> &nbsp; Rejected Cropped Image
            </h4>
        </div>
        <form action="{{url('products/auto-cropped/'.$product->id.'/approve-rejected')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="col-md-12 text-center">
                <button href="{{ url('products/auto-cropped/'.$product->id.'/approve-rejected') }}" type="button" class="btn btn-default">Approve</button>
                @if($secondProduct)
                    <a href="{{ url('products/auto-cropped/'.$secondProduct->id.'/show-rejected') }}">Next Image</a>
                @endif
            </div>
        </form>
        <div class="col-md-12">
            <table class="table table-striped table-bordered" style="width: 100%">
                <tr>
                    <td>
                        {{ $product->name }}
                        <br>
                        {{ $product->sku }}
                        <br>
                        <a href="{{ route('products.show', $product->id) }}" target="_new">{{ $product->id }}</a>
                        <br>
                        {{ $product->product_category->title }}
                        <br>
                        <a class="btn btn-secondary" href="{{ route('products.show', $product->id) }}">Product Details</a>
                    </td>
                    <td>
                        <p>Reject Remark : {{ $product->crop_remark ?? 'N/A' }}</p>
                        @if($product->is_image_processed)
                            <a class="btn btn-secondary btn-sm" href="{{ url('/download/crop-rejected/' . $product->id . '/cropped') }}">Download Cropped</a>
                        @endif
                        <br><br>
                        @if($originalMediaCount)
                            <a class="btn btn-secondary btn-sm" href="{{ url('/download/crop-rejected/' . $product->id . '/original') }}">Download Original</a>
                        @endif
                    </td>
                    <td>
                        <strong>Dimenson: {{$product->lmeasurement }} X {{ $product->hmeasurement }} X {{ $product->dmeasurement }}</strong>
                       
                        <br><strong>Dimension: {{round($product->lmeasurement*0.393701)}} X {{round($product->hmeasurement*0.393701)}} X {{round($product->dmeasurement*0.393701)}}</strong>
                    <td>
                        <form method="post" action="{{ url('products/auto-cropped/'.$product->id.'/approve-rejected') }}" enctype="multipart/form-data">
                            @csrf
{{--                            <div class="form-group">--}}
{{--                                <label for="images">Images</label>--}}
{{--                                <input type="file" name="images[]" id="images" multiple class="form-control">--}}
{{--                            </div>--}}
                            <div class="form-group">
                                <fieldset id="actions" label="Choose Action">
                                    <input type="radio" name="action" id="approved" value="approved"><label for="approved"> &nbsp;Approve Crop</label><br>
                                    <input type="radio" name="action" id="uncropped" value="uncropped"><label for="uncropped"> &nbsp;Mark as Not cropped</label><br>
                                    <input type="radio" name="action" id="manual" value="manual"><label for="manual"> &nbsp;Move to Manual Cropping</label><br>
                                    <input type="radio" name="action" id="unreject" value="unreject"><label for="unreject"> &nbsp;Move to Approval</label>
                                </fieldset>
                                <button class="btn btn-secondary">Update Changes</button>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
            <div>
                @foreach($product->media()->get() as $image)
                    <?php
                    //                        [$height, $width] = getimagesize(getMediaUrl($image))
?>
                    @if (stripos($image->filename, 'cropped') !== false)
                        <div style="display: inline-block; border: 1px solid #ccc" class="mt-5">
                            <div style=" margin-bottom: 5px; width: 500px;height: 500px; background-image: url('{{ getMediaUrl($image) }}'); background-size: 500px">
                                <img style="width: 500px;" src="{{ asset('images/'.$img) }}" alt="">
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div style="width: 650px; margin: 0 auto;" class="fotorama" data-nav="thumbs" data-allowfullscreen="true">
                @foreach($product->media()->get() as $image)
                    <a href="{{ getMediaUrl($image) }}"><img src="{{ getMediaUrl($image) }}"></a>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Fotorama from CDNJS, 19 KB -->
    <link  href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>

{{--    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>--}}

    @if (Session::has('mesage'))
        <script>
            toastr['success'](
                '{{Session::get('message')}}',
                'success'
            )
        </script>
    @endif
@endsection