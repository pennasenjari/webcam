#!/bin/bash

# Path to your FFmpeg executable
FFMPEG_PATH=/usr/bin/ffmpeg

# Video input and output settings
INPUT_DEVICE="/dev/video0"
OUTPUT_DIR="/var/www/webcam/live"
OUTPUT_FILE="$OUTPUT_DIR/stream.m3u8"
LOG_FILE="$OUTPUT_DIR/ffmpeg.log"

# Ensure the output directory exists
mkdir -p $OUTPUT_DIR

# Remove old HLS files
rm -f $OUTPUT_DIR/*.m3u8 $OUTPUT_DIR/*.ts

# Start FFmpeg and save the process ID (PID)
/usr/bin/ffmpeg -f v4l2 -i $INPUT_DEVICE -codec:v libx264 -codec:a aac -f hls -hls_time 10 -hls_list_size 5 -hls_flags delete_segments+append_list $OUTPUT_FILE > $LOG_FILE 2>&1 &
echo $! > $OUTPUT_DIR/ffmpeg.pid
