function changeSimulatorSetting(object, objectId, simulator) {
  $.ajax({
    type: "POST",
    url: "/chatbot/messages/update-simulator-setting",
    data: {
      _token: csrftoken,
      object: object,
      objectId: objectId,
      auto_simulator: simulator,
    },
    dataType: "json",
  })
    .done(function (response) {
      if (response.code == 200) {
        toastr["success"](response.messages);
      } else {
        toastr["error"](response.messages);
      }
    })
    .fail(function (response) {
      toastr["error"]("Could not update simulator status");
    });
}
