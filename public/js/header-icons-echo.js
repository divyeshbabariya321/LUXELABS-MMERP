if (isPusherInitialized()) {
    listenHeaderIconNotifications();
}

function listenHeaderIconNotifications() {
    window.Echo.private('headerIconNotifications')
        .listen('.HeaderIconNotificationsFound', (e) => {

            let count;
            e.notifications?.map(function (notification) {
                count = notification.ScriptsExecutionHistory?.count;
                if (count > 0) {
                    if ($('.script-document-error-badge').hasClass('hide')) {
                        $('.script-document-error-badge').removeClass('hide');
                    }
                }
            });
            
        });
}