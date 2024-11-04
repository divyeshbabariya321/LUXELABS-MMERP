<script type="text/x-jsrender" id="template-result-block">
    <div class="table-responsive mt-3" {{:pageUrl}}>
        <table class="table table-bordered"style="table-layout:fixed;">
            <thead>
              <tr>
                <th width="3%"></th>
                <th width="5%">Id</th>
                <th width="5%">Original Title</th>
                <th width="5%">Title</th>
                <th width="5%">Original Meta Title</th>
                <th width="5%">Meta Title</th>
                <th width="5%">Original Meta Keywords</th>
                <th width="5%">Meta Keywords</th>
                <th width="10%">Original Meta Description</th>
                <th width="10%">Meta Description</th>
                <th width="5%">Original Content Heading</th>
                <th width="5%">Content Heading</th>
                <th width="10%">Original Content</th>
                <th width="10%">Content</th>
                <th width="5%">Created at</th>
                <th width="4%">Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}} 
                  <tr>
                    <td><input type="checkbox" class="groups" name="groups[]" value="{{:prop.id}}"></td>
                    <td>&nbsp;{{:prop.id}}</td>
                    
                    <td class="Website-task" title="{{:prop.original_page.title}}">{{:prop.original_page.title}} </td>
                    <td class="Website-task" title="{{:prop.title}}">{{:prop.title}} </td>
                    
                    <td class="Website-task" title="{{:prop.original_page.meta_title}}">{{:prop.original_page.meta_title}}</td>
                    <td class="Website-task" title="{{:prop.meta_title}}">{{:prop.meta_title}}</td>
                    
                    <td class="Website-task" title="{{:prop.original_page.meta_keywords}}">{{:prop.original_page.meta_keywords}}</td>
                    <td class="Website-task" title="{{:prop.meta_keywords}}">{{:prop.meta_keywords}}</td>
                    
                    <td class="Website-task" title="{{:prop.original_page.meta_description}}">{{:prop.original_page.meta_description}}</td>
                    <td class="Website-task" title="{{:prop.meta_description}}">{{:prop.meta_description}}</td>
                    
                    <td class="Website-task" title="{{:prop.original_page.content_heading}}">{{:prop.original_page.content_heading}}</td>
                    <td class="Website-task" title="{{:prop.content_heading}}">{{:prop.content_heading}}</td>
                    
                    <td class="Website-task" title="">
                      <a class="open-page-content-modal" data-id="{{:prop.id}}" href="#">View Content</a>
                        <div class="modal rt-page-modal" id="page-content-modal-{{:prop.id}}" role="dialog">
                          <div class="modal-dialog modal-lg " role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Original Page Content</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                {{:prop.original_page.content}}
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                
                              </div>
                            </div>
                          </div>
                        </div>  
                    
                    </td>
                    <td class="Website-task "  title="">
                      <a class="open-page-content-modal" data-id="{{:prop.id}}" href="#">View Content</a>
                      <div class="modal rt-page-modal" id="page-content-modal-{{:prop.id}}" role="dialog">
                        <div class="modal-dialog modal-lg " role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Page Content</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              {{:prop.content}}
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                   
                    
                    <td>{{:prop.created_at}}</td>
                    <td>
                        <button type="button" title="Changes & Approve Translate" data-id="{{>prop.id}}" class="btn btn-edit-template" style="padding: 0px 1px !important;">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
</script> 