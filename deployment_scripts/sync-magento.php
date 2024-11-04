<?php

if (count($argv) < 2) {
    exit("Usage: php script.php <website_store_id>\n");
}

$store_website_id = $argv[1];

// Remote Database Connection
$remoteHost            = $argv[2];
$remoteUsername        = 'tempuser';
$remotePassword        = 'GouTysKhsFhsfrF784';
$remoteDatabase        = $argv[3];
$website_store_id      = '0';
$website_store_view_id = '0';

$remoteDb = new PDO("mysql:host=$remoteHost;dbname=$remoteDatabase;charset=utf8mb4", $remoteUsername, $remotePassword);

// Local Database Connection
$localHost     = '81.0.247.216';
$localUsername = 'erplive';
$localPassword = 'UhgjT68FgrhjOOo';
$localDatabase = 'erp_live';

$localDb = new PDO("mysql:host=$localHost;dbname=$localDatabase;charset=utf8mb4", $localUsername, $localPassword);

try {
    // Select records from the remote MySQL database
    $query = 'SELECT config_id, scope, scope_id, path, value FROM core_config_data';
    $stmt  = $remoteDb->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Define the INSERT query for the local database
        $insertQuery = 'INSERT INTO magento_settings (website_store_id, website_store_view_id, name, config_id, scope, scope_id, path, value, store_website_id) VALUES (:website_store_id, :website_store_view_id, :name, :config_id, :scope, :scope_id, :path, :value, :store_website_id)';

        // Prepare the INSERT statement
        $insertStmt = $localDb->prepare($insertQuery);

        // Bind values from the remote database to the local INSERT statement
        $insertStmt->bindParam(':website_store_id', $row['scope_id']);
        $insertStmt->bindParam(':website_store_view_id', $row['scope_id']);
        $insertStmt->bindParam(':name', $row['path']);
        $insertStmt->bindParam(':config_id', $row['config_id']);
        $insertStmt->bindParam(':scope', $row['scope']);
        $insertStmt->bindParam(':scope_id', $row['scope_id']);
        $insertStmt->bindParam(':path', $row['path']);
        $insertStmt->bindParam(':value', $row['value']);
        $insertStmt->bindParam(':store_website_id', $store_website_id);
        echo $row['scope'] . "\n";
        echo $row['value'] . "\n";
        // Execute the INSERT statement
        $insertStmt->execute();
    }

    echo 'Data inserted successfully!';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// Close the database connections
$remoteDb = null;
$localDb  = null;
?>

