<?php

// Include REDIS
$redis = include_once('../../RedisInit.php');

$data = [
  "jsonrpc" => "2.0",
  "method" => "eth_getBlockByNumber",
  "params" => ["latest", true],
  "id" => "1"
];

$data_string = json_encode($data);
$block_info = curl_init('http://127.0.0.1:8983');
curl_setopt($block_info, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($block_info, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($block_info, CURLOPT_RETURNTRANSFER, true);
curl_setopt($block_info, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string)
  ]
);

$result = curl_exec($block_info);

print_r($result);