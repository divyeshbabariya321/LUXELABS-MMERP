    <div class="col-md-6">
        <form class="form-inline" method="post" action="<?php echo route("scrap.task-list.create-multiple",[$id]); ?>">
           {!! csrf_field() !!}
         
          <div class="row">
          {{ html()->select("assigned_to", ["" => "Select-user"] + \App\User::pluck("name", "id")->toArray())->class("form-control mb-2 mr-sm-2 select2 col-md-10") }}
          </div>
          <button type="submit" class="btn btn-secondary mb-2 btn-create-task-multiple">Submit</button>
        </form>
    </div>  
    <script>
    $(".select2-quick-reply").select2( { tags: true } );

       
    </script>
