<?php

return [
    'account_sid'       => env('TWILIO_ACCOUNT_SID', 'AC9bd16a33827a3f7ee46390fdae2286f5'), //'AC5fc748210ade30f991cea8666c2c9580',
    'auth_token'        => env('TWILIO_AUTH_TOKEN', 'd2c33280d65aaaa8c97ba52f653b4e05'), //'518bd5f099967756a93962fb1e9904eb',
    'caller_id'         => ['+14704105322', '+14012404685', '+15104054253'], //['+918000403018'],
    'webrtc_app_sid'    => env('TWILIO_WEBRTC_APP_SID', 'APb4f1d8c7afdc14a4030f0ae07d323f32'), //'AP92a0fd1e5f2b3198ca8bc5feee8a64d3',
    'default_caller_id' => env('TWILIO_DEFAULT_CALLER_ID', '+14704105322'), //'+918000403018',
    'conference_sid'    => env('TWILIO_CONFERENCE_ID', 'CA25e16e9a716a4a1786a7c83f58e30482'), //'EHf1e04dc8c9a92887c24416e504fae008',
];
