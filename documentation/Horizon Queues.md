## Queues documentation

### mageone, magetwo, magethree

1. mageone, magetwo, magethree are used to push products, stores etc. to magento website. I guess this was initially used to distribute the load.

### fetch_email

1. Not in use

### product

1. to push ProductAi products

### magento

1. to push product from PushToMagento job. Referenced in `app/Console/Commands/Manual/ManualQueueForMagento.php` file. This command is to push products to magento manually I guess.

### supplier_products

1. this queue is being used in various places like job (UpdateProductCategoryFromErp, UpdateProductColorFromErp, UpdateProductCompositionFromErp, UpdateScrapedCategory, UpdateScrapedColor, UpdateSizeFromErp, UpdateProductCategoryFromErp)

### customer_message

1. this queue is being used in various places like job (AttachSuggestionProduct, SendMessageToCustomer, UpdateOrderStatusMessageTpl, SendMessageToCustomer, AttachImagesSend, UpdateReturnStatusMessageTpl)

### watson_push

1. this queue is being used in various places like job (ManageWatson, PushToWatson, ManageWatsonAssistant)

### email

1. this queue is being used in various places like job (FetchEmail)

### high, image_search, failed_magento_job

1. this queue is not in use.

### command_execution

1. this queue is being used in various places like job (CommandExecution)

### send_email

1. this queue is being used in various places like job (SendEmail)
