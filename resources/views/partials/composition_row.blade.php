<tr id="composition_{{ $composition->id }}">
    <td>{{ $composition->id }}</td>
    <td>{{ $composition->name }}</td>
    <td>{{ $composition->created_at->format('Y-m-d') }}</td>
    <!-- Other fields of the composition -->
</tr>
