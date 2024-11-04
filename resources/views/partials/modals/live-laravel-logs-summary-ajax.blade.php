@if (isset($logs))
    @foreach ($logs as $log)
        @php
            $str = $log;
            $temp1 = explode('.', $str);
            $temp2 = explode(' ', $temp1[0]);
            $type = $temp2[2];
            
            $file_name = explode('===', $log);
            $log = str_replace('===' . $file_name[1], '', $log);
        @endphp

        <tr>
            <td>{{ $file_name[1] }}</td>
            <td>{{ $type }}</td>
            <td class="expand-row table-hover-cell">
                <span class="td-mini-container">
                    {{ strlen($log) > 110 ? substr($log, 0, 110) . '...' : $log }}
                </span>
                <span class="td-full-container hidden">
                    {{ $log }}
                </span>
            </td>
        </tr>
    @endforeach
@endif
