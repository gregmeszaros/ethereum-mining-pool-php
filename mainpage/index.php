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

// Pool Diff ( 400 M as default)
$pool_diff = 400000000;

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
    $data = [
      "jsonrpc" => "2.0",
      "method" => "eth_submitHashrate",
      "params" => $json['params'],
      "id" => 73,
    ];
    $data = json_encode($data);

    $ch_hashrate = curl_init('http://127.0.0.1:8983');
    curl_setopt($ch_hashrate, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_hashrate, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch_hashrate, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_hashrate, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
      ]
    );

    // eth_submitHashrate
    $output = curl_exec($ch_hashrate);
    echo $output;

    if($log) {
      $current .= "\n eth_submitHashrate: " . print_r($output, TRUE);
      file_put_contents($log_path, $current);
    }

    break;
  case 'eth_getWork':
    $data = [
      "jsonrpc" => "2.0",
      "method" => "eth_getWork",
      "params" => [], "id" => 73
    ];
    $data = json_encode($data);

    $ch_get_work = curl_init('http://127.0.0.1:8983');
    curl_setopt($ch_get_work, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_get_work, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch_get_work, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_get_work, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
      ]
    );

    // eth_getWork
    $output = curl_exec($ch_get_work);

    $output = json_decode($output, TRUE);
    $redis->set('getWorkPow', $output['result'][0]);
    $new_diff = getTargetDiff($pool_diff);
    $fix = '';

    $target_diff = bcdechex($new_diff);
    $currentLenght = strlen($target_diff);
    $desiredLenght = 64;
    if ($currentLenght < $desiredLenght) {
      $toadd = $desiredLenght - $currentLenght;
      for ($i=0; $i < $toadd; $i++) {
        $fix .= '0';
      }
      $target_diff = '0x' . $fix . $target_diff;
    }

    $output['result'][2] = $target_diff;

    // Overwrite rpc method
    $overwrite_output = array("id" => 1, "jsonrpc" => "2.0", "result" => [$output['result'][0], $output['result'][1], $target_diff]);

    echo json_encode($overwrite_output);

    if($log) {
      $current .= "\n eth_getWork: " . print_r($output, TRUE);
      $current .= "\n eth_getWork - overwrite " . print_r($overwrite_output, TRUE);
      $current .= "\n eth_getWork - target diff " . print_r($target_diff, TRUE);
      file_put_contents($log_path, $current);
    }

    break;
  case 'eth_submitWork':
    $data = [
      "jsonrpc" => "2.0",
      "method" => "eth_submitWork",
      "params" => $json['params'],
      "id" => 1
    ];
    $data = json_encode($data);

    $current .= "\n eth_submitWork params: " . print_r($data, TRUE);

    $ch_submit_work = curl_init('http://127.0.0.1:8983');
    curl_setopt($ch_submit_work, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch_submit_work, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch_submit_work, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_submit_work, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
      ]
    );

    // eth_submitWork
    $output = curl_exec($ch_submit_work);
    echo $output;

    if($log) {
      $current .= "\n eth_submitWork: " . print_r($output, TRUE);
      file_put_contents($log_path, $current);
    }
    break;
}

/**
 * Returns difficulty for the miner
 * @param int $pool_diff
 * @param int $miner_hashrate
 * @return String
 */
function getTargetDiff($pool_diff = 400000000, $miner_hashrate = 1) {
  $a256 = new Math_BigInteger('115792089237316195423570985008687907853269984665640564039457584007913129639936');  //2^256
  $pool_diff = new Math_BigInteger($pool_diff * $miner_hashrate);

  list($quotient, $remainder) = $a256->divide($pool_diff);
  $target_diff = new Math_BigInteger($quotient->toString());

  return $target_diff;
}

function bcdechex($dec) {
  $hex = '';
  do {
    $last = bcmod($dec, 16);
    $hex = dechex($last) . $hex;
    $dec = bcdiv(bcsub($dec, $last), 16);
  } while($dec > 0);
  return $hex;
}

?>