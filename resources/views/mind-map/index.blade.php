@extends('layouts.app')

@section("styles")
<link rel="stylesheet" href="{{ mix('webpack-dist/css/mind_map/common.css') }} ">
<link rel="stylesheet" href="{{ mix('webpack-dist/css/mind_map/app.css') }} ">

<link rel="stylesheet" href="{{ mix('webpack-dist/css/mind_map/minicolors/jquery.miniColors.css') }} ">
@endsection

@section('content')
<script id="template-float-panel" type="text/x-jquery-tmpl">
<div class="ui-widget ui-dialog ui-corner-all ui-widget-content float-panel no-select">
  <div class="ui-dialog-titlebar ui-widget-header ui-helper-clearfix">
    <span class="ui-dialog-title">${title}</span>
    <a class="ui-dialog-titlebar-close ui-corner-all" href="#" role="button">
      <span class="ui-icon"></span>
    </a>
  </div>
  <div class="ui-dialog-content ui-widget-content">
  </div>
</div>
</script>

<script id="template-open-table-item" type="text/x-jquery-tmpl">
<tr>
  <td><a class="title" href="#">${title}</a></td>
  <td>${$item.format(dates.modified)}</td>
  <td><a class="delete" href="#">delete</a></td>
</tr>
</script>


<script id="template-open" type="text/x-jquery-tmpl">
<div id="open-dialog" class="file-dialog" title="Open mind map">
  <h1><span class="highlight">Works again!</span> In System</h1>
  <p>Change later from system</p>
  <button id="button-open-cloud">Open</button>
  <span class="cloud-loading">Loading...</span>
  <span class="cloud-error error"></span>
  <div class="seperator"></div>
  <h1>Local Storage</h1>
  <p>This is a list of all mind maps that are saved in your browser's local storage. Click on the title of a map to open it.</p>
  <table class="localstorage-filelist">
    <thead>
      <tr>
        <th class="title">Title</th>
        <th class="modified">Last Modified</th>
        <th class="delete"></th>
      </tr>
    </thead>
    <tbody class="document-list"></tbody>
  </table>
  <div class="seperator"></div>
  
</div>
</script>

<script id="template-save" type="text/x-jquery-tmpl">
<div id="save-dialog" class="file-dialog" title="Save mind map">
  
  <h1>Local Storage</h1>
  <p>You can save your mind map in your browser's local storage. Be aware that this is still experimental: the space is limited and there is no guarantee that the browser will keep this document forever. Useful for frequent backups in combination with cloud storage.</p>
  <button id="button-save-localstorage">Save</button>
  <input type="checkbox" class="autosave" id="checkbox-autosave-localstorage">
  <label for="checkbox-autosave-localstorage">Save automatically every minute.</label>
  <div class="seperator"></div>
  

</div>
</script>

<script id="template-navigator" type="text/x-jquery-tmpl">
<div id="navigator">
  <div class="active">
    <div id="navi-content">
      <div id="navi-canvas-wrapper">
        <canvas id="navi-canvas"></canvas>
        <div id="navi-canvas-overlay"></div>
      </div>
      <div id="navi-controls">
        <span id="navi-zoom-level"></span>
        <div class="button-zoom" id="button-navi-zoom-out"></div>
        <div id="navi-slider"></div>
        <div class="button-zoom" id="button-navi-zoom-in"></div>
      </div>
    </div>
  </div>
  <div class="inactive">
  </div>
</div>
</script>


