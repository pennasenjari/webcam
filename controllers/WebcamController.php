<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class WebcamController extends Controller
{

    public function actionStartStreaming()
    {
        $scriptPath = Yii::getAlias('@app/scripts/start-video.sh');
        exec($scriptPath, $output, $returnVar);
        if ($returnVar === 0) {
            //Yii::$app->session->setFlash('success', 'Streaming started successfully.');
            return $this->asJson(['success' => $returnVar === 0]);
          } else {
            //Yii::$app->session->setFlash('error', 'Failed to start streaming.');
            return $this->asJson(['not yet' => $returnVar === 0]);
          }
    }

    public function actionStopStreaming()
    {
        $scriptPath = Yii::getAlias('@app/scripts/stop-video.sh');
        exec($scriptPath, $output, $returnVar);

        return $this->asJson(['success' => $returnVar === 0]);
    }

    public function actionCheckStream()
    {
        $outputDir = Yii::getAlias('@app/live');
        $ready = file_exists("$outputDir/stream.m3u8");

        return $this->asJson(['ready' => $ready]);
    }

    public function actionCheckMotion()
    {
        $motionFile = Yii::getAlias('@app/motion/motion.log');
        $motionDetected = false;
        if (file_exists($motionFile)) {
            $logContents = file_get_contents($motionFile);
            if (strpos($logContents, 'MOTION DETECTED') !== false) {
                $motionDetected = true;
            }
        }

        return $this->asJson(['motionDetected' => $motionDetected]);
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