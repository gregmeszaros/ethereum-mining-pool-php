<?php
error_reporting(error_reporting() & ~E_NOTICE);

// $m = new Memcached();
// $m->addServer('localhost', 11211);

// @TODO maybe have a config option if to use REDIS vs Memcache?
$redis = include_once('../../RedisInit.php');

$getBlockInfoKey = 'blockinfo';
$getWorkCacheKey = 'eth_getWork_response';

$data = array("jsonrpc" => "2.0", "method" => "eth_getBlockByNumber", "params" => ["latest", true], "id" => "1");
$data_string = json_encode($data);
$ch1 = curl_init('http://127.0.0.1:8983');
curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch1, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
  'Content-Length: ' . strlen($data_string))
);
$data = array("jsonrpc" => "2.0", "method" => "eth_getWork", "params" => [], "id" => 73);
$data_string = json_encode($data);
$ch = curl_init('http://127.0.0.1:8983');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
  'Content-Length: ' . strlen($data_string))
);

$iteration;

while(1) {
  $iteration++;
	echo "\n-------------------------------------------------------------------";
	echo "\n".$iteration;
	echo "\n-------------------------------------------------------------------";
	echo "\nGetting Block Data From RPC...";
	$result1 = curl_exec($ch1);
	if ($result1) {
    echo "\n blockinfo - ok!";
  }
	$result = curl_exec($ch);
	if ($result) {
    echo "\n getWork_response - ok!";
  }

  $redis->set($getWorkCacheKey, $result);
  $redis->set($getBlockInfoKey, $result1);

  echo "\nCached in Memory!";

  /**
  $m->set($getWorkCacheKey,$result);

	$items = array(
    $getBlockInfoKey => $result1,
    $getWorkCacheKey => $result
  );
  $m->setMulti($items);
   */

	usleep(25000);
}

?>