# Mark Website Store with Tag

Mark a website store with a tag, so that with the help of single text we can get all child website as well

## Model File

> app\Models\WebsiteStoreTag.php

## Controller File

> Modules\StoreWebsite\Http\Controllers\StoreWebsiteController.php

## Method function

> create_tags, attach_tags

## View File

> Modules\StoreWebsite\Resources\views\index.blade.php
> Modules\StoreWebsite\Resources\views\templates\list-template.php

## Route File

> Modules\StoreWebsite\Routes\web.php

- create-tag, attach-tag

## JS file

> public\js\store-website.js

## Functionality

> First of all user need to create a tag related to website if its not exist in the list.
> There are two buttons on page URL "store-website"

1.  Create Tag

- On click on this button, A popup will appear with a single text box where user can enter text (where system will autoomatically create **slug** of that text) .

2. List Tags

- On click on this button, A popup will appear with list of tags.

## Assignment

- User can assign the tag to any specific single website store, by click on the action button and link Icon in last of the action list.
- A popup will appear with drop down list of all tags, Where user can select a tag for website.
