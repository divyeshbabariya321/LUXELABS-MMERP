 @if($countries->isEmpty())

            <tr>
                <td colspan="5">
                    No Result Found
                </td>
            </tr>
@else

@foreach ($countries as $country)
    <tr>
        <td><input  type="checkbox" class="checkboxClass" name="selectcheck" value='{{ $country->id }}'></td>
        <td>{{ $country->country_code }}</td>
        <td>{{ $country->country_name }}</td>
        <td>
        <select name="segment" onchange="addsegment('{{$country->id}}',this.value);" >   
            <option value=""></option>  
        @foreach($segments as $s)
             <?php
                  $sel='';
                  if ($country->segment_id==$s->id)
                    {
                         $sel =" selected='selected' ";
                    }
             ?>
                <option value="{{$s->id}}" {{$sel}}>{{$s->segment}}</option>
             @endforeach   
       </select>     
        </td>
        <td><input type="number" step="0.00" class="dutyinput form-control" data-id="{{$country->id}}" value="{{$country->default_duty}}"></td>
        <td> @if($country->status==1)
               approved
             @else
               pending 
             @endif     
        </td>
        <td>{{ $country->created_at->format('d-m-Y') }}</td>
        <td>{{ $country->updated_at->format('d-m-Y H:i:s') }}</td>
    </tr>   
@endforeach
@endif