<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$env      = parse_ini_file('/opt/etc/mysql-creds.conf');
$dbuser   = $env['EMAIL_MYSQL_USER'];
$dbpass   = $env['EMAIL_MYSQL_PASS'];
$dbname   = $env['EMAIL_MYSQL_DB'];
$dbhost   = $env['EMAIL_MYSQL_HOST'];
$testmail = 'info@mio-moda.com';
$hostname = '{mail.mio-moda.com:993/imap/ssl}INBOX';
$username = $argv[1];
$password = $argv[2];
echo "$argv[1]";
$inbox = imap_open($hostname, $username, $password) or exit('Cannot connect to mio-moda: ' . imap_last_error());
//$emails = imap_search($inbox,'ALL');
$emails = imap_search($inbox, 'UNSEEN');
//$x=count($MB);
if ($emails) {
    rsort($emails);
    /* for every email... */
    foreach ($emails as $email_number) {
        //$email_number=$emails[0];
        //print_r($emails);
        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $message  = imap_fetchbody($inbox, $email_number, 1);

        $subject = $overview[0]->subject;
        $from    = $overview[0]->from;
        $to      = $overview[0]->to;
        $date    = $overview[0]->date;
        $size    = $overview[0]->size;
        $date    = date('Y-m-d H:i:s', strtotime($date));
        echo "mail body = $message \n";
        if ($conn = @mysqli_connect($dbhost, $dbuser, $dbpass)) {
            if (! @mysqli_select_db($conn, $dbname)) {
                mail($email, 'Email Logger Error', "There was an error selecting the email logger database.\n\n" . mysql_error());
            }
            $from    = mysqli_real_escape_string($conn, $from);
            $to      = mysqli_real_escape_string($conn, $to);
            $subject = mysqli_real_escape_string($conn, $subject);
            //  $headers = mysqli_real_escape_string($conn, $headers);
            $message = mysqli_real_escape_string($conn, $message);
            $query   = "INSERT INTO emails (`created_at`,`updated_at`,`email_category_id`,`template`,`type`,`to`,`from`,`subject`,`message`) VALUES('$date',now(),'0','customer-simple','incoming','$to','$from','$subject','$message')";
            mysqli_query($conn, $query);
            $error = mysqli_error($conn);
            echo 'Error Occurred: ' . $error;

            $mailfl = 0;
            if ($to = $testmail) {
                // CUSTOMERS
                $sql = "select id,phone from customers where email = '$from'";
                echo "$sql \n";
                $result = mysqli_query($conn, $sql);
                if (empty($results)) {
                    echo "No results found \n";
                    $rowcount = 0;
                } else {
                    $rowcount = mysqli_num_rows($result);
                }

                if ($rowcount >= 1) {
                    echo "customer email $from \n";
                    $row   = $result->fetch_assoc();
                    $cid   = $row['id'];
                    $phone = $row['phone'];
                    $query = "INSERT INTO chat_messages (`flow_exit`,`time_doctor_activity_user_id`,`time_doctor_activity_summary_id`,`document_id`,`is_queue`,`number`,`message`,`media_url`,`approved`,`status`,`contact_id`,`erp_user`,`supplier_id`,`task_id`,`dubbizle_id`,`vendor_id`,`customer_id`,`is_email`,`from_email`,`to_email`,`email_id`) VALUES('0',0,0,'0','0','$phone','$message',NULL,'0','0',NULL,NULL,NULL,NULL,NULL,NULL,'$cid','1','$from','$to','1')";
                    mysqli_query($conn, $query);
                    $error = mysqli_error($conn);
                    echo 'Error Occurred: ' . $error;
                    $mailfl = 1;
                }

                // Vendors
                $sql    = "select id,phone from vendors where email = $from";
                $result = mysqli_query($conn, $sql);
                echo "$sql \n";
                if (empty($results)) {
                    echo "No results found \n";
                    $rowcount = 0;
                } else {
                    $rowcount = mysqli_num_rows($result);
                }
                if ($rowcount >= 1) {
                    echo "vendor customer email $from \n";
                    $row   = $result->fetch_assoc();
                    $cid   = $row['id'];
                    $phone = $row['phone'];
                    $query = "INSERT INTO chat_messages (`flow_exit`,`time_doctor_activity_user_id`,`time_doctor_activity_summary_id`,`document_id`,`is_queue`,`number`,`message`,`media_url`,`approved`,`status`,`contact_id`,`erp_user`,`supplier_id`,`task_id`,`dubbizle_id`,`vendor_id`,`customer_id`,`is_email`,`from_email`,`to_email`,`email_id`) VALUES('0',0,0,'0','0','$phone','$message',NULL,'0','0',NULL,NULL,NULL,NULL,NULL,'$vid',NULL,'1','$from','$to','1')";
                    mysqli_query($conn, $query);
                    $error = mysqli_error($conn);
                    echo 'Error Occurred: ' . $error;
                    $mailfl = 1;
                }

                // Suppliers
                $sql    = "select id,phone from suppliers where email = $from";
                $result = mysqli_query($conn, $sql);

                echo "$sql \n";
                if (empty($results)) {
                    echo "No results found \n";
                    $rowcount = 0;
                } else {
                    $rowcount = mysqli_num_rows($result);
                }
                if ($rowcount >= 1) {
                    echo "supp customer email $from \n";
                    $row   = $result->fetch_assoc();
                    $cid   = $row['id'];
                    $phone = $row['phone'];
                    $query = "INSERT INTO chat_messages (`flow_exit`,`time_doctor_activity_user_id`,`time_doctor_activity_summary_id`,`document_id`,`is_queue`,`number`,`message`,`media_url`,`approved`,`status`,`contact_id`,`erp_user`,`supplier_id`,`task_id`,`dubbizle_id`,`vendor_id`,`customer_id`,`is_email`,`from_email`,`to_email`,`email_id`) VALUES('0',0,0,'0','0','$phone','$message',NULL,'0','0',NULL,NULL,'$sid',NULL,NULL,NULL,NULL,'1','$from','$to','1')";
                    mysqli_query($conn, $query);
                    $error = mysqli_error($conn);
                    echo 'Error Occurred: ' . $error;
                    $mailfl = 1;
                }

                //Other Emails
                if ($mailfl == 0) {
                    echo "Other email $from \n";
                    $query = "INSERT INTO chat_messages (`flow_exit`,`time_doctor_activity_user_id`,`time_doctor_activity_summary_id`,`document_id`,`is_queue`,`number`,`message`,`media_url`,`approved`,`status`,`contact_id`,`erp_user`,`supplier_id`,`task_id`,`dubbizle_id`,`vendor_id`,`customer_id`,`is_email`,`from_email`,`to_email`,`email_id`) VALUES('0',0,0,'0','0',NULL,'$message',NULL,'0','0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','$from','$to','1')";
                    echo "$query";
                    mysqli_query($conn, $query);
                    $error = mysqli_error($conn);
                    echo 'Error Occurred: ' . $error;
                }
            }
        } else {
            //mail($notify,'Email Logger Error',"There was an error connecting the email logger database.\n\n".mysql_error());
            echo 'mysqli_error()';
        }
    }
}
