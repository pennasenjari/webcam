#!/bin/bash

# Output directory where the PID file is stored
OUTPUT_DIR="/var/www/webcam/live"
PID_FILE="$OUTPUT_DIR/ffmpeg.pid"

# Stop FFmpeg if it's running
if [ -f $PID_FILE ]; then
    PID=$(cat $PID_FILE)
    kill -9 $PID
    rm $PID_FILE

    # Remove old HLS files
    rm -f $OUTPUT_DIR/*.m3u8 $OUTPUT_DIR/*.ts

    echo "FFmpeg stopped."
else
    echo "FFmpeg is not running."
fi
