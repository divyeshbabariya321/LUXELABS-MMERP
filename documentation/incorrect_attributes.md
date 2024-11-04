# Incorrect Attributes

Incorrect attributes is representation of Products which has incorrect attributes values received from the response of scrap product.

      In this module mainly there are three sections: Filter, Bulk update and Grid.

### 1. Incorrect Attribute Count

- At top of page, showing total number of `product count`. By default total count calculated for the product which have incorrect attributes. This count will update based on filter selection.

### 2. Grid Data

- Grid data is displaying from `index` method of `UnknownAttributeProductController` controller by fetch records from `products` table.
- Grid data contain 8 columns `Product Id`, `Product Name`, `SKU`, `Supplier`, `Attribute`, `Original Value`, `ERP Value` and `Action`. Action column contains two buttons, first one is for update individual attribute and second one is to get history.
- Update button allows to replace attribute's originial(current) value with erp(new) value.
- On click update button request will go to `updateAttributeAssignment` method of `UnknownAttributeProductController` controller to update selected record. Once it successfully update in table, updated value will reflected in grid without refresh page.
- History/list button will open one popup to list out history of changed attribute value. If attributes updated multiple time, number of rows will display latest changed record at top.
- On click list button request will go to `getProductAttributeHistory` method of `UnknownAttributeProductController` controller and fetch records from `product_updated_attribute_histories` table. All fetched records will display in popup.
- Grid row text color:
  - In grid, row color may be very. It can be either black, red or green.
  - When product save from scarpper, default job status is pending so those records display in black, if job success for products, those row will display in gree and red color row indicated that job failed.

### 3. Filters

- Filter option is available at top left corner. It contain `Attributes`, `Stock` and `Job Status` drop down.
  - First filter contains four types of attribute `Unknown Size`, `Unknown Measurement`, `Unknown Category`, and `Unknown Color`.
  - Second filter is for stock: Select all product, out of stock and in stock.
  - Third drop down is to filter records based on job status: Pending, Success and Failed.
    1. Pending
    - This option is to know how many jobs are pending or not started yet.
    2. Success
    - If job executed for perticular product then it's status changed to Success.
    3. Failed
    - Indicate failed job of products.
- On select one or more filter, request comes to `index` method of `UnknownAttributeProductController` controller and get filtered records from `products` table by applying filter on relevant column.
- This function will return array/no of records which will update total numbers of product count as well as grid data without page reload.

### 4. Bulk Update

- Bulk update section available at top right corner of page that allow us to assign/replace attribute values of multiple products.
- First choose for which `Attribute Assignment` need to update attribute value. It contains `Unknown Size`, `Unknown Measurement`, `Unknown Category`, and `Unknown Color`.
- Based on selection second(`Original`) and third drop down(`ERP`) changed to show relevant value without reload page.
- Select value from second drop down which you want to replace while select from third drop down to replace value of second one.
- At last assign button that allow us to start bulk update process in queue & jobs.
- This bulk update process requested to `attributeAssignment` function of same controller which will first validate request and dispatch queue `attribute_assignment` if is validated successfully.
- This job will update it's status and manage history in `product_updated_attribute_history` table.
- Various job status are available in third drop down of filer section
