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
  $current .= "\n PHP input: " . print_r($json, TRUE);
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
    $output = [
      "id" => 73,
      "jsonrpc" => "2.0",
      "result" => TRUE
    ];
    $output = json_encode($output);
    echo $output;
    break;
  case 'eth_getWork':
    $getBlockInfo = 'blockinfo';
    $getWorkKey = 'eth_getWork_response';

    $data = [
      "jsonrpc" => "2.0",
      "method" => "eth_getBlockByNumber",
      "params" => ["latest", TRUE],
      "id" => "1"
    ];
    $data = json_encode($data);

    $ch_block = curl_init('http://127.0.0.1:8983');
    curl_setopt($ch_block, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_block, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch_block, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_block, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data))
    );

    // Block info
    $output = curl_exec($ch_block);

    if($log) {
      $current .= "\n eth_getBlockByNumber: " . print_r($output, TRUE);
      file_put_contents($log_path, $current);
    }

    $data = [
      "jsonrpc" => "2.0",
      "method" => "eth_getWork",
      "params" => [], "id" => 73
    ];
    $data = json_encode($data);

    $ch_get_work = curl_init('http://127.0.0.1:8983');
    curl_setopt($ch_get_work, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_get_work, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch_get_work, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_get_work, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data))
    );

    // eth_getWork
    $output = curl_exec($ch_get_work);

    if($log) {
      $current .= "\n eth_getWork: " . print_r($output, TRUE);
      file_put_contents($log_path, $current);
    }

    break;
  case 2:
    echo "i equals 2";
    break;
}

?>