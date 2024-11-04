@foreach ($data as $key => $translation)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $translation->from }}</td>
                    <td>{{ $translation->to }}</td>
                    <td>{{ $translation->text_original }}</td>
                    <td>{{ $translation->text }}</td>
                    <td>{{ $translation->updated_at }}</td>
                    <td>{{ $translation->created_at }}</td> 
                    <td>
                        <a class="btn btn-image" href="{{ route('translation.edit',$translation->id) }}"><img src="/images/edit.png"/></a>
                        {{ html()->form('DELETE', route('translation.destroy', [$translation->id]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}

                    </td>
                </tr>
@endforeach