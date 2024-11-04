@foreach ($events as $event)
    <tr>
        <td> {{ $event->name }} </td>
        <td> {{ $event->slug }} </td>
        <td> {{ $event->description }} </td>
        <td> {{ $event->start_date }} </td>
        <td> {{ $event->end_date }} </td>
        <td> {{ $event->duration_in_min }} </td>
        <td> {{ $event->created_at }} </td>
    </tr>
@endforeach()