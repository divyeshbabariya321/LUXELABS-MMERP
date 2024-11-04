//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; //stream from getUserMedia()
var rec; //Recorder.js object
var input; //MediaStreamAudioSourceNode we'll be recording

// shim for AudioContext when it's not avb.
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext; //audio context to help us record

var recordButton = document.getElementById("rvn_recordButton");
var stopButton = document.getElementById("rvn_stopButton");
var pauseButton = document.getElementById("rvn_pauseButton");
var rvn_id = document.getElementById("rvn_id");
var rvn_tid = document.getElementById("rvn_tid");

//add events to those 2 buttons
recordButton.addEventListener("click", startRecording);
stopButton.addEventListener("click", stopRecording);
pauseButton.addEventListener("click", pauseRecording);

function startRecording() {
  recordingsList.innerHTML = "";
  
  document.getElementById("rvn_status").innerHTML =
    "Status: Please wait a moment...";
  /*
		Simple constraints object, for more advanced audio features see
		https://addpipe.com/blog/audio-constraints-getusermedia/
	*/

  var constraints = { audio: true, video: false };

  /*
    	Disable the record button until we get a success or fail from getUserMedia() 
	*/

  recordButton.disabled = true;
  stopButton.disabled = false;
  pauseButton.disabled = false;

  /*
    	We're using the standard promise based getUserMedia() 
    	https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
	*/

  navigator.mediaDevices
    .getUserMedia(constraints)
    .then(function (stream) {
      

      /*
			create an audio context after getUserMedia is called
			sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
			the sampleRate defaults to the one set in your OS for your playback device

		*/
      audioContext = new AudioContext();

      //update the format
      document.getElementById("formats").innerHTML =
        "Format: 1 channel pcm @ " + audioContext.sampleRate / 1000 + "kHz";

      /*  assign to gumStream for later use  */
      gumStream = stream;

      /* use the stream */
      input = audioContext.createMediaStreamSource(stream);

      /* 
			Create the Recorder object and configure to record mono sound (1 channel)
			Recording 2 channels  will double the file size
		*/
      rec = new Recorder(input, { numChannels: 1 });

      //start the recording process
      rec.record();
      document.getElementById("rvn_status").innerHTML =
        "Status: Recording started...";
      
    })
    .catch(function (err) {
      //enable the record button if getUserMedia() fails
      recordButton.disabled = false;
      stopButton.disabled = true;
      pauseButton.disabled = true;
    });
}

function pauseRecording() {
  
  if (rec.recording) {
    //pause
    rec.stop();
    pauseButton.innerHTML = "Resume";
    document.getElementById("rvn_status").innerHTML =
      "Status: Recording paused...";
  } else {
    //resume
    rec.record();
    pauseButton.innerHTML = "Pause";
    document.getElementById("rvn_status").innerHTML =
      "Status: Recording resumed...";
  }
}

function stopRecording() {
  
  document.getElementById("rvn_status").innerHTML =
    "Status: Recording stopped...";
  //disable the stop button, enable the record too allow for new recordings
  stopButton.disabled = true;
  recordButton.disabled = false;
  pauseButton.disabled = true;

  //reset button just in case the recording is stopped while paused
  pauseButton.innerHTML = "Pause";

  //tell the recorder to stop the recording
  rec.stop();

  //stop microphone access
  gumStream.getAudioTracks()[0].stop();

  //create the wav blob and pass it on to createDownloadLink
  rec.exportWAV(createDownloadLink);
}

function createDownloadLink(blob) {
  recordingsList.innerHTML = "";
  var url = URL.createObjectURL(blob);
  var au = document.createElement("audio");
  var div = document.createElement("div");
  var link = document.createElement("a");
  var title = document.createElement("p");

  //name of .wav file to use during upload and download (without extendion)
  var filename = new Date().toISOString().replace(/\D/g, "").substr(0, 14);
  filename = rvn_id.value + "_" + filename + ".wav";
  //add controls to the <audio> element
  au.controls = true;
  au.src = url;
  div.appendChild(document.createElement("br"));
  div.appendChild(document.createElement("br"));
  title.innerHTML = "<strong>Recordings: </strong>" + filename;
  div.appendChild(title);

  //add the new audio element to li
  div.appendChild(au);

  div.appendChild(document.createElement("br"));
  div.appendChild(document.createElement("br"));

  //upload link
  var upload = document.createElement("a");
  upload.className = "btn btn-s btn-secondary";
  upload.href = "/chatbot/messages/upload-audio";
  upload.innerHTML = "Upload & Send Meesage";
  upload.addEventListener("click", function (event) {
    event.preventDefault();
    document.getElementById("loading-image").style.display = "block";
    var xhr = new XMLHttpRequest();
    xhr.overrideMimeType("application/json");
    xhr.onload = function (e) {
      if (this.readyState === 4) {
        //
      }

      if (xhr.status === 200) {
        var jsonResponse = JSON.parse(xhr.responseText);
        
        if (jsonResponse.success) {
          id = rvn_id.value;
          tid = rvn_tid.value;
          document.getElementById("is_audio_" + id).value = 1;

          // for page chatbot/messages
          var messageElement = document.getElementById("message_" + id);
          
          if (typeof messageElement != "undefined" && messageElement != null) {
            document.getElementById("message_" + id).value = jsonResponse.url;
            var sendBtn = document.getElementById("send-message_" + id);
            sendBtn.click();
          }

          // for page development/list
          var messageElement = document.getElementById("send_message_" + id);
          
          if (typeof messageElement != "undefined" && messageElement != null) {
            document.getElementById("send_message_" + id).value =
              jsonResponse.url;
            var sendBtn = document.getElementById("submit_message_" + id);
            sendBtn.click();
          }

          // for page task
          var messageElement = document.getElementById("getMsg" + id);
          
          if (typeof messageElement != "undefined" && messageElement != null) {
            document.getElementById("getMsg" + id).value = jsonResponse.url;
            var sendBtn = document.getElementById("send-message_" + id);
            sendBtn.click();
          }

          var closeBtn = document.getElementById("rvn-btn-close-modal");
          closeBtn.click();
          //document.getElementById("message_"+id).value = jsonResponse.url;
        } else {
          toastr["error"](jsonResponse.message, "error");
        }
      } else {
        toastr["error"]("Oops.something went wrong", "error");
      }
      document.getElementById("loading-image").style.display = "none";
    };
    var token = document.querySelector('meta[name="csrf-token"]').content;
    var fd = new FormData();
    fd.append("audio_data", blob, filename);
    fd.append("_token", token);
    xhr.open("POST", upload.href, true);
    xhr.send(fd);
  });

  div.appendChild(upload); //add the upload link to div

  //add the div element to the ol
  recordingsList.appendChild(div);
}
