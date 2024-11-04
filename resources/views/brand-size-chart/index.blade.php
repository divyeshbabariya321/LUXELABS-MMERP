@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
@endsection

@section('content')
    <style type="text/css">
        .nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
            color: #fff;
            background-color: #5A6267;
        }

        a {
            color: #5A6267;
            text-decoration: none;
        }

        a:focus, a:hover{
            color: #5A6267;
        }

    </style>
    <div class="row">
        <div class="col-md-12 ">
            <h2 class="page-heading">Brand Size Chart List </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 pr-5">
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('brand/create/size/chart') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <div class="row">
        <div class="col-md-12 pl-5 pr-5">
            <div id="exTab1" >	
                <ul  class="nav nav-pills">
                    @forelse ($storeWebsite as $key => $item)
                        <li class="@if($loop->first) active @endif">
                            <a  href="#tab_div_{{ $key + 1 }}" data-toggle="tab">{{ $item->title }}</a>
                        </li>
                    @empty
                    @endforelse
                </ul>
        
                <div class="tab-content clearfix">
                    @forelse ($storeWebsite as $key => $item)
                        <div class="tab-pane @if($loop->first) active @endif" id="tab_div_{{ $key + 1 }}">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <tr>
                                        <th></th>
                                        @forelse ($item->sizeCategory->unique() as $catkey => $catitem)
                                            <th>@if(isset($catitem->parent->parent)) {{ $catitem->parent->parent->title }} @endif {{ $catitem->title }}</th>
                                        @empty
                                            <th></th>
                                        @endforelse
                                    </tr>
                                    @forelse ($item->size_charts as $key => $size)
                                        <tr>
                                            <th>{{ ($size['brands']['name'])?$size['brands']['name']:'No Brand' }}</th>
                                            @forelse ($item->sizeCategory->unique() as $catkey => $catitem)
                                                <td>
                                                @forelse ($sizeChart as $chartitem)
                                                    @if($chartitem->category_id == $catitem->id && $chartitem->brand_id == $size['brand_id'])
                                                        @if ($chartitem->hasMedia($sizeChartMediaTag))
                                                            <a href="{{ getMediaUrl($chartitem->getMedia($sizeChartMediaTag)->first()) }}" data-fancybox>
                                                                <span class="td-mini-container">
                                                                    <img src="{{ getMediaUrl($chartitem->getMedia($sizeChartMediaTags)->first()) }}" class="img-responsive thumbnail-200 mb-1">
                                                                </span>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @empty
                                                @endforelse
                                                </td>
                                            @empty
                                                <td></td>
                                            @endforelse
                                        </tr>
                                    @empty
                                        <tr>
                                            <th></th>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script type="text/javascript">
$('[data-fancybox]').fancybox({
    clickContent: 'close',
    buttons: ['close']
})
</script>
@endsection
