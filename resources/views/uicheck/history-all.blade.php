@if ($data)
@foreach ($data as $value)
<tr>
    <td>{{ $value->uichecks_id ?? '-' }}</td>
    <td>{{ $value->site_development_category_name ?? '-' }}</td>
    <td>{{ $value->store_website_name ?? '-' }}</td>
    <td>{{ $value->type ?? '-' }}</td>
    <td>{{ $value->old_disp_val ?? '-' }}</td>
    <td>{{ $value->new_disp_val ?? '-' }}</td>
    <td>{{ $value->addedBy ?? '-' }}</td>
    <td class="cls-created-date">{{ $value->created_at ?? '-' }}</td>
</tr>
@endforeach
@endif