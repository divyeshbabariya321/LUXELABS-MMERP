@foreach ($permissions as $key => $permission)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->route }}</td>
                <td>
                    <a class="btn btn-image" href="{{ route('permissions.show',$permission->id) }}"><img src="/images/view.png" /></a>
                    @if(auth()->user()->isAdmin())
                        <a class="btn btn-image" href="{{ route('permissions.edit',$permission->id) }}"><img src="/images/edit.png" /></a>
                        {{ html()->form('DELETE', route('permissions.destroy', [$permission->id]))->style('display:inline')->open() }}
                        {{ html()->submit('Delete')->class('btn btn-secondary') }}
                        {{ html()->form()->close() }}
                    @endif
                    
                        
                   
                </td>
            </tr>
        @endforeach