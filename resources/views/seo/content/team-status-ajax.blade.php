
@php
    $isSeo = false;
    try {
        $seoKeyword->seoRemarks()->firstOrFail();
        $isSeo = true;
    } catch (\Throwable $th) {
        //throw $th;
        $isSeo = false;
    }

    $isPublish = false;
    try {
        $seoKeyword->publishRemarks()->firstOrFail();
        $isPublish = true;
    } catch (\Throwable $th) {
        //throw $th;
        $isPublish = false;
    }
@endphp

@if($statusType == 'SEO_STATUS')
    @if($isSeo && $statusType == 'SEO_STATUS')
        @foreach ($seoKeyword->seoRemarks as $item)
            <div class="row mt-2">
                <div class="col-md-8 statusSec">
                    <label class="form-label">{{ $item->processStatus->label }}</label>
                    <input type="text" class="form-control" data-id="{{ $item->processStatus->id }}" value="{{ $item->remarks }}">
                </div>
            </div>
        @endforeach
    @elseif($statusType == 'SEO_STATUS')
        @foreach ($seoProcessStatus as $item)
            @if($item->type == 'seo_approval')
                <div class="row mt-2">
                    <div class="col-md-8 statusSec">
                        <label class="form-label">{{ $item->label }}</label>
                        <input type="text" class="form-control" data-id="{{ $item->id }}">
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endif

@if ($statusType == 'PUBLISH_STATUS')
    @if($isPublish && $statusType == 'PUBLISH_STATUS')
        @foreach ($seoKeyword->publishRemarks as $item)
            <div class="row mt-2">
                <div class="col-md-8 statusSec">
                    <label class="form-label">{{ $item->processStatus->label }}</label>
                    <input type="text" class="form-control" data-id="{{ $item->processStatus->id }}" value="{{ $item->remarks }}">
                </div>
            </div>
        @endforeach
    @elseif($statusType == 'PUBLISH_STATUS')
        @foreach ($seoProcessStatus as $item)
            @if($item->type == 'publish')
                <div class="row mt-2">
                    <div class="col-md-8 statusSec">
                        <label class="form-label">{{ $item->label }}</label>
                        <input type="text" class="form-control" data-id="{{ $item->id }}">
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endif


