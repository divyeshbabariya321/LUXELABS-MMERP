@extends('layouts.app')


@section('title', 'Plans')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
@section('content')

<link rel="stylesheet" href="{{ mix('webpack-dist/css/common.css') }} ">

    <div id="myDiv">
        <img id="loading-image" src="{{asset('images/pre-loader.gif')}}" style="display:none;" />
    </div>
    <div class="col-md-12 p-0">
        <h2 class="page-heading">Plans page</h2>
    </div>
    @include('partials.flash_messages')
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 margin-tb">

                <!-- <div class="row"> -->

                <form action="{{ url()->current() }}" method="GET" id="searchForm" class="form-inline align-items-start">
                    <div class="form-group col-md-1 mr-3s mb-3 no-pd">
                        <input name="term" type="text" class="form-control" value="{{ request('term') }}"
                            placeholder="Search.." style="width:100%;">
                    </div>
                    <div class="form-group col-md-2 mr-3s mb-3 no-pd">
                        <input name="date" type="date" class="form-control" value="{{ request('date') }}"
                            placeholder="Search.." style="width:100%;">
                    </div>
                    <div class="form-group col-md-2 mr-3s no-pd">
                        <select class="form-control" name="typefilter">
                            <option value="">Select Type</option>
                            @foreach ($typeList as $value)
                                <option value="{{ $value->type }}">{{ $value->type }}</option>
                            @endforeach;
                        </select>
                    </div>
                    <div class="form-group col-md-2 mr-3s no-pd">
                        <select class="form-control" name="categoryfilter">
                            <option value="">Select Category</option>
                            @foreach ($categoryList as $value)
                                <option value="{{ $value->category }}">{{ $value->category }}</option>
                            @endforeach;
                        </select>
                    </div>
                    <div class="form-group col-md-2 mr-3s no-pd">
                        <select class="form-control" name="priority">
                            <option value="">Select priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mr-3s no-pd">
                        <select class="form-control" name="status">
                            <option value="">Select status</option>
                            <option value="complete">complete</option>
                            <option value="pending">pending</option>
                        </select>
                    </div>
                    <div class="col-md-1 no-pd">
                        <button type="submit" class="btn mt-0 btn-image image-filter-btn"><img
                                src="{{asset('images/filter.png')}}" /></button>
                        <a href="{{ route('plan.index') }}" class="btn btn-image" id=""><img
                                src="{{asset('images/resend2.png')}}" style="cursor: nwse-resize;"></a>
                    </div>

                    <div class="col-md-4">
                        <div class="align-right mb-">
                            <button type="button" class="btn btn-secondary new-plan" data-toggle="modal"
                                data-target="#myModal">New plan</button>
                            <button type="button" class="btn btn-secondary new-plan" data-toggle="modal"
                                data-target="#myBasis">New basis</button>
                            <button type="button" class="btn btn-secondary new-type" data-toggle="modal"
                                data-target="#newtype">New Type</button>
                            <button type="button" class="btn btn-secondary new-category" data-toggle="modal"
                                data-target="#newcategory">New Category</button>
                        </div>
                    </div>
                    <!-- </div> -->
                </form>
            </div>

        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered" id="store_website-analytics-table"style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th width="3%">#ID</th>
                        <th width="5%">Type</th>
                        <th width="6%">Category</th>
                        <th width="6%">Subject</th>
                        <th width="7%">Sub subject</th>
                        <th width="8%">Solutions</th>
                        <th width="6%">DeadLine</th>
                        <th width="5%">Budget</th>
                        <th width="4%">Basic</th>
                        <th width="7%">Implications</th>
                        <th width="15%">Priority</th>
                        <th width="8%">Description</th>
                        <th width="15%">Remarks</th>
                        <th width="8%">status</th>
                        <th width="7%">Date</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody class="searchable">
                    @foreach ($planList as $key => $record)
                        <tr>
                            <td style="vertical-align:middle">{{ $record->id }}</td>
                            <td style="vertical-align:middle">{{ $record->type }}</td>
                            <td class="Website-task" style="vertical-align:middle">{{ $record->category }}</td>
                            <td class="Website-task" style="vertical-align:middle">{{ $record->subject }}</td>
                            <td class="Website-task" style="vertical-align:middle">{{ $record->sub_subject }}</td>
                            <td style="display: flex; vertical-align: middle;"><input type="text"
                                    class="form-control solutions" name="solutions"
                                    data-id="{{ $record->id }}"><button type="button"
                                    class="btn btn-image show-solutions" data-id="{{ $record->id }}"><i
                                        class="fa fa-info-circle ml-2"></i></button></td>
                            <td class="r-date" style="vertical-align:middle">{{ $record->deadline }}</td>
                            <td style="vertical-align:middle">{{ $record->budget }}</td>
                            <td class="Website-task"style="vertical-align:middle">{{ $record->basis }}</td>
                            <td class="Website-task"style="vertical-align:middle">{{ $record->implications }}</td>
                            <td style="vertical-align:middle">{{ $record->priority }}</td>
                            <td width="15%" style="vertical-align:middle">
                              <span class="toggle-title-box has-small" 
                                    data-small-title="{{ \Illuminate\Support\Str::limit($record->description, 10, '..') }}"
                                    data-full-title="{{ $record->description ?? '' }}">
                                  {{ $record->description ? (strlen($record->description) > 12 ? \Illuminate\Support\Str::limit($record->description, 10, '..') : $record->description) : '' }}
                              </span>
                            </td>
                            <td>
                                <div style="width: 100%;">
                                    <div class="d-flex">
                                        <input type="text" name="remark_pop"
                                            class="form-control remark-plan{{ $record->id }}"
                                            placeholder="Please enter remark"
                                            style="margin-bottom:5px;width:100%;display:inline;">
                                        <button type="button" class="btn btn-sm btn-image add_remark"
                                            title="Send message" data-record_id="{{ $record->id }}">
                                            <img src="{{ asset('images/filled-sent.png') }}">
                                        </button>
                                        <button data-record_id="{{ $record->id }}"
                                            class="btn btn-xs btn-image show-plan-remark" title="Remark"><img
                                                src="{{ asset('images/chat.png') }}" alt=""></button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="form-control plan-status" name="plan-status">
                                    <option value="">Please Select status</option>
                                    <option value="complete" {{ $record->status == 'complete' ? 'selected' : '' }}
                                        data-id ="{{ $record->id }}">complete</option>
                                    <option value="pending" {{ $record->status == 'pending' ? 'selected' : '' }}
                                        data-id ="{{ $record->id }}">pending</option>
                                </select>
                            </td>

                            <td class="r-date"style="vertical-align:middle">{{ $record->date }}</td>
                            <td class="actions-main"style="vertical-align:middle">
                                <button type="button" class="btn mt-0 btn-secondary edit-plan"
                                    data-id="{{ $record->id }}"><i class="fa fa-edit"></i></button>
                                <a href="{{ route('plan.delete', $record->id) }}" class="btn mt-0 btn-image delete-btn"
                                    title="Delete Record"><img src="{{asset('images/delete.png')}}"></a>
                                <button title="Add step" type="button" class="btn btn-secondary btn-sm add-sub-plan"
                                    data-id="{{ $record->id }}" data-toggle="modal" data-target="#myModal">+</button>
                                <button title="Open step" type="button"
                                    class="btn mt-0 preview-attached-img-btn btn-image no-pd"
                                    data-id="{{ $record->id }}">
                                    <img src="{{asset('images/forward.png')}}" style="cursor: default;">
                                </button>
                                <button title="Open Action" type="button" class="btn  mt-0 plan-action btn-image no-pd"
                                    data-id="{{ $record->id }}">
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="expand-{{ $record->id }} hidden">
                            <th colspan="10" style="border:none;"></th>
                            <th>priority</th>
                            <th>description</th>
                            <th>Remark</th>
                            <th>status</th>
                            <th>date</th>
                            <th>Action</th>
                            @foreach ($record->subList($record->id) as $sublist)
                        <tr class="expand-{{ $record->id }} hidden">
                            <td colspan="10" class="no-border"></td>
                            <td>{{ $sublist->priority }}</td>
                            <td width="15%">
                              <span class="toggle-title-box has-small"
                                    data-small-title="{{ \Illuminate\Support\Str::limit($sublist->description, 10, '..') }}"
                                    data-full-title="{{ $sublist->description ?? '' }}">
                                  {{ $sublist->description ? (\Illuminate\Support\Str::limit($sublist->description, 10, '..')) : '' }}
                              </span>
                            </td>
                            <td width="10%">
                              <span class="toggle-title-box has-small" 
                                    data-small-title="{{ \Illuminate\Support\Str::limit($sublist->remark, 10, '..') }}"
                                    data-full-title="{{ $sublist->remark ?? '' }}">
                                  {{ $sublist->remark ? (\Illuminate\Support\Str::limit($sublist->remark, 10, '..')) : '' }}
                              </span>
                            </td>
                            <td>{{ $sublist->status }}</td>
                            <td>{{ $sublist->date }}</td>
                            <td>
                                <button type="button" class="btn btn-secondary edit-plan"
                                    data-id="{{ $sublist->id }}"><i class="fa fa-edit"></i></button>
                                <a href="{{ route('plan.delete', $sublist->id) }}" class="btn btn-image"
                                    title="Delete Record"><img src="{{asset('images/delete.png')}}"></a>
                            </td>
                        </tr>
                    @endforeach
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="14">{{ $planList->appends(request()->except('page'))->links() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- The Modal -->
    <div class="modal fade" id="myBasis" tabindex="-1" role="dialog" aria-labelledby="myBasis" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myBasis">New basis</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="planadd" action="{{ route('plan.create.basis') }}">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <div class="row subject-field">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Name:</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter name" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newtype" tabindex="-1" role="dialog" aria-labelledby="newtype" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newtype">New Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="typeadd" action="{{ route('plan.create.type') }}">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <div class="row subject-field">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Name:</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter name" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="plan-action" tabindex="-1" role="dialog" aria-labelledby="plan-action"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="plan-action">Plan Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="planactionadd" action="#">
                    <input type="hidden" name="id" value="">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <table class="table table-bordered planactionadd_content_table">
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="plan-solutions" tabindex="-1" role="dialog" aria-labelledby="plan-solutions"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="plan-action">Plan Solutions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="planactionadd" action="#">
                    <input type="hidden" name="id" value="">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Plans</th>
                                </tr>
                                <tbody class="show-plans-here"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newcategory" tabindex="-1" role="dialog" aria-labelledby="newcategory"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newcategory">New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="typeadd" action="{{ route('plan.create.category') }}">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <div class="row subject-field">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Name:</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter name" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModal">Plans</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="planadd" action="{{ route('plan.store') }}">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <div class="row subject-field">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Type</label>
                                        <input type="text" class="form-control" name="type" list="type"
                                            id="plan_type" required="required" />
                                        <datalist id="type">
                                            @foreach ($typeList as $value)
                                                <option value="{{ $value->type }}">{{ $value->type }}</option>
                                            @endforeach;
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Category</label>
                                        <input type="text" class="form-control" name="category" list="category"
                                            id="plan_cat" required="required" />
                                        <datalist id="category">
                                            @foreach ($categoryList as $value)
                                                <option value="{{ $value->category }}">{{ $value->category }}</option>
                                            @endforeach;
                                        </datalist>
                                    </div>
                                </div>
                            </div>
                            <div class="row subject-field">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Subject:</label>
                                        <input type="text" id="plan_sub" name="subject" class="form-control"
                                            required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Sub subject:</label>
                                        <input type="text" name="sub_subject" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Priority:</label>
                                        <select class="form-control" name="priority" required>
                                            <option value="high">High</option>
                                            <option value="medium">Medium</option>
                                            <option value="low">Low</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="edit_id" name="id">
                                <input type="hidden" id="parent_id" name="parent_id">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Status:</label>
                                        <select class="form-control" name="status" required>
                                            <option value="complete">complete</option>
                                            <option value="pending">pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row subject-field">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Budget:</label>
                                        <input type="number" name="budget" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Deadline:</label>
                                        <input type="date" name="deadline" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Basis:</label>
                                        <input type="text" class="form-control" name="basis" list="basis" />
                                        <datalist id="basis">
                                            @foreach ($basisList as $value)
                                                <option value="{{ $value->status }}">{{ $value->status }}</option>
                                            @endforeach;
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Implications</label>
                                        <input type="text" name="implications" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Date:</label>
                                        <input type="date" name="date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Description:</label>
                                        <textarea class="form-control" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row remark-field hidden">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Remark:</label>
                                        <textarea class="form-control" name="remark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mySubModal" tabindex="-1" role="dialog" aria-labelledby="mySubModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModal">Add Sub Plan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="sub_planadd" action="{{ route('plan.store') }}">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Priority:</label>
                                        <select class="form-control" name="priority" required>
                                            <option value="high">High</option>
                                            <option value="medium">Medium</option>
                                            <option value="low">Low</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="sub_edit_id" name="id">
                                <input type="hidden" id="sub_parent_id" name="parent_id">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Status:</label>
                                        <select class="form-control" name="status" required>
                                            <option value="complete">complete</option>
                                            <option value="pending">pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row subject-field">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Budget:</label>
                                        <input type="number" name="budget" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Deadline:</label>
                                        <input type="date" name="deadline" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Basis:</label>
                                        <input type="text" class="form-control" name="basis" list="basis" />
                                        <datalist id="basis">
                                            @foreach ($basisList as $value)
                                                <option value="{{ $value->status }}">{{ $value->status }}</option>
                                            @endforeach;
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Implications</label>
                                        <input type="text" name="implications" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Date:</label>
                                        <input type="date" name="date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Description:</label>
                                        <textarea class="form-control" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row remark-field hidden">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Remark:</label>
                                        <textarea class="form-control" name="remark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="plan-list-remark-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Task Remark</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:1%;">ID</th>
                                    <th style=" width: 12%">Update By</th>
                                    <th style="word-break: break-all; width:12%">Remark</th>
                                    <th style="width: 11%">Created at</th>
                                    <th style="width: 11%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="plan-reamrk-list-view">
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

@endsection

<script>
    $(document).ready(function() {
        $(document).on('click', '.new-plan', function(event) {
            $('#parent_id').val('');
            $('#edit_id').val('');
            $('.remark-field').addClass('hidden');
            $('.subject-field').removeClass('hidden')
            $('#planadd')[0].reset();

        });


        $(document).on('click', '.preview-attached-img-btn', function(e) {
            e.preventDefault();
            var planId = $(this).data('id');
            var expand = $('.expand-' + planId);
            $(expand).toggleClass('hidden');

        });

        //change code to solve bug
        $(document).on('click', '.add-sub-plan', function(event) {
            var id = $(this).data('id');
            $('#sub_edit_id').val('');
            // $('#sub_parent_id').val(id); //not using this modal
            $('#parent_id').val(id);
            $('#sub_planadd')[0].reset();
            $("#plan_type").prop('required', false);
            $("#plan_cat").prop('required', false);
            $("#plan_sub").prop('required', false);
            $('.remark-field').removeClass('hidden');
            $('.subject-field').addClass('hidden');
        });

        //change code to solve bug
        $(document).on('click', '.edit-plan', function(event) {
        $('.remark-field').addClass('hidden');
        $('.subject-field').removeClass('hidden')
        $("#plan_type").prop('required', true);
        $("#plan_cat").prop('required', true);
        $("#plan_sub").prop('required', true);

        $('#planadd')[0].reset();
        var id = $(this).data('id');
        $('#parent_id').val('');
        $('#edit_id').val('');

        $('#edit_id').val(id)

        $.ajax({
            url: "{{ route('plan.edit') }}",
            data: {
                id: id
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                $('input[name="type"]').val(data.object.type);
                $('input[name="category"]').val(data.object.category);
                $('input[name="subject"]').val(data.object.subject);
                $('input[name="sub_subject"]').val(data.object.sub_subject);
                $('select[name="priority"]').val(data.object.priority).change();
                $('select[name="status"]').val(data.object.status).change();
                // $('select[name="basis"]').val(data.object.basis).change();
                $('input[name="basis"]').val(data.object.basis).change();
                $('input[name="date"]').val(data.object.date);
                $('input[name="budget"]').val(data.object.budget);
                $('input[name="deadline"]').val(data.object.deadline);
                $('input[name="implications"]').val(data.object.implications);
                $('textarea[name="description"]').val(data.object.description);
                $('textarea[name="remark"]').val(data.object.remark);
                $('#parent_id').val(data.object.parent_id);
                if (data.object.parent_id != null) {
                    $("#plan_type").prop('required', false);
                    $("#plan_cat").prop('required', false);
                    $("#plan_sub").prop('required', false);
                    $('.remark-field').removeClass('hidden');
                    $('.subject-field').addClass('hidden');
                }
                $('#myModal').modal('toggle');
            } else {
                toastr['error']("Opps! Something went wrong, Please try again.");
            }
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr['error']("Opps! Something went wrong, Please try again.");
        });

        });


        (function($) {
            $('#filter').keyup(function() {
                var rex = new RegExp($(this).val(), 'i');
                $('.searchable tr').hide();
                $('.searchable tr').filter(function() {
                    return rex.test($(this).text());
                }).show();
            })

            $(document).on("click", ".find-records", function(e) {
                e.preventDefault();
                var id = $(this).data("id");
                $.ajax({
                    url: "/store-website-analytics/report/" + id,
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(data) {
                    $("#loading-image").hide();
                    $(".bd-report-modal-lg .modal-body").empty().html(data);
                    $(".bd-report-modal-lg").modal("show");
                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    toastr['error']("Opps! Something went wrong, Please try again.");
                });
            });

        }(jQuery));
    

        $(document).on("click", ".toggle-title-box", function(ele) {
            var $this = $(this);
            if ($this.hasClass("has-small")) {
                $this.html($this.data("full-title"));
                $this.removeClass("has-small")
            } else {
                $this.addClass("has-small")
                $this.html($this.data("small-title"));
            }
        });

        $(document).on('change', '.plan-status', function(e) {
        if ($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected',
                this).attr('data-id') != undefined)) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('plan.status.update') }}",
                data: {
                    status: $('option:selected', this).val(),
                    plan_id: $('option:selected', this).attr('data-id')
                },
                success: function(response) {
                    location.reload();
                    toastr['success'](response.message, 'success');
                },
                error: function(response) {
                    toastr['error']("An error occurred");
                }
            })
        }
        });

        $(document).on("click", ".add_remark", function(e) {
        e.preventDefault();
        var thiss = $(this);
        var plan_id = $(this).data('record_id');
        var remark = $(`.remark-plan` + plan_id).val();
        $.ajax({
            type: "POST",
            url: "{{ route('plan.reamrk.add') }}",
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
                plan_id: plan_id,
                remark: remark,
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            toastr['success'](response.message);
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.message);
        });
        });

        $(document).on("click", ".show-plan-remark", function(e) {
        e.preventDefault();
        var record_id = $(this).data('record_id');
        $.ajax({
            type: "POST",
            url: "{{ route('plan.remark.list') }}",
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
                recordId: record_id,
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $("#plan-list-remark-modal").modal("show");
            $(".plan-reamrk-list-view").html(response.data);
            toastr['success'](response.message);
        }).fail(function(response) {
            toastr['error'](response.message);
        });
        });

        //change code by new requirement
        $(document).on("click", ".plan-action", function(ele) {
        var id = $(this).data('id');
        $("#plan-action").find('input[name="id"]').attr('value', id);
        var url = '{{ route('plan.action.addons', ':id') }}';
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            $('.planactionadd_content_table').html(data);
            $("#plan-action").modal("show");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            $("#loading-image").hide();
            toastr['error']("Opps! Something went wrong, Please try again.");
        });
        });
        $(document).on("click", ".show-solutions", function(ele) {
        var id = $(this).data('id');
        var url = '{{ route('plan.show.solutions', ':id') }}';
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            //show-plans-here
            var $html = '';
            $.each(data, function(i, item) {
                $html += "<tr>";
                $html += "<td>" + item.solution + "</td>";
                $html += "</tr>";
            });
            $('.show-plans-here').html($html)
            $("#plan-solutions").modal("show");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            $("#loading-image").hide();
            toastr['error']("Opps! Something went wrong, Please try again.");
        });
        });
        $(document).on("keyup", ".solutions", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            if ($(this).val().length > 0) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('plan.solution.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        solution: $(this).val(),
                        id: $(this).data('id')
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(data) {
                    $("#loading-image").hide();
                    console.log(data.strength);
                    $("#plan-action").modal("hide");
                    toastr["success"]('Data save successfully.');
                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    $("#plan-action").modal("hide");
                    toastr["error"]('An error occured!');
                });
            }
        }
        });




        //change code by new requirement
        $(document).on("submit", "#planactionadd", function(event) {
        event.preventDefault();
        let form = $('#planactionadd')[0];
        let formData = new FormData(form);
        $.ajax({
            type: 'POST',
            url: "{{ route('plan.action.store') }}",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            $("#plan-action").modal("hide");
            toastr["success"]('Data save successfully.');
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            $("#loading-image").hide();
            $("#plan-action").modal("hide");
            toastr["error"]('No record found!');
        });
        });

        $(document).on("click", ".delete_field", function() {
        $(this).closest('.removable_class').remove();
        });

        $(document).on("click", ".add_plan_action_data", function() {

        let data_id = $(this).attr('data-id');
        let field_name = '';
        let field_name_active = '';
        if (data_id == 1) {
            field_name = 'plan_action_strength';
            field_name_active = 'plan_action_strength_active';
        } else if (data_id == 2) {
            field_name = 'plan_action_weakness';
            field_name_active = 'plan_action_weakness_active';
        } else if (data_id == 3) {
            field_name = 'plan_action_opportunity';
            field_name_active = 'plan_action_opportunity_active';
        } else {
            field_name = 'plan_action_threat';
            field_name_active = 'plan_action_threat_active';
        }
        create_plan_action_tr(field_name, field_name_active, data_id)
        });

        function create_plan_action_tr(field_name, field_name_active, data_id) {

        let html = `<tr class="removable_class">
                        <td style="vertical-align:middle" colspan="4">
                        <input type="text" name="${field_name}[]" class="form-control">
                        </td>
                        <td class="actions-main actions-main-sub w-100" style="vertical-align:middle">
                            <button type="button" class="btn btn-secondary btn-sm delete_field">-</button>
                        </td>
                    </tr>`;

        $('.plan_action_tbody[data-id="' + data_id + '"]').append(html);
        }
    });
</script>
