<div id="newQuestionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mt-0">Add new Question</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('vendor.question.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea class="form-control" name="question" placeholder="Enter Question" value="{{ old('question') }}" required></textarea>

                    @if ($errors->has('question'))
                        <div class="alert alert-danger">{{$errors->first('question')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="number" class="form-control" name="sorting" placeholder="Sorting" value="{{ old('sorting') }}" required>

                    @if ($errors->has('sorting'))
                        <div class="alert alert-danger">{{$errors->first('sorting')}}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-secondary">Add Question</button>
            </form>

            @if(!empty($vendor_questions))
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <th>Question</th>
                            <td class="text-center"><b>Sorting</b></td>
                        </tr>
                        <?php
                        foreach ($vendor_questions as $questions) { ?>
                            <tr>
                                <td><?php echo $questions->question; ?></td>

                                <td><?php echo $questions->sorting; ?></td>    
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
<div id="vqa-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Questions</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Question</th>
                                <th width="20%">Answer</th>
                            </tr>
                        </thead>
                        <tbody class="vqa-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vqa-answer-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Answer</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqa-answer-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="newRQuestionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mt-0">Add Rating Question</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('vendor.rquestion.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea class="form-control" name="question" placeholder="Enter Question" value="{{ old('question') }}" required></textarea>

                    @if ($errors->has('question'))
                        <div class="alert alert-danger">{{$errors->first('question')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="number" class="form-control" name="sorting" placeholder="Sorting" value="{{ old('sorting') }}" required>

                    @if ($errors->has('sorting'))
                        <div class="alert alert-danger">{{$errors->first('sorting')}}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-secondary">Add Question</button>
            </form>

            @if(!empty($rating_questions))
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <th>Rating Question</th>
                            <td class="text-center"><b>Sorting</b></td>
                        </tr>
                        <?php
                        foreach ($rating_questions as $questions_r) { ?>
                            <tr>
                                <td><?php echo $questions_r->question; ?></td>
                                <td><?php echo $questions_r->sorting; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
<div id="vqar-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rating Questions</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Question</th>
                                <th width="20%">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="vqar-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vqar-answer-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rating Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Rating</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqar-answer-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>
<div id="newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.rqastatuscolor') }}" method="POST">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                            <td class="text-center"><b>Action</b></td>
                        </tr>
                        <?php
                        foreach ($status as $status_data) { ?>
                        <tr>
                            <td>
                                <input type="text" name="colorname[<?php echo $status_data->id; ?>]" class="form-control" value="<?php echo $status_data->status_name; ?>">
                            </td>
                            <td style="text-align:center;"><?php echo $status_data->status_color; ?></td>
                            <td style="text-align:center;">
                                <input type="color" name="color_name[<?php echo $status_data->id; ?>]" class="form-control" data-id="<?php echo $status_data->id; ?>" id="color_name_<?php echo $status_data->id; ?>" value="<?php echo $status_data->status_color; ?>" style="height:30px;padding:0px;">
                            </td>     
                            <td>
                                <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-status-rq" title="Delete Status" data-id="{{$status_data->id}}" ><i class="fa fa-trash"></i></button>
                            </td>                         
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div id="vqarnotes-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rating Questions Notes</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('vendor.notes.store') }}" method="POST">
                    @csrf
                    <div class="form-group col-md-10 p-0">
                        <input type="hidden" id="notes_vendor_id" name="vendor_id">
                        <input type="hidden" id="notes_question_id" name="question_id">
                        <textarea class="form-control" name="notes" placeholder="Enter Notes" value="{{ old('notes') }}" required></textarea>

                        @if ($errors->has('notes'))
                            <div class="alert alert-danger">{{$errors->first('notes')}}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-1">
                        <button type="submit" class="btn btn-secondary">Add Notes</button>
                    </div>
                </form>

                <div class="col-md-12 p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th>Note</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="vqarnotes-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="qa-status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="qa-status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary qa-status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>
<div id="qa-newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.qastatuscolor') }}" method="POST">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                            <td class="text-center"><b>Action</b></td>
                        </tr>
                        <?php
                        foreach ($status_q as $status_data_q) { ?>
                        <tr>
                            <td>
                                <input type="text" name="colorname[<?php echo $status_data_q->id; ?>]" class="form-control" value="<?php echo $status_data_q->status_name; ?>">
                            </td>
                            <td style="text-align:center;"><?php echo $status_data_q->status_color; ?></td>
                            <td style="text-align:center;"><input type="color" name="color_name[<?php echo $status_data_q->id; ?>]" class="form-control" data-id="<?php echo $status_data_q->id; ?>" id="color_name_<?php echo $status_data_q->id; ?>" value="<?php echo $status_data_q->status_color; ?>" style="height:30px;padding:0px;"></td> 
                            <td>
                                <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-status-q" title="Delete Status" data-id="{{$status_data_q->id}}" ><i class="fa fa-trash"></i></button>
                            </td>                              
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary qa-submit-status-color">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>