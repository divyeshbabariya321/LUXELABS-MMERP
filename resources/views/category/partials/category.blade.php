<tr>
    <td>
        {{ \App\Helpers\CategoryHelper::getParentCategoryNamesRecursive($category) }}
        <button style="padding-right:0px;" type="button" class="btn btn-xs show-history-btn" title="Show History"
            data-id="{{ $category->id }}">
            <i class="fa fa-info-circle"></i>
        </button>
    </td>
    <td>
        <div data-cat-id="{{ $category->id }}" class="col-md-8 category-mov-btn">
            @php $options = explode(',', $category->references) @endphp
            @if (count($options) > 0)
                @foreach ($options as $option)
                    @if (strlen($option) > 1)
                        <span class="btn btn-secondary">{{ $option }} <i data-name="{{ $option }}"
                                class="fa fa-eye call-used-product"></i></span>
                    @endif
                @endforeach
            @else
                &nbsp;
            @endif
        </div>
    </td>
    <td>
        <div data-cat-id="{{ $category->id }}" class="category-next-btn">
            {{ html()->select('status_after_autocrop', $allStatus, $category->status_after_autocrop)->class('select2 form-control status_after_autocrop')->style('width:200px;') }}
        </div>
    </td>
</tr>


@foreach ($category->getSubChilds as $childCategory)
    @include('category.partials.category', ['category' => $childCategory])
@endforeach
