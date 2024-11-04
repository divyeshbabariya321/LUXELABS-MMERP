<tr>
    <td class="p-2"></td>
    <td class="p-2">
        <div class="d-flex justify-content-between">
            <span>
                {{ $activity }}
            </span>
            <span>
                <button type="button" class="btn btn-image task-actual-start p-0 m-0" data-id="{{ $activity->id }}" data-type="activity">
                    <img src="/images/youtube_128.png" />
                </button>
                <button type="button" class="btn btn-image task-complete p-0 m-0" data-id="{{ $activity->id }}" data-type="activity">
                    <img src="/images/incomplete.png" />
                </button>
            </span>
        </div>
    </td>
    <td class="p-2 task-start-time"></td>
    <td class="p-2 task-time"></td>
    <td class="expand-row table-hover-cell p-2">
        <span class="td-mini-container"></span>
        <span class="td-full-container hidden">
            <ul></ul>
            <span class="d-flex">
                <input type="text" class="form-control input-sm quick-remark-input" name="remark" placeholder="Remark" value="">
                <button type="button" class="btn btn-image quick-remark-button" data-id="{{ $activity->id }}">
                    <img src="/images/filled-sent.png" />
                </button>
            </span>
        </span>
    </td>
</tr>