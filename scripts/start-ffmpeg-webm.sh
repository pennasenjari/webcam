#!/bin/bash

# Path to your FFmpeg executable
FFMPEG_PATH=/usr/bin/ffmpeg

# Video input and output settings
INPUT_DEVICE="/dev/video0"
OUTPUT_DIR="/var/www/webcam/live"
OUTPUT_FILE="$OUTPUT_DIR/stream.webm"

# Ensure the output directory exists
mkdir -p $OUTPUT_DIR

# Start FFmpeg
$FFMPEG_PATH -f v4l2 -i $INPUT_DEVICE -c:v libvpx -c:a libvorbis -f webm $OUTPUT_FILE -y
