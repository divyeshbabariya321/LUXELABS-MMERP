#!/bin/bash
start_time=$(date "+%Y-%m-%d %H:%M:%S")
echo "-------------------------------------------"
echo "Start time: $start_time"
#Directories to scan and clean up
directories=(
    "/home/avoir-chic-qa-1-1/releases/"
    "/home/brands-qa-1-1/releases/"
    "/home/solo-prod-qa-1-1/releases/"
    "/home/suvandnat-qa-1-1/releases/"
    "/home/veralusso-qa-1-1/releases/"
)


# Healthcheck start ping
curl -m 10 --retry 5 https://health.theluxuryunlimited.com/ping/865e11ce-0ecf-44d3-86bc-beb3f3e6f36d/start


# Initialize an error variable
err=0

# Initialize an output variable to capture script output
output=""

# Function to handle errors
handle_error() {
    echo "Error: $1"
    err=1
    output+="Error: $1"$'\n'  # Append the error message to the output variable
}

for dir in "${directories[@]}"; do
    # Change to the build directory
    cd "$dir" || { echo "Error: Unable to change to the directory: $dir"; handle_error "Unable to change to the directory: $dir"; continue; }

    # List all directories in the build directory and sort them numerically
    builds=($(ls -d */ | sed 's/\///' | sort -n))

    # Calculate the number of builds to keep
    builds_to_keep=3

    # Determine the number of builds
    num_builds="${#builds[@]}"

    # Check if there are more than 3 builds
    if [ "$num_builds" -le "$builds_to_keep" ]; then
        echo "Nothing to remove in directory $dir. There are $num_builds builds."
        output+="Nothing to remove in directory $dir. There are $num_builds builds."$'\n'
        continue
    fi

    # Calculate the number of builds to remove
    builds_to_remove=$((num_builds - builds_to_keep))

    # Loop through and remove excess builds
    for ((i = 0; i < builds_to_remove; i++)); do
        build_to_remove="${builds[$i]}"
        echo "Removing build in directory $dir: $build_to_remove"
        output+="Removing build in directory $dir: $build_to_remove"$'\n'
        # Check for errors during removal
        if ! rm -rf "$build_to_remove"; then
            handle_error "Error removing build in directory $dir: $build_to_remove"
        fi
    done

    echo "Cleanup in directory $dir completed successfully."
    output+="Cleanup in directory $dir completed successfully."$'\n'
done

# Check the value of the err variable and echo the output if err is not equal to zero
if [ "$err" -ne 0 ]; then
    curl -m 10 --retry 5 --data-raw "$output" https://health.theluxuryunlimited.com/ping/865e11ce-0ecf-44d3-86bc-beb3f3e6f36d/fail # Healthcheck fail ping
else
    curl -m 10 --retry 5 --data-raw "$output" https://health.theluxuryunlimited.com/ping/865e11ce-0ecf-44d3-86bc-beb3f3e6f36d    # Healthcheck success ping
fi
end_time=$(date "+%Y-%m-%d %H:%M:%S")
echo "End time: $end_time"
echo "-------------------------------------------"
exit 0
#------------------------------------------------------------------------------------------

