// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js");
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
  apiKey: "<?php env('FCM_API_KEY') ?>",
  authDomain: "<?php env('FCM_AUTH_DOMAIN') ?>",
  projectId: "<?php env('FCM_PROJECT_ID') ?>",
  storageBucket: "<?php env('FCM_STORAGE_BUCKET') ?>",
  messagingSenderId: "<?php env('FCM_MESSAGING_SENDER_ID') ?>",
  appId: "<?php env('FCM_APP_ID') ?>",
  measurementId: "<?php env('FCM_MEASUREMENT_ID') ?>",
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
  
  const title = "Hello world is awesome";
  const options = {
    body: "Your notificaiton message .",
    icon: "/firebase-logo.png",
  };
  return self.registration.showNotification(title, options);
});
