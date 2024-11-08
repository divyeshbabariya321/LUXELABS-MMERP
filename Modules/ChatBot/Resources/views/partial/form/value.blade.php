<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="value">Type</label>
            <select name="keyword_or_question" id="keyword_or_question" class="form-control view_details_div"
                    onchange="checkType()">
                <option value="intent">Intent</option>
                <option value="entity">Entity</option>
                <option value="simple">Simple Text</option>
                <option value="priority-customer">Priority Customer</option>
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="value">Intent / Entity / ERP Entity</label>
            {{ html()->text('value', isset($value) ? $value : '')->class('form-control')->placeholder('Enter your value') }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="value">Category</label>
            <select name="category_id" id="" class="form-control">
                <option value="">Select</option>
                @foreach($allCategoryList as $cat)
                    <option value="{{$cat['id']}}">{{$cat['text']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="value">Auto Approve</label>
            <select name="auto_approve" id="" class="form-control">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div id="intent_details">
            <div class="form-group">
                <label for="question">User Intent</label>
                <div class="row align-items-end" id="intentValue_1">
                    <div class="col-md-9">
                        {{ html()->text('question[]', null)->class('form-control')->placeholder('Enter User Intent') }}
                    </div>
                    <div class="col-md-2" id="add-intent-value-btn">
                        <!-- <a href="javascript:;" class="btn btn-secondary btn-sm add-more-intent-condition-btn">
                            -
                        </a>     -->
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="question">Parent Intent</label>
                <div class="row align-items-end" id="intentparent_1">
                    <div class="col-md-9">
                        <?php
                        $parentIntents = isset($parentIntents) ? $parentIntents : [];
                        ?>
                        {{ html()->select('parent', ['' => 'Select parent Intent'] + $parentIntents)->class('form-control')->placeholder('Select Parent intent') }}

                    </div>
                </div>
            </div>
            <!-- <div class="form-group" id="add-intent-value-btn">
                <a href="javascript:;" class="btn btn-secondary btn-sm add-more-intent-condition-btn">
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            </div> -->
        </div>
    </div>
</div>
<div id="entity_details">
    <div class="form-group">
        <label for="value">User Entity</label>
        {{ html()->text('value_name', null)->class('form-control')->id('value')->placeholder('Enter user entity') }}
    </div>
    <div class="form-row align-items-end">
        <div class="form-group col-md-4">
            <label for="type">Entity types (Google)</label>
            {{ html()->select('entity_type', ['' => 'Please select'] + ($allEntityType ?? []))->class('form-control')->id('types') }}
        </div>
        <div class="form-group col-md-4">
            <div class="row align-items-end" id="typeValue_1">
                <div class="col-md-9">
                    {{ html()->text('entity_types[]', null)->class('form-control')->id('type')->placeholder('Enter value')->attribute('maxLength', 64) }}
                </div>
            </div>
        </div>
        <div class="form-group col-md-2" id="add-type-value-btn2">
            <a href="javascript:;" class="btn btn-secondary btn-sm add-more-condition-btn2">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </div>
    <div class="form-row align-items-end">
        <div class="form-group col-md-4">
            <label for="type">Type (Watson)</label>
            {{ html()->select('types', ['' => 'Please select', 'synonyms' => 'synonyms', 'patterns' => 'patterns'])->class('form-control')->id('types') }}
        </div>
        <div class="form-group col-md-4">
            <div class="row align-items-end" id="typeValue_1">
                <div class="col-md-9">
                    {{ html()->text('type[]', null)->class('form-control')->id('type')->placeholder('Enter value')->attribute('maxLength', 64) }}
                </div>
            </div>
        </div>
        <div class="form-group col-md-2" id="add-type-value-btn">
            <a href="javascript:;" class="btn btn-secondary btn-sm add-more-condition-btn">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </div>
</div>
<div id="erp_details">
    <div class="form-group">
        <strong>Keyword:</strong>
        <input type="text" name="keyword" class="form-control" value="{{ old('keyword') }}"
               placeholder="Enter Comma Separated Values">

        @if ($errors->has('keyword'))
            <div class="alert alert-danger">{{$errors->first('keyword')}}</div>
        @endif
    </div>
    <div class="form-group">
        <strong>Completion Date:</strong>
        <div class='input-group date' id='sending-datetime'>
            <input type='text' class="form-control" name="sending_time" value="{{ date('Y-m-d H:i') }}"/>

            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
        </div>

        @if ($errors->has('sending_time'))
            <div class="alert alert-danger">{{$errors->first('sending_time')}}</div>
        @endif
    </div>

    <div class="form-group">
        <strong>Repeat:</strong>
        <select class="form-control" name="repeat">
            <option value="">Don't Repeat</option>
            <option value="Every Day">Every Day</option>
            <option value="Every Week">Every Week</option>
            <option value="Every Month">Every Month</option>
            <option value="Every Year">Every Year</option>
        </select>
    </div>

    <div class="form-group">
        <strong>Active:</strong>
        <input type="checkbox" class="form-control" name="is_active" value="1" checked>
    </div>
</div>

<div class="form-group">
    <div style="word-break: break-all;">
        <strong>Reply:</strong>
        <p>You can use variables in the reply they will be formatted when sent Eg:- #{website}, #{order_id}:</p>
        <p>Variables List:-
            <select>
                @foreach($variables ?? [] as $variable)
                    <option value="{!! $variable !!}">{!! $variable !!}</option>
                @endforeach
            </select>
        </p>
    </div>
    <textarea name="suggested_reply" class="form-control" rows="8" cols="80"
              required>{{ old('suggested_reply') }}</textarea>
</div>
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="value">Select watson account</label>
            <select name="watson_account" class="form-control" required>
                <option value="0">All account</option>
                @if(!empty($watson_accounts))
                    @foreach($watson_accounts as $acc)
                        <option value="{{$acc->id}}"> {{$acc->id}} - {{$acc->storeWebsite->title}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <div class="col" id="google_account">
        <div class="form-group">
            <label for="value">Select goggle account</label>
            <select name="google_account" class="form-control" required>
                <option value="">Select google account</option>
                @if(!empty($google_accounts))
                    @foreach($google_accounts as $acc)
                        <option value="{{$acc->id}}"> {{$acc->id}} - {{$acc->storeWebsite ? $acc->storeWebsite->title : ''}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>


