# Attribute Replacement

Attribute replement is used to replace value of field_identifier(name, composition and short_description columns) of `products` table if it's added with this grid. .
The Attribute Replacement page is listing data from `index` function in the `AttributeReplacementController` controller.

1. ### Fetch Attribute Replacement from DB:
   - Data for grid will fetch from the `attribute_replacements` table
   - Default order by set as `field_identifier` to fetch oldest record first
2. ### Response:
   - Fetch records need to add in response to load data in datatable grid.
   - `replacements` assign value to `$replacements`
3. ### Listing/Set data into grid:
   Once records fetch from above Eloquent, it formatted to make compatible for server side datatable:
   - `Attribute` column set from `$replacement->field_identifier`
   - `Subject` column set from `$replacement->first_term`
   - `Replace With` column set from `$replacement->replacement_term`
   - `Remark` column set from `$replacement->remarks`
   - `Authorization` column display value of user who authorized previously. In case logged user add new row then have to "authorize" by click on button with same name in this column.
   - `Date` column set from `$replacement->created_at`
   - `Action` column set delete button and it show only if logger user is admin.
4. ### Attribute Replacement Creation:
   A new replacement attribute can be add from first row of table.
   - There are four input controls use to add new attribute replacement.
   - Input field are `field_identifier`(Select attribute), `term`, `replacement_term` and `remark`
   - Fifth column contain `Add` button to save emntered attribute replacement.
