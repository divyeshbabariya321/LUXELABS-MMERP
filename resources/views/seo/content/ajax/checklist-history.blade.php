<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Is checked</th>
            <th>Value</th>
            <th>Date</th>
            <th>User</th>
        </tr>
    </thead>

    <tbody>
        @php
            $oldHistroy = null;
        @endphp
        @foreach ($checklistHistory as $history)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $history->field_name }}</td>
                <td>
                    <ul class="list-group">
                        <li class="list-group-item"><b>Old : </b>{{ isset($oldHistroy->is_checked) ? ($oldHistroy->is_checked ? 'Checked' : 'Unchecked') : '-' }}</li>    
                        <li class="list-group-item"><b>New : </b>{{ $history->is_checked ? 'Checked' : 'Unchecked' }}</li>    
                    </ul> 
                </td>
                <td>
                    <ul class="list-group">
                        <li class="list-group-item"><b>Old : </b>{{ $oldHistroy->value ?? '-' }}</li>    
                        <li class="list-group-item"><b>New : </b>{{ $history->value ?? '-' }}</li>    
                    </ul>     
                </td>
                <td>
                    <ul class="list-group">
                        <li class="list-group-item"><b>Old : </b>{{ isset($oldHistroy->date) ? date('Y-m-d h:i A', strtotime($oldHistroy->date)) : '-' }}</li>    
                        <li class="list-group-item"><b>New : </b>{{ isset($history->date) ? date('Y-m-d h:i A', strtotime($history->date)) : '-' }}</li>    
                    </ul> 
                </td>
                <td>
                    <ul class="list-group">
                        <li class="list-group-item"><b>Old : </b>{{ isset($oldHistroy->user) ? $oldHistroy->user->name : '-' }}</li>    
                        <li class="list-group-item"><b>New : </b>{{ isset($history->user) ? $history->user->name : '-' }}</li>    
                    </ul> 
                </td>
            </tr>
            @php
                $oldHistroy = $history;
            @endphp
        @endforeach
    </tbody>
</table>