@foreach ($userEmails as $key => $userEmail)
    <tr>
        <td>{{ Carbon\Carbon::parse($userEmail->created_at)->format('d-m-Y H:i:s') }}
        </td>
        <td class="expand-row-email" style="word-break: break-all">
            <span class="td-mini-email-container">
                {{ strlen($userEmail->from) > 30 ? substr($userEmail->from, 0, 15) . '...' : $userEmail->from }}
            </span>
            <span class="td-full-email-container hidden">
                {{ $userEmail->from }}
            </span>
        </td>
        <td class="expand-row-email" style="word-break: break-all">
            <span class="td-mini-email-container">
                {{ strlen($userEmail->to) > 30 ? substr($userEmail->to, 0, 15) . '...' : $userEmail->to }}
            </span>
            <span class="td-full-email-container hidden">
                {{ $userEmail->to }}
            </span>
        </td>
        <td data-toggle="modal" data-target="#view-quick-email"
            onclick="openQuickMsg({{ json_encode($userEmail) }})"
            style="cursor: pointer;">
            {{ strlen($userEmail->subject) > 10 ? substr($userEmail->subject, 0, 15) . '...' : $userEmail->subject }}
        </td>
        <td>
            <a href="javascript:;" data-id="{{ $userEmail->id }}"
                data-content="{{ $userEmail->message }}"
                class="menu_editor_copy btn btn-xs p-2">
                <i class="fa fa-copy"></i>
            </a>
        </td>
        <td>
            <input type="checkbox" name="email_read" id="is_email_read"
                value="1" data-id="{{ $userEmail->id }}"
                onclick="updateReadEmail(this)">
        </td>
    </tr>
@endforeach