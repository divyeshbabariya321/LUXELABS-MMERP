<div id="datatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('store-website.brand.column.update') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Id</td>
                                <td>
                                    <input type="checkbox" value="Id" id="Id" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Id', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Brand', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Min Price</td>
                                <td>
                                    <input type="checkbox" value="Min Price" id="Min Price" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Min Price', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Max Price</td>
                                <td>
                                    <input type="checkbox" value="Max Price" id="Max Price" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Max Price', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>

                            <?php 
                            foreach($storeWebsite as $k => $title) { 
                                $title= str_replace(' & ','&',$title);
                                $title= str_replace(' - ','-',$title);
                                $title= str_replace('&',' & ',$title);
                                $title= str_replace('-',' - ',$title);
                                $words = explode(' ', $title);
                                $is_short_title=0;
                                if (count($words) >= 2) {
                                    $title='';
                                    foreach($words as $word){
                                        $title.=strtoupper(substr($word, 0, 1));
                                    }
                                    $is_short_title=1;
                                } ?>

                                <tr>
                                    <td><?php echo $title; ?></td>
                                    <td>
                                        <input type="checkbox" value="{{$k}}" id="{{$k}}" name="column" @if (!empty($dynamicColumnsToShow) && in_array($k, $dynamicColumnsToShow)) checked @endif>
                                    </td>
                                </tr>
                            <?php 
                            } ?>  
                        </div>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('form').on('submit', function(event) {
            event.preventDefault();
            
            var selectedColumns = [];
    
            $('input[name="column"]:checked').each(function() {
                selectedColumns.push($(this).val());
            });
                var formData = {
                columns: selectedColumns 
            };
    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/store-website/brand/column-visbility',
                data: formData,
                success: function(response) {
                    toastr["success"]("column Hide Update successfully");
                    location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                    location.reload();
                }
            });
        });
    });
</script>