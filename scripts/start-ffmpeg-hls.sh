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

# Pre-create a basic .m3u8 file
#echo "#EXTM3U" > $OUTPUT_FILE
#echo "#EXT-X-VERSION:3" >> $OUTPUT_FILE
#echo "#EXT-X-TARGETDURATION:2" >> $OUTPUT_FILE
#echo "#EXT-X-MEDIA-SEQUENCE:0" >> $OUTPUT_FILE

# Start FFmpeg with faster settings and save the process ID (PID)
#$FFMPEG_PATH -f v4l2 -framerate 25 -video_size 640x480 -i $INPUT_DEVICE -preset ultrafast -tune zerolatency -codec:v libx264 -codec:a aac -f hls -hls_time 2 -hls_list_size 5 -hls_flags delete_segments+append_list $OUTPUT_FILE  > $LOG_FILE 2>&1 &
#$FFMPEG_PATH -f v4l2 -framerate 25 -video_size 320x240 -i $INPUT_DEVICE -preset ultrafast -tune zerolatency -codec:v libx264 -codec:a aac -f hls -hls_time 2 -hls_list_size 5 -hls_flags delete_segments+append_list $OUTPUT_FILE  > $LOG_FILE 2>&1 &
$FFMPEG_PATH -f v4l2 -framerate 20 -video_size 320x240 -i $INPUT_DEVICE -preset ultrafast -tune zerolatency -codec:v libx264 -codec:a aac -f hls -hls_init_time 1 -hls_time 1 -hls_list_size 10 -hls_flags delete_segments+append_list $OUTPUT_FILE  > $LOG_FILE 2>&1 &
echo $! > $OUTPUT_DIR/ffmpeg.pid
