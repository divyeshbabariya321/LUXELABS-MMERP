<table id="classTable" class="table table-bordered">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Info</th>
            <th>Creator</th>
            <th>Documents</th>
            <th>created_at</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!$devDocuments->isEmpty()) { $i=0; ?>
        <?php foreach($devDocuments as $documents) { ?>
        <tr>
            <td><?php echo $documents->subject; ?></td>
            <td><?php echo $documents->description; ?></td>
            <td><?php echo $documents->user ? $documents->user->name : 'N/A'; ?></td>
            <td>
                <a href="{{ url('/') }}/uicheckdocs/{{ $documents->filename }}" target="_blank" class="d-inline-block">
                    {{ "Document : ".($i+1) }}
                </a>                    
            </td>
            <td>{{ $documents->created_at ? $documents->created_at->format('Y-m-d H:i:s') : '-' }}</td>
        </tr>
        <?php  $i++; } ?>
        <?php } ?>
    </tbody>
</table>
