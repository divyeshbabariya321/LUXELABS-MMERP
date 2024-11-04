{{-- main chatboat modal --}}
<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
            </div>
            <div class="modal-body" style="background-color: #999999;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- end chatboat modal --}}




{{-- another modal --}}

    <div id="sop-knowledgebase-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Seconda popup</h4>

                </div>
                <div class="modal-body" >
                    <div class="form-group d-flex justify-content-between">
                        <div class="">
                            <input type="radio" id="sop_radio" name="sop_or_knowledgebase">
                            <label for="sop_radio">SOP</label>
                        </div>
                        <div>
                            <input type="radio" id="knowledgebase_radio" name="sop_or_knowledgebase">
                            <label for="knowledgebase_radio">Knowledge base</label>
                        </div>
                    </div>
                    <div class="formgroup">
                        <input type="text" name="communation_name" id="communation_name" placeholder="name"> 
                        <label for="communation_name" ></label>
                    </div>
                    <div class="formgroup">
                        <textarea type="discription" name="communicaiton_discription" id="communicaiton_discription" placeholder="Description">  </textarea>
                        <label for="communicaiton_discription" ></label>

                    </div>
            
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
      </div>  
        {{-- end another modal --}}
        <script>




        </script>
