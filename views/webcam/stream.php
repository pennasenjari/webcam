<?php

$this->title = 'Video Stream';

if ($videotype == 'application/x-mpegURL'): ?>
<div>
    <video id="video" width="640" height="480" controls autoplay muted>
        Your browser does not support the video tag.
    </video>

    <button id="toggle-streaming" style="display: block" data-streaming="false" class="btn btn-success">Start Streaming</button>

    <div id="streaming-indicator" style="display: none;">Starting stream, this can take a moment...</div>

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var video = document.getElementById('video');
        var indicator = document.getElementById('streaming-indicator');
        var toggleButton = document.getElementById('toggle-streaming');
        var videoSrc = '<?= $url ?>';
        var hls = null; // Initialize HLS object

        function loadVideo() {
            indicator.style.display = 'none'; // Hide the indicator
            if (Hls.isSupported()) {
                hls = new Hls({
                    capLevelToPlayerSize: true,
                    maxBufferLength: 30,
                    debug: true
                });
                hls.loadSource(videoSrc);
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    video.play();
                });
                hls.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        switch (data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.error('Network error:', data);
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.error('Media error:', data);
                                if (data.details === 'bufferAddCodecError') {
                                    console.error('Codec issue detected:', data);
                                    // Try a fallback or re-encode strategy
                                }
                                break;
                            default:
                                console.error('An error occurred:', data);
                                break;
                        }
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = videoSrc;
                video.addEventListener('loadedmetadata', function() {
                    video.play();
                });
                video.addEventListener('error', function(e) {
                    console.error('Video element error:', e);
                });
            } else {
                console.error('This browser does not support HLS video type.');
            }
        }

        function stopVideo() {
            if (hls) {
                hls.destroy(); // Destroy HLS instance
                hls = null;
            }
            video.pause();
            video.src = ""; // Clear video source
            video.load(); // Reset the video element
            toggleButton.classList.remove("btn-danger");
            toggleButton.classList.add("btn-success");
        }

        function checkStreamAvailability() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?= \yii\helpers\Url::to(['webcam/check-stream']) ?>');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.ready) {
                        loadVideo(); // Start playing the new stream
                        toggleButton.textContent = 'Stop Streaming';
                        toggleButton.dataset.streaming = 'true';
                        toggleButton.classList.remove("btn-success");
                        toggleButton.classList.add("btn-danger");
                    } else {
                        setTimeout(checkStreamAvailability, 1000); // Check again in second
                    }
                } else {
                    console.error('Failed to check stream availability.');
                }
            };
            xhr.onerror = function() {
                console.error('Network error while checking stream availability.');
            };
            xhr.send();
        }

        toggleButton.addEventListener('click', function() {
            if (toggleButton.dataset.streaming === 'false') {
                indicator.style.display = 'block'; // Show the indicator
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['webcam/start-streaming']) ?>',
                    success: function() {
                        checkStreamAvailability(); // Start checking stream availability
                    },
                    error: function() {
                        console.error('Failed to start streaming.');
                    }
                });
            } else {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['webcam/stop-streaming']) ?>',
                    success: function() {
                        stopVideo(); // Stop and clear video
                        toggleButton.textContent = 'Start Streaming';
                        toggleButton.dataset.streaming = 'false';
                        toggleButton.class.streaming = 'false';
                    },
                    error: function() {
                        console.error('Failed to stop streaming.');
                    }
                });
            }
        });
    });
    </script>
</div>

<?php else: ?>

  <video width="640" height="480" controls autoplay>
      <source src="<?= $url ?>" type="<?= $videotype ?>">
      Your browser does not support the video tag.
  </video>

<?php endif; ?>
