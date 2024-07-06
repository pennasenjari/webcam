#!/bin/bash

# Path to this script
SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")

# Output directory and PID file
LIVE_DIR="/var/www/webcam/live"
MOTION_DIR="/var/www/webcam/motion"

# Stop ffmpeg processes
pkill ffmpeg

# Remove the .m3u8 file and .ts segment files
sleep 2 # Added because there was a problem deleting .m3u8 file
rm -f $LIVE_DIR/*.pid
rm -f $LIVE_DIR/*.m3u8
rm -f $LIVE_DIR/*.ts

pkill motion
sleep 2
rm -f $MOTION_DIR/*.mkv
