@php
 $repositories = \App\Github\GithubRepository::get();
@endphp
<div id="create-repo-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Latest PR</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">S.No</th>
                            <th width="20%">Repo name</th>
                            <th width="20%">status</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list">
                        @foreach($repositories as $key => $repo)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$repo->name}}</td>
                            <td>
                                <input type="checkbox" name="repostatus[]" value="{{ $repo->id }}" data-repo_id="{{ $repo->id }}" class="repostatus" 
                                    @if ($repo->repo_status == 1)
                                        checked="checked"
                                    @endif
                                >
                            </td>
                        </tr>
                        @endforeach 
                    </tbody>
                    
                </table>
           </div>
        </div>
    </div>
</div>

