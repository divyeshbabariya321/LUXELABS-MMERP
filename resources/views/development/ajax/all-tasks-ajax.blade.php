<table class="table table-bordered all-tasks-table" style="table-layout: fixed;">
     <thead>
         <tr>
             <th width="">Id</th>
             <th width="">Subject</th>
             <th width="">Assigned To</th>
             <th width="">Approved Time</th>
             <th width="">Status</th>
             <th width="">Tracked Time</th>
             <th width="">Tracking Start</th>
             <th width="">Tracking End</th>
             <th width="">Difference</th>
         </tr>
     </thead>
     <tbody>
        @foreach ($tasks_csv as $task_csv)
             <tr>
                 <td style="vertical-align: middle;">{{ $task_csv['id'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Subject'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Assigned To'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Approved Time'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Status'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Tracked Time'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Tracking Start'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Tracking End'] }}</td>
                 <td style="vertical-align: middle;">{{ $task_csv['Difference'] }}</td>
             </tr>
         @endforeach
     </tbody>
 </table>
 
 {{ $issues->links() }}
