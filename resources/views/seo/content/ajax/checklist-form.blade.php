@php
    $auth = auth()->user();
@endphp
<form action="{{ $actionUrl }}" autocomplete="off" id="checkListForm"> @csrf
    <input type="hidden" name="type" value="CHECKLIST">
    <input type="hidden" name="checklistType" value="{{ $statusType }}">
    @if($checkList->count() > 0)
        @foreach ($checkList as $item)
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="hidden" name="label[]" value="{{ $item->field_name }}">
                    <label class="form-label">{{ $item->field_name }}</label>
                </div>
                <div class="col-md-2">
                    <select name="is_checked[]" class="form-control">
                        <option value="1" {{ $item->is_checked == 1 ? 'selected' : '' }}>Check</option>
                        <option value="0" {{ $item->is_checked == 0 ? 'selected' : '' }}>Uncheck</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="value[]" value="{{ $item->value }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="datetime-local" name="date[]" value="{{ $item->date }}" class="form-control">
                </div>
                @if($auth->hasRole(['Admin']))
                <div class="col-md-1">
                    <button type="button" data-url="{{ route('seo.content.edit', $seoProcess->id)}}" data-label="{{ $item->field_name }}" class="btn btn-image search ui-autocomplete-input checkListHistory" style="cursor: default">
                        <img src="{{ asset('images/history.png') }}" style="width: 30px !important; cursor: default;">
                    </button>
                </div>
                @endif
            </div>
        @endforeach
    @else
        @foreach ($checkListLabels as $item)
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="hidden" name="label[]" value="{{ $item }}">
                    <label class="form-label">{{ $item }}</label>
                </div>
                <div class="col-md-2">
                    <select name="is_checked[]" class="form-control">
                        <option value="1">Check</option>
                        <option value="0">Uncheck</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="value[]" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="datetime-local" name="date[]" class="form-control">
                </div>
                @if($auth->hasRole(['Admin']))
                <div class="col-md-1">
                    <button type="button" data-url="{{ route('seo.content.edit', $seoProcess->id)}}" data-label="{{ $item }}" class="btn btn-image search ui-autocomplete-input checkListHistory" style="cursor: default">
                        <img src="{{ asset('images/history.png') }}" style="width: 30px !important; cursor: default;">
                    </button>
                </div>
                @endif
            </div>
        @endforeach
    @endif
</form>