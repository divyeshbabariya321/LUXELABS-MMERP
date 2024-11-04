<?php

return [
  'type' => env('GOOGLE_VISION_TYPE'),
  'project_id' => env('GOOGLE_VISION_PROJECT_ID'),
  'private_key_id' => env('GOOGLE_VISION_PRIVATE_KEY_ID'),
  'private_key' => env('GOOGLE_VISION_PRIVATE_KEY'),
  'client_email' => env('GOOGLE_VISION_CLIENT_EMAIL'),
  'client_id' => env('GOOGLE_VISION_CLIENT_ID'),
  'auth_uri' => env('GOOGLE_VISION_AUTH_URI'),
  'token_uri' => env('GOOGLE_VISION_TOKEN_URI'),
  'auth_provider_x509_cert_url' => env('GOOGLE_VISION_AUTH_PROVIDER_X509_CERT_URL'),
  'client_x509_cert_url' => env('GOOGLE_VISION_CLIENT_X509_CERT_URL'),
];