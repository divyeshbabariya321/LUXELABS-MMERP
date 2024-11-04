## FAQ PUSH documentation

### Introduction

    This functionality helps the user to push the FAQ to website stores which is handled with laravel queue.

#### Functionality

    {domain}/reply-list

1. **Changes 1 (Single Push)**
   You will see the action header in table and a new icon over there with symbol of "?"
   once user will click on this icon it will fetch the details of website store and then it will push that particular FAQ to their store website with help of magento URL and api key

2. **Change 2 (Multiple Push)**
   In order to push multiple or all faq's to their respective website store, there is a button in search bar "Push FAQ", this will put the ID of reply in Queue which queue will automatically handle on its own

#### Coding

**New files**

>     `\app\Http\Controllers\FaqPushController.php`
>     Controller helps to push single FAQ as per the ID of reply in queue and queue will process it and hit the API on their magento URL. There are two function in this controller
>     	-	`pushFaq` Which handles the request to push single reply to any single website.
>      	-	`pushFaqAll` which helps to get the data in chunk of 100 and push it one by one in their respective website.

>     `\app\Jobs\ProceesPushFaq.php`
>     Job file is used for queue and dispatch the jobs.

**Edited Files**

>     `\resources\views\reply\list.blade.php`
>     View file edited for two new buttons and JS (click handler)

>     `\routes\web.php`
>     Route file is edited so that we can define the ajax route to insert a record of job.
