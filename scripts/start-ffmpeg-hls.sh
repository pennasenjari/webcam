#!/bin/bash

# Kill any existing process
source ./stop_ffmpeg.sh

# Path to your FFmpeg executable
FFMPEG_PATH=/usr/bin/ffmpeg

# Video input and output settings
INPUT_DEVICE="/dev/video0"
OUTPUT_DIR="/var/www/webcam/live"
OUTPUT_FILE="$OUTPUT_DIR/stream.m3u8"
PID_FILE="$OUTPUT_DIR/ffmpeg.pid"
LOG_FILE="$OUTPUT_DIR/ffmpeg.log"

# Ensure the output directory exists
mkdir -p $OUTPUT_DIR

# Delay before starting new process (adjust as needed)
#sleep 3

# Start FFmpeg with settings and save the process ID (PID)
$FFMPEG_PATH -f v4l2 -framerate 20 -video_size 320x240 -i $INPUT_DEVICE -vf format=yuv420p -codec:v libx264 -profile:v main -level:v 3.1 -preset ultrafast -tune zerolatency -codec:a aac -b:a 128k -f hls -hls_time 2 -hls_list_size 5 -hls_flags delete_segments+append_list -hls_delete_threshold 3 $OUTPUT_FILE > $LOG_FILE 2>&1 &

echo $! > $PID_FILE

<<comment
$FFMPEG_PATH -f v4l2 -framerate 20 -video_size 320x240 -i $INPUT_DEVICE -c:v libx264 -profile:v main -level:v 3.1 -preset ultrafast -tune zerolatency -c:a aac -b:a 128k -f hls -hls_time 2 -hls_list_size 5 -hls_flags delete_segments+append_list -hls_delete_threshold 3 $OUTPUT_FILE > $LOG_FILE 2>&1 &

Explanation of FFmpeg Command:
-f v4l2: Specifies the input format (Video4Linux2).
-framerate 20: Sets the frame rate to 20 fps.
-video_size 320x240: Sets the video resolution.
-i $INPUT_DEVICE: Specifies the input device.
-vf format=yuv420p: Converts the video format to YUV420p.
-c:v libx264: Uses the H.264 codec for video.
-profile:v main: Sets the H.264 profile to main.
-level:v 3.1: Sets the H.264 level to 3.1.
-preset ultrafast: Uses the ultrafast preset for minimal compression delay.
-tune zerolatency: Tunes for zero latency.
-c:a aac: Uses the AAC codec for audio.
-b:a 128k: Sets the audio bitrate to 128 kbps.
-f hls: Specifies the HLS output format.
-hls_time 2: Sets the duration of each HLS segment to 2 seconds.
-hls_list_size 5: Keeps the last 5 segments in the playlist.
-hls_flags delete_segments+append_list: Deletes old segments and appends new segments to the list.
-hls_delete_threshold 3: Sets the threshold for deleting segments to 3.
comment
