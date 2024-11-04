function confirmDialog(options) {
    $.confirm({
        title: options.title,
        theme: 'material',
        content: options.content,
        draggable: false,
        useBootstrap: false,
        boxWidth: '400px',
        buttons: {
            confirm: {
                btnClass: 'btn-blue',
                action: options.confirm
            },
            cancel: {
                btnClass: 'btn-red',
                action: options.cancel
            }
        }
    });
}