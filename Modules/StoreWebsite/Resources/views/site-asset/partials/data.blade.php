@if($store_websites)
    @forelse($site_development_categories as $sdc) 
    <tr>
        <td>
            <input  type="checkbox" class="checkboxClass" name="selectcheck" value='{{ $sw->id }}'>
        </td>
        <td>
        {{ $sdc->title }} 
        </td>
        <td>
           {{ $sdc->masterCategory?->title }} 
        </td>
        @forelse($store_websites as $sw)
            @php 
                $check = 0;
                $site = $sdc->getDevelopment($sdc->id, $sw->id);
                if($site){
                    if($site->is_site_asset == 1){
                        $check = 1;
                    }
                }
            @endphp
            <td>@if($check)
                    <table>
                        <tr>
                            <td> 
                                <button title="PSD Desktop" type="button" class="btn btn-image" ><i class="fa fa-desktop" @if( $sw->getSiteAssetData($sw->id, $sdc->id, 'PSDD')) style="color:green" @endif></i></button> 
                            </td>
                            <td> <button title="PSD Mobile" type="button" class="btn btn-image" ><i class="fa fa-phone" @if( $sw->getSiteAssetData($sw->id, $sdc->id, 'PSDM')) style="color:green" @endif></i></button>
                            </td>
                            <td> <button title="PSD App" type="button" class="btn btn-image" ><i class="fa fa-windows" @if( $sw->getSiteAssetData($sw->id, $sdc->id, 'PSDA')) style="color:green" @endif></i></button>
                            </td>
                            <td> <button title="Figma" type="button" class="btn btn-image" ><i class="fa fa-file-image-o" @if( $sw->getSiteAssetData($sw->id, $sdc->id, 'FIGMA')) style="color:green" @endif></i></button>
                            </td>
                            <td>
                            @php
                                $websitenamestr = ($sw) ? $sw->title : "";
                            @endphp
                            <button title="create quick task" type="button" class="btn btn-image d-inline create-quick-task p-2" data-id="@if($site){{ $site->id }}@endif" data-title="@if($site){{ $websitenamestr.' '.$site->title }} @endif" data-category_id = "{{ $sdc->id }}"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-image d-inline count-dev-customer-tasks p-2" title="Show task history" title="Show Task History" data-id="@if($site){{ $site->id }}@endif"><i class="fa fa-info-circle"></i></button>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
        @empty
        @endforelse
        <td>
            
    </tr>
    @empty
    @endforelse
@else
    <tr><td>Sorry No Data Available.</td></tr>
@endif