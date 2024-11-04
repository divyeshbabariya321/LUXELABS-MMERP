<tr>
    <td>
        <div class="flex justify-content-between">
            <label  class="col-form-label">Strength</label>
            <button title="Add step" type="button" class="btn btn-secondary btn-sm add_plan_action_data" data-id="1" >+</button>
        </div>
        <table class="table table-bordered" id="store_website-analytics-table" style="table-layout: fixed;">
            <thead>
            <tr>
                <th>Strength</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody class="plan_action_tbody" data-id="1">
            @foreach($strengths as $key => $strength)
                <tr class="removable_class">
                    <td style="vertical-align:middle">
                        {{(strlen($strength->plan_action) > 12) ? substr($strength->plan_action, 0, 10).".." : $strength->plan_action}}
                        <input type="hidden" name="plan_action_old[]" value="{{$strength->id}}">
                    </td>
                    <td style="vertical-align:middle">{{$strength->getAdminUser->name}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($strength->created_at, 'd,M Y')}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($strength->updated_at, 'd,M Y')}}</td>
                    <td class="actions-main actions-main-sub w-100 border-bottom-0 border-right-0" style="vertical-align:middle">
                        <label class="switch">
                            <input type="hidden" name="plan_action_old_active_hidden[{{$strength->id}}]" value="{{$strength->is_active}}">
                            <input type="checkbox" name="plan_action_old_active[{{$strength->id}}]" value="1" {{(($strength->is_active == 1) ? 'checked' : '')}}>
                            <span class="slider round"></span>
                        </label>
                        <button type="button" class="btn btn-secondary btn-sm delete_field" data-id="{{$strength->id}}" >-</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </td>
    <td>
        <div class="flex justify-content-between">
            <label  class="col-form-label">Weakness</label>
            <button title="Add step" type="button" class="btn btn-secondary btn-sm add_plan_action_data" data-id="2" >+</button>
        </div>
        <table class="table table-bordered" id="store_website-analytics-table" style="table-layout: fixed;">
            <thead>
            <tr>
                <th>Weakness</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody class="plan_action_tbody" data-id="2" >
            @foreach($weaknesses as $key => $weakness)
                <tr class="removable_class">
                    <td style="vertical-align:middle">
                        {{(strlen($weakness->plan_action) > 12) ? substr($weakness->plan_action, 0, 10).".." : $weakness->plan_action}}
                        <input type="hidden" name="plan_action_old[]" value="{{$weakness->id}}">
                    </td>
                    <td style="vertical-align:middle">{{$weakness->getAdminUser->name}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($weakness->created_at, 'd,M Y')}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($weakness->updated_at, 'd,M Y')}}</td>
                    <td class="actions-main actions-main-sub w-100  border-bottom-0 border-right-0" style="vertical-align:middle">
                        <label class="switch">
                            <input type="hidden" name="plan_action_old_active_hidden[{{$weakness->id}}]" value="{{$weakness->is_active}}">
                            <input type="checkbox" name="plan_action_old_active[{{$weakness->id}}]" value="1" {{(($weakness->is_active == 1) ? 'checked' : '')}}>
                            <span class="slider round"></span>
                        </label>
                        <button type="button" class="btn btn-secondary btn-sm delete_field" data-id="{{$weakness->id}}" >-</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td>
        <div class="flex justify-content-between">
            <label  class="col-form-label">Opportunity</label>
            <button title="Add step" type="button" class="btn btn-secondary btn-sm add_plan_action_data" data-id="3" >+</button>
        </div>
        <table class="table table-bordered" id="store_website-analytics-table" style="table-layout: fixed;">
            <thead>
            <tr>
                <th>Opportunity</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody class="plan_action_tbody" data-id="3" >
            @foreach($opportunities as $key => $opportunity)
                <tr class="removable_class">
                    <td style="vertical-align:middle">
                        {{(strlen($opportunity->plan_action) > 12) ? substr($opportunity->plan_action, 0, 10).".." : $opportunity->plan_action}}
                        <input type="hidden" name="plan_action_old[]" value="{{$opportunity->id}}">
                    </td>
                    <td style="vertical-align:middle">{{$opportunity->getAdminUser->name}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($opportunity->created_at, 'd,M Y')}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($opportunity->updated_at, 'd,M Y')}}</td>
                    <td class="actions-main actions-main-sub w-100  border-bottom-0 border-right-0" style="vertical-align:middle">
                        <label class="switch">
                            <input type="hidden" name="plan_action_old_active_hidden[{{$opportunity->id}}]" value="{{$opportunity->is_active}}">
                            <input type="checkbox" name="plan_action_old_active[{{$opportunity->id}}]" value="1" {{(($opportunity->is_active == 1) ? 'checked' : '')}}>
                            <span class="slider round"></span>
                        </label>
                        <button type="button" class="btn btn-secondary btn-sm delete_field" data-id="{{$opportunity->id}}" >-</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </td>
    <td>
        <div class="flex justify-content-between">
            <label  class="col-form-label">Threat</label>
            <button title="Add step" type="button" class="btn btn-secondary btn-sm add_plan_action_data" data-id="4" >+</button>
        </div>
        <table class="table table-bordered" id="store_website-analytics-table" style="table-layout: fixed;">
            <thead>
            <tr>
                <th>Threat</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody class="plan_action_tbody" data-id="4" >
            @foreach($threats as $key => $threat)
                <tr class="removable_class">
                    <td style="vertical-align:middle">
                        {{(strlen($threat->plan_action) > 12) ? substr($threat->plan_action, 0, 10).".." : $threat->plan_action}}
                        <input type="hidden" name="plan_action_old[]" value="{{$threat->id}}">
                    </td>
                    <td style="vertical-align:middle">{{$threat->getAdminUser->name}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($threat->created_at, 'd,M Y')}}</td>
                    <td style="vertical-align:middle">{{\App\Helpers\common_functions::UTCToLocal($threat->updated_at, 'd,M Y')}}</td>
                    <td class="actions-main actions-main-sub w-100  border-bottom-0 border-right-0" style="vertical-align:middle">
                        <label class="switch">
                            <input type="hidden" name="plan_action_old_active_hidden[{{$threat->id}}]" value="{{$threat->is_active}}">
                            <input type="checkbox" name="plan_action_old_active[{{$threat->id}}]" value="1" {{(($threat->is_active == 1) ? 'checked' : '')}}>
                            <span class="slider round"></span>
                        </label>
                        <button type="button" class="btn btn-secondary btn-sm delete_field" data-id="{{$threat->id}}" >-</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </td>
</tr>