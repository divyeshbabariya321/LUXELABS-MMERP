<div class="row">
 <div class="col-lg-12">
     <form>
          {!! csrf_field() !!}
         <div class="row">
             <div class="col">
                 <input type="text" name="instance_id" class="form-control" placeholder="Enter Instance id">
             </div>
             <div class="col">
                 <textarea name="comment" class="form-control" placeholder="Enter comment here"></textarea>
             </div>
             <div class="col">
                 <button class="btn btn-secondary add-instance">ADD</button>
             </div>
             <div class="col">
                 <input type="date" name="date" id="date11">
             </div>
         </div>
     </form>
 </div>
</div>
<div class="row mt-5">
      <div class="col-lg-12">
          <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Instance Id</th>
                  <th>Comment</th>
                  <th>Created At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                   @foreach($instances as $i)
                        <tr>
                           <td>{{$i->id}}</td>
                           <td>{{$i->instance_id}}</td>
                           <td>{{$i->comment}}</td>
                           <td>{{$i->created_at}}</td>
                           <td>
                               <button type="button" data-id="{{$i->id}}" class="btn btn-start-manage-instances"><i class="fa fa-play" aria-hidden="true"></i></button>
                               <button type="button" data-id="{{$i->id}}" class="btn btn-stop-manage-instances"><i class="fa fa-stop" aria-hidden="true"></i></button>
                               <button type="button" data-id="{{$i->id}}" class="btn btn-delete-manage-instances"><i class="fa fa-trash" aria-hidden="true"></i></button>
                               <button type="button" data-id="{{$i->instance_id}}" class="btn btn-log-instances"><i class="fa fa-history" aria-hidden="true"></i></button>
                           </td>
                         </tr>
                   @endforeach
              </tbody>
          </table>
      </div>
</div>