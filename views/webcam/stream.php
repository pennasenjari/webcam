<?php

/* @var $this yii\web\View */
/* @var $url string */
/* @var $videtype string */

$this->title = 'Video Stream';

if ($videotype == 'application/x-mpegURL'): ?>

    <video id="video" width="640" height="480" controls autoplay muted>
        Your browser does not support the video tag.
    </video>

    <script src="/js/hls.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var video = document.getElementById('video');
            var videoSrc = '<?= $url ?>';

            function loadVideo() {
                if (Hls.isSupported()) {
                    var hls = new Hls();
                    hls.loadSource(videoSrc);
                    hls.attachMedia(video);
                    hls.on(Hls.Events.MANIFEST_PARSED, function() {
                        video.play();
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = videoSrc;
                    video.addEventListener('loadedmetadata', function() {
                        video.play();
                    });
                } else {
                    console.error('This browser does not support HLS.');
                }
            }

            function stopVideo() {
                if (hls) {
                    hls.destroy(); // Destroy HLS instance
                }
                video.stopVideo();
                video.src = ""; // Clear video source
            }

            function checkStreamAvailability() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?= \yii\helpers\Url::to(['webcam/check-stream']) ?>');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.ready) {
                            loadVideo(); // Start playing the new stream
                        } else {
                            setTimeout(checkStreamAvailability, 2000); // Check again in 2 seconds
                        }
                    } else {
                        console.error('Failed to check stream availability.');
                    }
                };
                xhr.send();
            }

            document.getElementById('start-streaming').addEventListener('click', function() {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['webcam/start-streaming']) ?>',
                    success: function() {
                        checkStreamAvailability(); // Start checking stream availability
                    }
                });
            });

            document.getElementById('stop-streaming').addEventListener('click', function() {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['webcam/stop-streaming']) ?>',
                    success: function() {
                        stopVideo(); // Stop and clear video
                    }
                });
            });
        });
    </script>
  </div>
 
<?php else: ?>

  <video width="640" height="480" controls autoplay>
      <source src="$url" type="$videotype">
      Your browser does not support the video tag.
  </video>

<?php endif; ?>

<div>
  <button id="start-streaming" class="btn btn-success">Start Streaming</button>
  <button id="stop-streaming" class="btn btn-danger">Stop Streaming</button>
</div>