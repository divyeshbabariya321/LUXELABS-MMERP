<div class="col-sm-12">
    <table class="table table-bordered order-table table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" id="checkAllRow"></th>
                <th>#</th>
                <th>Un - Matched Category</th>
                <th>Products Urls</th>
                <th>Supplier</th>
                <th>Mapped Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if (!$unmapped_categories->isEmpty())
                @foreach ($unmapped_categories as $unmapped_category)
                    <tr>
                        <td><input type="checkbox" class="check-row" data-id="{{ $unmapped_category->id }}"></td>
                        <td>{{ $unmapped_category->id }}</td>
                        <td>{{ $unmapped_category->category_stack_display }}</td>
                        <td><a href="{{ $unmapped_category->product_urls_display }}" target="_blank">{{ $unmapped_category->product_urls_display }}</a></td>
                        <td>{{ $unmapped_category->supplier }}</td>
                        @php
                            $cat_display = $unmapped_category->mapped_category_display;
                        @endphp
                        @if ($cat_display)
                            <td>{{ $cat_display }}</td>
                        @else
                            <td><span class="badge">Not mapped</span></td>
                        @endif
                        <td>
                            @if ($cat_display)
                                <button data-id="{{ $unmapped_category->id }}" data-mapped="{{ $cat_display }}" data-mapped_categories="{{ json_encode($unmapped_category->mapped_categories) }}"
                                    type="button" class="btn btn-primary map-category-action">Update</button>
                            @else
                                <button data-id="{{ $unmapped_category->id }}"
                                    data-cat="{{ $unmapped_category->category_stack_display }}"type="button"
                                    class="btn btn-primary map-category-action">Map</button>
                            @endif

                            <div>

                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">
                        No Records Found
                    </td>
                </tr>
            @endif

        </tbody>
    </table>
    {{ $unmapped_categories->links() }}
</div>

<script>
    clearChecked();
</script>
