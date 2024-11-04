# Quick replies

This page helps to create multiple replies with combination of **category**, **Sub category**, **Sub Sub category** and **website**.

## Route

> quick-replies

## Controller File

> app\Http\Controllers\QuickReplyController.php

## Method function

> quickReplies

## View File

> resources\views\quick_reply\quick_replies.blade.php

## Functionality

> First of all while loading the page, System get all parents categories and depend upon the requested parameter fetch category by ID,
> After that, System fetch all replies from table and modified it as per the way, so that without any effort, in view file we can publish and show it to all users on click of icon in Modal (Bootstrap)

## View Side Rendering

System collect all the variables and use it in view file.
A grid is created with combination of categories and websites, Every website has an option to enter the data respective to any category and any nested category.
Please take a reference from this screen shot
https://prnt.sc/4_KL8c4776eY

There are two buttons Plus Icon in order to add new reply for that particular combination and second view Icon, So that user can view the replies in popup.

1. Plus Icon
   on click of this icon, a input box will appear which will let the user enter the text and then user can click on "tick" icon, this will post the data to "save-store-wise-reply" this route and save and refresh the page in order to get latest data again.
2. View Icon
   On click of this icon, a popup Modal will appear with all replies that are associate with the category and website.

## Filters

A filter is available which is for filtering the data basis on the subcategory or nested subcategories.

## Add New category

A simple text filed is available at the top of the page, where user can enter the category text and click on **plus icon** the category will be automatically added in the list.
