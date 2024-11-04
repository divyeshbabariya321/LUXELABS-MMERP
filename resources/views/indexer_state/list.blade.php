<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th width="15%">Index</th>
        <th>Updated At</th>
        <th>Status</th>
        <th>Logs</th>
        <th>Settings</th>
        <th>Reindex</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\IndexerState $indexer */ ?>
    @foreach($indexerStates as $indexer)
        <?php
            $statusCssClass = 'index-' . $indexer->getStatus();
        ?>
        <tr>
            <td class="td-index-{{ $indexer->getId() }}">
                {{ $indexer->getIndex() }}
            </td>
            <td class="td-updated-at-{{ $indexer->getId() }}">
                {{ (string)$indexer->getUpdatedAt() }}
            </td>
            <td class="td-status-{{ $indexer->getId() }} {{ $statusCssClass }}">
                <span>{{ strtoupper($indexer->getStatus()) }}</span>
            </td>
            <td class="td-logs-{{ $indexer->getId() }}">
            <a href="#" class="btn btn-xs btn-info btn-reindex-logs td-edit-{{ $indexer->getId() }}" data-id="{{ $indexer->getId() }}">Logs</a>
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-indexer td-edit-{{ $indexer->getId() }}" data-id="{{ $indexer->getId() }}" data-json='@json($indexer)'>Edit</a>
            </td>
            <td>
                @if($indexer->getStatus() !== \App\Elasticsearch\Reindex\Interfaces\Reindex::RUNNING)
                    <a href="#" 
                        class="btn btn-xs btn btn-success btn-reindex-indexer td-edit-{{ $indexer->getId() }}" 
                        data-id="{{ $indexer->getId() }}" 
                        data-json='@json($indexer)'>
                        Reindex
                    </a>
                @else
                    <a href="#" 
                        class="btn btn-xs btn btn-danger btn-reindex-indexer td-edit-{{ $indexer->getId() }}" 
                        data-id="{{ $indexer->getId() }}"
                        stop-reindex="1">
                        Stop reindex
                    </a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>