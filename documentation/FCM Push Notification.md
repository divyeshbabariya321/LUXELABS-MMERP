# FCM Push Notification

This module is used for send FCM Push Notifications from ERP to android, ios and web application using Firebase. These module are handling with the following tables: `push_fcm_notifications`, `push_fcm_notification_histories` and `fcm_tokens`.

_Used PHP Package:_ https://github.com/brozot/Laravel-FCM

## FCM Notification

This module is used for the manage notifications information.

- **Create:** It can be created by adding data in the create form. User have to enter notification title, notification body, notification website, send at and expired day.
- **Edit:** An edit option is available in this module to edit the all data that we entered in the creation time of the account.
- **Delete** option will permanently delete a records.
- **View Error** option will show errors while occuer on send notification.

## FCM Token

This module is used for the store firebase device token of the user via API.

- **Create:** It can be created by passing data in the create API. In API, have to pass the device id, token, and website.

## FCM Send Notification Command

- This command is used to send the push notification to the user from ERP.
- It updates the notification status success/failed as get as well the history of it.
- This command is run every minute into the server.
