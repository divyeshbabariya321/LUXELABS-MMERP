<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th>Id</th>
        <th width="15%">Name</th>
        <th width="20%">Expression</th>
        <th>Event Name</th>
        <th>Template ID</th>
        <th>Severity</th>
        <th>Is active</th>
        <th>Edit</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\Zabbix\Trigger $trigger */ ?>
    @foreach($triggers as $trigger)
        <tr>
            <td class="td-id-{{ $trigger->getId() }}">
                {{ $trigger->getId() }}
            </td>
            <td class="td-name-{{ $trigger->getId() }}">
                {{ $trigger->getName() }}
            </td>
            <td class="td-expression-{{ $trigger->getId() }}">
                {{ $trigger->getExpression() }}
            </td>
            <td class="td-event-name-{{ $trigger->getId() }}">
                {{ $trigger->getEventName() }}
            </td>
            <td class="td-template-id-{{ $trigger->getId() }}">
                {{ $trigger->getTemplateName() }}
            </td>
            <td class="td-priority-{{ $trigger->getId() }} 
                @if ($trigger->getSeverity() >= 4)
                    red-td
                @elseif ($trigger->getSeverity() >= 2)
                    orange-td
                @else
                    gray-td
                @endif
                ">
                @switch($trigger->getSeverity())
                    @case(1)
                        Information
                        @break
                    @case(2)
                        Warning
                        @break
                    @case(3)
                        Average
                        @break
                    @case(4)
                        High
                        @break
                    @case(5)
                        Disaster
                        @break
                    @default
                        (default) not classified
                @endswitch
            </td>
            <td class="td-status-{{ $trigger->getId() }}">
                @if($trigger->isActive()) 
                <a href="#" class="btn btn-xs btn-warning btn-status-trigger td-status-{{ $trigger->getId() }}" data-id="{{ $trigger->getId() }}" data-json='@json($trigger)'>Deactivate</a>
                @else
                <a href="#" class="btn btn-xs btn-success btn-status-trigger td-status-{{ $trigger->getId() }}" data-id="{{ $trigger->getId() }}" data-json='@json($trigger)'>Enable</a>
                @endif
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-trigger td-edit-{{ $trigger->getId() }}" data-id="{{ $trigger->getId() }}" data-json='@json($trigger)'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<nav aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page - 1 }}">Previous</a></li>
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page - 1 }}">{{ $page - 1 }}</a></li>
    <li class="page-item active"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page }}">{{ $page }}</a></li>
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page + 1 }}">{{ $page + 1 }}</a></li>
    <li class="page-item"><a class="page-link" href="{{ route('zabbix.trigger.index') }}/?page={{ $page + 1 }}">Next</a></li>
  </ul>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.pagination a.page-link').on('click', function (e) {
            e.preventDefault();

            var pageUrl = $(this).attr('href');

            $.ajax({
                url: pageUrl,
                type: 'GET',
                success: function (data) {
                    $('#ajax-content').html(data);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>