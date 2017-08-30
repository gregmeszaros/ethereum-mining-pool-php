<?php

// Include BigInteger
include_once('../BigInteger.php');

// Connect mysql
$config = include('../config.php');

// Include REDIS
$redis = include_once('../RedisInit.php');

$minerdata = $_GET["miner"] ?? FALSE;
$host = $_SERVER["REMOTE_ADDR"];

// Share stats reset
$shareCounter = 5000;

// Pool Diff
$miner_diff = 15000000;

// Log data (true / false)
$log = true;

if($log) {
  $filename = 'miner-log.txt';
  $log_path = 'logs/' . $filename;

  if(!file_exists($log_path)) {
    $fh = fopen($log_path, 'w');
    fclose($fh);
  }

  // Get if anything already in the file
  $current = file_get_contents($log_path);

  $current .= "\n======================= INIT LOG FILE =============================";
  file_put_contents($log_path, $current);
}

// Get data from miner
if ($minerdata) {
  $minderdata_array = explode('@', $minerdata);
  $hash_rate = $minderdata_array[0];
  $payout_addr = $minderdata_array[1];
  $rig_name = $minderdata_array[2];
}

// Get data from input
$jsonquery = file_get_contents('php://input');
$json = json_decode($jsonquery, TRUE);

// Get Method
$method = $json['method'] ?? '';

if($log) {
  $current .= "\n Method used: $method";
  file_put_contents($log_path, $current);
}

/**
  MINER METHODS
  eth_getWork
  eth_submitWork
  eth_submitHashrate
  eth_awaitNewWork
  eth_progress
*/
switch ($method) {
  case 'eth_submitHashrate':
    $output = array("id" => 73, "jsonrpc" => "2.0", "result" => TRUE);
    $output = json_encode($output);
    echo $output;
    break;
  case 1:
    echo "i equals 1";
    break;
  case 2:
    echo "i equals 2";
    break;
}

?>