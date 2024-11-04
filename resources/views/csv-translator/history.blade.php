<table class="table table-bordered text-wrap w-auto min-w-100">
    <thead>
        <tr>
            <th>Id</th>
            <th>Keyword</th>
            @if ($language == 'en')
            <th>En</th>
            <th>En Status</th>
            @endif
            @if ($language == 'es')
            <th>ES</th>
            <th>ES Status</th>
            @endif
            @if ($language == 'ru')
            <th>RU</th>
            <th>RU Status</th>
            @endif
            @if ($language == 'ko')
            <th>KO</th>
            <th>KO Status</th>
            @endif
            @if ($language == 'ja')
            <th>JA</th>
            <th>JA Status</th>
            @endif
            @if ($language == 'it')
            <th>IT</th>
            <th>IT Status</th>
            @endif
            @if ($language == 'de')
            <th>DE</th>
            <th>DE Status</th>
            @endif
            @if ($language == 'fr')
            <th>FR</th>
            <th>FR Status</th>
            @endif
            @if ($language == 'nl')
            <th>NL</th>
            <th>NL Status</th>
            @endif
            @if ($language == 'zh')
            <th>ZH</th>
            <th>ZH Status</th>
            @endif
            @if ($language == 'ar')
            <th>AR</th>
            <th>AR Status</th>
            @endif
            @if ($language == 'ur')
            <th>UR</th>
            <th>UR Status</th>
            @endif
            <th>Updator</th>
            <th>Approver</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody class="data_history">
        @forelse($history as $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->key }}</td>
            @if ($language == 'en')
            <td>{{ $value->en }}</td>
            <td>{{ $value->status_en }}</td>
            @endif
            @if ($language == 'es')
            <td>{{ $value->es }}</td>
            <td>{{ $value->status_es }}</td>
            @endif
            @if ($language == 'ru')
            <td>{{ $value->ru }}</td>
            <td>{{ $value->status_ru }}</td>
            @endif
            @if ($language == 'ko')
            <td>{{ $value->ko }}</td>
            <td>{{ $value->status_ko }}</td>
            @endif
            @if ($language == 'ja')
            <td>{{ $value->ja }}</td>
            <td>{{ $value->status_ja }}</td>
            @endif
            @if ($language == 'it')
            <td>{{ $value->it }}</td>
            <td>{{ $value->status_it }}</td>
            @endif
            @if ($language == 'de')
            <td>{{ $value->de }}</td>
            <td>{{ $value->status_de }}</td>
            @endif
            @if ($language == 'fr')
            <td>{{ $value->fr }}</td>
            <td>{{ $value->status_fr }}</td>
            @endif
            @if ($language == 'nl')
            <td>{{ $value->nl }}</td>
            <td>{{ $value->status_nl }}</td>
            @endif
            @if ($language == 'zh')
            <td>{{ $value->zh }}</td>
            <td>{{ $value->status_zh }}</td>
            @endif
            @if ($language == 'ar')
            <td>{{ $value->ar }}</td>
            <td>{{ $value->status_ar }}</td>
            @endif
            @if ($language == 'ur')
            <td>{{ $value->ur }}</td>
            <td>{{ $value->status_ur }}</td>
            @endif
            <td>{{ $value->updater }}</td>
            <td>{{ $value->approver }}</td>
            <td>{{ $value->created_at }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7">No data found</td>
        </tr>
        @endforelse
    </tbody>
</table>