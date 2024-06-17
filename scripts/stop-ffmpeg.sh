#!/bin/bash

# Output directory and PID file
OUTPUT_DIR="/var/www/webcam/live"
PID_FILE="$OUTPUT_DIR/ffmpeg.pid"

# Stop FFmpeg process
if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    kill $PID
    rm -f "$PID_FILE"
fi

# Remove the .m3u8 file and .ts segment files
sleep 1 # Added because there was a problem deleting .m3u8 file
rm -f "$OUTPUT_DIR/stream.m3u8"
rm -f "$OUTPUT_DIR"/*.ts
