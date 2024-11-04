<script type="text/x-jsrender" id="template-result-block">
    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>Category</th>
                <th>Store</th>
                <th>Meta Title</th>
                <th>Meta Keyword</th>
                <th>Meta Description</th>
                <th>Meta Title <br> Translate Score</th>
                <th>Meta Keyword <br> Translate Score</th>
                <th>Meta Description <br> Translate Score</th>
                <th>Store View</th>
                <th>Created at</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}}
                  <tr>
                    <td><input type="checkbox" class="groups" name="groups[]" value="{{:prop.id}}">&nbsp;{{:prop.id}}</td>
                    <td>{{:prop.category}}</td>
                    <td>{{:prop.store_name}}</td>
                    <td>{{:prop.meta_title}}</td>
                    <td>{{:prop.meta_keyword}}</td>
                    <td>{{:prop.meta_description}}</td>
                    <td>{{if prop.meta_title_score}} {{:prop.meta_title_score}} {{/if}}</td>
                    <td>{{if prop.meta_keyword_score}} {{:prop.meta_keyword_score}} {{/if}}</td>
                    <td>{{if prop.meta_description_score}} {{:prop.meta_description_score}} {{/if}}</td>
                    <td title="{{:prop.store_view}}">{{:prop.store_small}}</td>
                    <td>{{:prop.created_at}}</td>
                    <td>
                        <button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Language" data-id="{{>prop.id}}" class="btn btn-translate-for-other-language">
                            <i class="fa fa-language" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="History" data-id="{{>prop.id}}" class="btn btn-history-list">
                            <i class="fa fa-history" aria-hidden="true"></i>
                        </button>
                        {{if !prop.meta_title_score && prop.parent_id}}
                        <button type="button" title="get Translared Text Score" data-id="{{>prop.id}}" class="btn btn-get-translate-score">
                            <i class="fa fa-dashboard" aria-hidden="true"></i>
                        </button>
                        {{/if}}
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
</script> 