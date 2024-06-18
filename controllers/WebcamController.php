<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class WebcamController extends Controller
{

    public function actionStartStreaming()
    {
        $scriptPath = Yii::getAlias('@app/scripts/start-ffmpeg-hls.sh');
        exec($scriptPath, $output, $returnVar);
/*
        if ($returnVar === 0) {
            Yii::$app->session->setFlash('success', 'Streaming started successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to start streaming.');
        }
*/
        return $this->asJson(['success' => $returnVar === 0]);
    }

    public function actionStopStreaming()
    {
        $scriptPath = Yii::getAlias('@app/scripts/stop-ffmpeg.sh');
        exec($scriptPath, $output, $returnVar);
/*
        if ($returnVar === 0) {
            Yii::$app->session->setFlash('success', 'Streaming stopped successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to stop streaming.');
        }
*/
        return $this->asJson(['success' => $returnVar === 0]);
    }

/*
public function actionStopStreaming()
{
    // Stop FFmpeg process
    $pidFile = Yii::getAlias('@app/live/ffmpeg.pid');
    if (file_exists($pidFile)) {
        $pid = file_get_contents($pidFile);
        // Use exec to kill the process
        exec("kill $pid", $output, $result);
        if ($result === 0) {
            unlink($pidFile); // Remove the PID file
        }
    }

    // Remove the .m3u8 file and .ts segments
    $outputDir = Yii::getAlias('@app/live');
    $m3u8File = $outputDir . '/stream.m3u8';
    if (file_exists($m3u8File)) {
        unlink($m3u8File); // Remove the .m3u8 file
    }

    // Remove .ts segment files
    $tsFiles = glob($outputDir . '/*.ts');
    foreach ($tsFiles as $file) {
        unlink($file); // Remove each .ts file
    }

    return $this->asJson(['status' => 'success']);
}
*/


    public function actionCheckStream()
    {
        $outputDir = Yii::getAlias('@app/live');
        $ready = file_exists("$outputDir/stream.m3u8");

        return $this->asJson(['ready' => $ready]);
    }

    public function actionStream()
    {
        // Available formats

        // HLS
        // Note: requires JS library in most browsers to display
        $url = 'https://vattu.ddns.net/live/stream.m3u8';
        //$videotype = 'video/mp2t';
        //$videotype = 'application/vnd.apple.mpegurl';
        $videotype = 'application/x-mpegURL';

        // MP4
        //$url = 'https://webcam.local/live/stream.mp4';
        //$videotype = 'video/mp4';

        // Webm
        //$url = 'https://webcam.local/live/stream.webm';
        //$videotype = 'video/webm';
                
        return $this->render('stream', [
            'url' => $url,
            'videotype' => $videotype,
        ]);

    }
}