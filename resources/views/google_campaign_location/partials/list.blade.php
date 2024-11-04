@foreach($locations as $location)
    <tr>
        <td>{{$location->id}}</td>
        <td>{{$location->google_location_id}}</td>
        <td>{{$location->type}}</td>
        <td>{{$location->address}}</td>
        <td>{{$location->distance}}</td>
        <td>{{$location->radius_units}}</td>
        <td>{{$location->is_target ? "Yes" : "Exclude"}}</td>
        <td>{{$location->created_at}}</td>
        <td>
            <div class="d-flex justify-content-between">
                {{ html()->form('DELETE', route('google-campaign-location.deleteLocation', [$campaignId, $location['google_location_id']]))->style('display:inline')->open() }}
                <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
                {{ html()->form()->close() }}
            </div>
        </td>
    </tr>
@endforeach