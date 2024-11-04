<?php

$user_id   = $argv[1];
$password1 = $argv[2];

$servername = '81.0.247.216';
$username   = 'erplive';
$password   = 'C*jlP2E0nbj6';
$dbname     = 'erp_live';

$options = [
    'cost' => 8,
];
$GPASS  = password_hash($password1, PASSWORD_BCRYPT);
$output = shell_exec("./gen-pass.sh $password");
echo $output;
//$str = preg_replace('/\:/', '', $output);
//echo $str;
echo $GPASS;
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (! $conn) {
    exit('Connection failed: ' . mysqli_connect_error());
}

$sql = "UPDATE users SET password = '$GPASS' WHERE email = '$user_id'";

if (mysqli_query($conn, $sql)) {
    echo "Password for user with ID $user_id set successfully.\n";
} else {
    echo 'Error: ' . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
