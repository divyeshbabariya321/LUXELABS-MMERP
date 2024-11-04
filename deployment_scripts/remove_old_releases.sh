#!/bin/bash

# Directory where the folders are located
folder_dir="$1"

# Change to the directory
cd "$folder_dir" || exit 1

# Get a list of folder names and sort them numerically
folders=($(ls -1 | sort -n))

# Calculate the number of folders to keep
folders_to_keep=3

# Calculate the number of folders to remove
folders_to_remove=$(( ${#folders[@]} - folders_to_keep ))

if [ $folders_to_remove -gt 0 ]; then
  # Loop through and remove the older folders
  for ((i = 0; i < folders_to_remove; i++)); do
    folder_to_remove="${folders[i]}"
    echo "Removing $folder_to_remove"
    rm -r "$folder_to_remove"
  done
else
  echo "No folders to remove, only $folders_to_keep or fewer folders found."
fi
