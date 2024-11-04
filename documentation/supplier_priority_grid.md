# Supplier Priority Module

Supplier Priority Module is for list out and update priorities for suppliers.At a time only one supplier have one priorites.For e.g. If I change priority for one supplier from Number "2" to "3" it will check for suppliers who have have priority Number "3" and it will downgrade that priorites to number "4".  
Priorites are dynamic so you can add as many as priorities."Manage Supplier Priority" button we can add priorities and list of priorities are also available in it.

- On click "Add" priority button request go to `addNewPriority` function in the `SupplierController` controller.This priorities are stored in `supplier_priority` table.

1. ### Supplier Priority Grid:

- Data Fetched from `getPrioritiesList` function in the `SupplierController` controller.

2. ### Supplier Priority Filter:

- On click `Filter` button request will go to `getPrioritiesList` function in the `SupplierController` controller and check for filter is for `supplier` or `priority` and based on that records will fetch.If there is priority value `Not set` in request it will check for null priorities in `supplier` table and fetch records.

3.  ### Supplier Priority Update:

- On click `Edit Icon` In grid It will open modal and fetch supplier detail and also option to change priority, after selected priority on `update` button click request will go to `updateSupplierPriority` function in in the `SupplierController` controller.Here it will check for `supplier_id` and `priority` and pass paramters to `updatePriority` function in `SupplierPriorityTrait` trait and update `priority` for that supplier and downgrade priority by "1" for other suppliers who has equal and higher priorities.
