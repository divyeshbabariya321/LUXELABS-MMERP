### Link your developer account to a new or existing Google Cloud project.

[![Link your developer account to a new or existing Google Cloud project](https://github.com/ludxb/erp/blob/8a1431797e6cd8fdf0768149c57fc50714c531d2/documentation/images/link1.jpg)]

[![Link your developer account to a new or existing Google Cloud project](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/link_youdev_2.png)]

### Enable the Google Play Developer Reporting API for your linked Google Cloud project.

[![Enable the Google Play Developer Reporting API](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/enable_the_google_play_dev1.png)]

[![Enable the Google Play Developer Reporting API](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/enable_the_google_play_dev2.png)]

### Set up a service account with appropriate Google Play Console permissions to access the Google Play Developer Reporting API.

[![Set up Service Account](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/save_service_acc1.png)]

### Grant access to provide the service account the necessary permissions to perform actions.

[![Grant Access](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/grant_access_1.png)]

.

# Scopes of Authorization

## OAuth scope

https://www.googleapis.com/auth/playdeveloperreporting is necessary.

[![Grant Access](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/scopes_of_auth.png)]

# Crashes, an active user over a certain period, ANR API is accessible

Crashes

## Using the following credentials

## Generated JSON:

'''
{"web":{"client_id":"898789820680-43onpg3elesf3pqhrjtqd2toku4r7es1.apps.googleusercontent.com","project_id":"santhila-208405","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://oauth2.googleapis.com/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_secret":"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","redirect_uris":["https://oauth.pstmn.io/v1/browser-callback"]}}
'''

## URL: https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.santhilag.starmarket/crashRateMetricSet:query

## METHOD: POST

## REQUEST BODY :

'''
{
"timeline_spec": {
"aggregation_period": "DAILY",
"start_time": {
"year": "2023",
"month": "1",
"day": "24"
},
"end_time": {
"year": "2023",
"month": "1",
"day": "25"
}
},
"dimensions": [
"apiLevel",
"versionCode",
"deviceModel",
"deviceType"
],
"metrics": [
"distinctUsers",
"crashRate7dUserWeighted",
"crashRate28dUserWeighted",
"userPerceivedCrashRate"
],
"page_size": "10"
}

'''
We have tested in postman

[![post_crushmetric](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/post_crashmetricset1.png)]

### Method:Get

##https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.santhilag.starmarket/crashRateMetricSet
[![get_crushmetric](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/get_crashmetricset.png)]

# AnrRateMetricSet:

## Method: GET

## https://playdeveloperreporting.googleapis.com/v1beta1/apps/com.santhilag.starmarket/anrRateMetricSet

[![get_crushmetric](https://github.com/ludxb/erp/blob/3d205c0564e63526bb3585eb7ab50600a6c55aa2/documentation/images/anrratemetricset_get.png)]
