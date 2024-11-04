#!/bin/bash

# Function to print a JSON message
print_json() {
  local status="$1"
  local message="$2"
  echo "{\"status\": \"$status\", \"message\": \"$message\"}"
}

# Check if all required parameters are provided
if [ "$#" -ne 4 ]; then
  print_json "error" "Usage: $0 <source_server_ip> <source_server_dir> <dest_server_ip> <dest_server_dir>"
  exit 1
fi

source_server_ip="$1"
source_server_dir="$2"
dest_server_ip="$3"
dest_server_dir="$4"

# Set the SSH port and key options
ssh_port=22  # Replace with your desired SSH port
ssh_key="/path/to/your/ssh/keyfile"  # Replace with the path to your SSH key

# Rsync command with SSH options
rsync -e "ssh -p $ssh_port -i $ssh_key" -avz "$source_server_ip:$source_server_dir" "$dest_server_ip:$dest_server_dir"

# Check the exit status of rsync
if [ $? -eq 0 ]; then
  print_json "success" "Rsync completed successfully"
else
  print_json "error" "Rsync failed"
fi

