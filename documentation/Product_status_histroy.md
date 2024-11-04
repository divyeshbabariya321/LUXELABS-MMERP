# Product Status Log

Product status Log is used to display products with their status and time of when status was changed.

Genereally, there are total 49 status managed for product in different different stage but in this page, we are showing only 13 status and their time.

Now system is also manage ` Pending` status. When any process start which manage this status that is initialize by ` Pending` status. Once relevant condition satisfy or specific code block execute then status will be changed to active and grid show relevant time under column of that status.

1. ### Product Status Grid:

- It list out products from `product_status_histories` table and this table contain history of each product when it's status has been updated in various stage.
- Data fetch process start from `productScrapLog` function in the `ProductController` controller.
- `productScrapLog` function filter product by `product_id`, `sku`, `select_date` if it's sent in request.
- By default thirteen status [`Scrape`, `Auto crop`, `Final approval`, `Is being cropped`, `Is being scraped`, `Pending products without category`, `Request For external Scraper`, `Send external Scraper`, `Finished external Scraper`, `Unknown Color`, `Unknown Size`, `Unknown Composition`, `Unknown Measurement`] consider to list products and it can filter by specific status if send in request.
- Latest modified record will display first and grid default pagination set to 50.
- Filtered or total product count calculated on each request to show updated one with page.

2.  ### Product Status Filter:

- There are total of four filters available above grid to filter records in grid.
- Filters are date, product id, product status and sku.
- When enter data or select from drop down and click on magnify glass, request sent to same function which mentined in above section and filtered out result by consider each filter as request parameter