<script id="template-inspector" type="text/x-jquery-tmpl">
<div id="inspector">
  <div id="inspector-content">
    <table id="inspector-table">
      <tr>
        <td>Font size:</td>
        <td><div
            class="buttonset buttons-very-small buttons-less-padding">
            <button id="inspector-button-font-size-decrease">A-</button>
            <button id="inspector-button-font-size-increase">A+</button>
          </div></td>
      </tr>
      <tr>
        <td>Font style:</td>
        <td><div
            class="font-styles buttonset buttons-very-small buttons-less-padding">
            <input type="checkbox" id="inspector-checkbox-font-bold" /> 
            <label
            for="inspector-checkbox-font-bold" id="inspector-label-font-bold">B</label>
              
            <input type="checkbox" id="inspector-checkbox-font-italic" /> 
            <label
            for="inspector-checkbox-font-italic" id="inspector-label-font-italic">I</label> 
            
            <input
            type="checkbox" id="inspector-checkbox-font-underline" /> 
            <label
            for="inspector-checkbox-font-underline" id="inspector-label-font-underline">U</label> 
            
            <input
            type="checkbox" id="inspector-checkbox-font-linethrough" />
             <label
            for="inspector-checkbox-font-linethrough" id="inspector-label-font-linethrough">S</label>
          </div>
        </td>
      </tr>
      <tr>
        <td>Font color:</td>
        <td><input type="hidden" id="inspector-font-color-picker"
          class="colorpicker" /></td>
      </tr>
      <tr>
        <td>Branch color:</td>
        <td><input type="hidden" id="inspector-branch-color-picker"
          class="colorpicker" />
          <button id="inspector-button-branch-color-children" title="Apply branch color to all children" class="right buttons-small buttons-less-padding">Inherit</button>
        </td>
      </tr>
    </table>
  </div>
</div>
</script>

<script id="template-export-map" type="text/x-jquery-tmpl">
<div id="export-map-dialog" title="Export mind map">
  <h2 class='image-description'>To download the map right-click the
    image and select "Save Image As"</h2>
  <div id="export-preview"></div>
</div>
</script>

  <div id="print-area">
    <p class="print-placeholder">Please use the print option from the
      mind map menu</p>
  </div>
  
  <div id="">
    
    <div id="topbar">
       
      <div id="toolbar">
        

        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="buttons erp__mm__buttons">
            
                            <span class="buttons-left erp__mindmap_left"> </span> 
                            <span class="buttons-right erp__mindmap_right" style="display: none !important">
                            </span>
                          </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="">
                            <div class="row">
                                <div class="col-sm-12">
                                  <a href="{{route('mind-map.index')}}" class="btn btn-default"> New</a>
                                  
                                  <button class="btn btn-default" type="button" data-toggle="modal" data-target="#MindMapOpen">Open</button>
                                  <button class="btn btn-default" type="button" data-toggle="modal" data-target="#MindMapSave">Save</button>
                                  <button class="btn btn-default" type="button" id="button-save-hdd">Download</button>
                                  <button class="btn btn-default" type="button" id="mm_button-export-image">Export As Image</button>
                                </div>


                            </div>


                        </div>

                    </div>
                    
                </div>
            </div>
        </div>

        

        

      </div>
    </div>
    <div id="canvas-container">
      <div id="drawing-area" class="no-select"></div>
    </div>
    <div id="bottombar">
      
      <div id="statusbar">
        <div
          class="buttons buttons-right buttons-small buttons-less-padding"></div>
      </div>
    </div>
  </div>
  <input type="hidden" name="save_url" id="save_url" value="{{route('mind-map.store')}}">
  <input type="hidden" name="get_id_url" id="get_id_url" value="{{route('mind-map.show',":id")}}">
      
    <div id="MindMapSave" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Save Mind Map</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name">Title</label>
                                <input type="text" class="form-control" id="mind-map-title" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name">Description</label>
                                <textarea id="mind-map-description" class="form-control" cols="20" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnsave" id="button-save-system">Submit</button>
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="MindMapOpen" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Open Mind Map</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label >Select From System</label>
                                <select name="" class="form-control" id="mind-map-system-select">
                                    <option value="">Select</option>
                                    @foreach($mapDiagrams as $mapDiagram)
                                    <option value="{{$mapDiagram->id}}" >{{$mapDiagram->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label >Upload file</label>
                                <div class="file-chooser">
                                    <input type="file" class="form-control" id="mm-local-file-selector" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnsave" id="button-open-system">Submit</button>
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
  @endsection

  @section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="{{ mix('webpack-dist/js/zoom-meetings.js') }} "></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://api.filestackapi.com/filestack.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>

  

  <script type="text/javascript" src="{{ mix('webpack-dist/js/mind_map/script.js') }} "></script> 
  @endsection

