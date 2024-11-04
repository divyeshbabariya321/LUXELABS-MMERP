<div id="sendToWhatsapp" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send to Whatsapp</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('update.password') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <select class="form-control" name="user_id">
                            @foreach($users as $user)
                            <option class="form-control" value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                            <input type="hidden" name="id" id="passwordId"/>
                        <input type="hidden" name="send_message" value="1">
                        <input type="hidden" name="send_on_whatsapp" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
                </form>
        </div>

    </div>
</div>