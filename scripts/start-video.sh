#!/bin/bash

set -x  # Enable debugging

SCRIPT=$(readlink -f "$0")
SCRIPT_DIR=$(dirname "$SCRIPT")
FFMPEG=/usr/bin/ffmpeg
LIVE_DEVICE="/dev/video0"
VIRTUAL_DEVICE="/dev/video99"
LIVE_DIR="/var/www/webcam/live"
LOG_DIR="/var/www/webcam/log"

LIVE_FILE="$LIVE_DIR/stream.m3u8"
LIVE_PID="$LIVE_DIR/ffmpeg.pid"

MOTION_CONF="$SCRIPT_DIR/motion.conf"

LIVE_LOG="$LOG_DIR/ffmpeg.log"
MOTION_LOG="$LOG_DIR/motion.log"
SCRIPT_LOG="$LOG_DIR/script.log"

# Ensure directories exist
mkdir -p "$LIVE_DIR" "$LOG_DIR"

# Truncate old logs
truncate -s 0 "$LIVE_LOG"
truncate -s 0 "$MOTION_LOG"
truncate -s 0 "$SCRIPT_LOG"

# Check if v4l2loopback module is loaded
if ! lsmod | grep -q v4l2loopback; then
    echo "v4l2loopback module not loaded, attempting to load..."
    sudo modprobe v4l2loopback video_nr=99
    sleep 1  # Wait for module to initialize
fi

# Check if /dev/video99 exists
if [ ! -e "$VIRTUAL_DEVICE" ]; then
    echo "Virtual device $VIRTUAL_DEVICE not found. Exiting..."
    exit 1
fi

# Stream from physical device to virtual device
{
    echo "Starting FFmpeg to stream from $LIVE_DEVICE to $VIRTUAL_DEVICE"
    $FFMPEG -loglevel warning -f v4l2 -framerate 20 -video_size 320x240 -i $LIVE_DEVICE -f v4l2 $VIRTUAL_DEVICE
    echo "FFmpeg (virtual device) started with PID $!"
} >> "$LIVE_LOG" 2>&1 &

FFmpeg_VIRTUAL_PID=$!
echo $FFmpeg_VIRTUAL_PID > $LIVE_PID

sleep 2  # Allow FFmpeg to start streaming to the virtual device

# Start FFmpeg for HLS streaming from the virtual device
{
    echo "Starting FFmpeg for HLS streaming from $VIRTUAL_DEVICE"
    $FFMPEG -loglevel warning -f v4l2 -framerate 20 -video_size 320x240 -i $VIRTUAL_DEVICE -c:v libx264 -preset ultrafast -map 0:v -f hls -hls_time 2 -hls_list_size 10 $LIVE_FILE
    echo "FFmpeg (streaming) started with PID $!"
} >> "$LIVE_LOG" 2>&1 &

FFMPEG_PID=$!
echo $FFMPEG_PID >> $LIVE_PID

# Start motion detection using the virtual device
{
    echo "Starting Motion detection using $VIRTUAL_DEVICE"
    motion -c $MOTION_CONF
    echo "Motion started with PID $!"
} >> "$MOTION_LOG" 2>&1 &

MOTION_PID=$!

# Confirm the processes are running
{
    sleep 2
    if ps -p $FFMPEG_PID > /dev/null; then
        echo "FFmpeg is running"
    else
        echo "FFmpeg failed to start"
        cat "$LIVE_LOG"
    fi

    if ps -p $MOTION_PID > /dev/null; then
        echo "Motion is running"
    else
        echo "Motion failed to start"
        cat "$MOTION_LOG"
    fi
} >> "$SCRIPT_LOG" 2>&1
