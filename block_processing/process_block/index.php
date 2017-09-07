<?php

// Include REDIS
$redis = include_once('../../RedisInit.php');

$nonces_to_check = json_decode($redis->get('nonces_to_check'), TRUE);
print_r($nonces_to_check);
$iteration = 0;

while(1) {
  $iteration++;

  $result = getBlock();
  print_r($result);
  $last_block = json_decode($result, TRUE);
  $last_block_number = hexdec($last_block['result']['number']);
  print $last_block_number;
  print 'iteration number: ' . $iteration;

  if (is_array($nonces_to_check)) {
    foreach ($nonces_to_check as $nonce) {
      print_r($nonces_to_check);
      print 'nonce to check: ' . $nonce;
      // Set starting position
      $last_block_number = $previous_block_number;
      do {
        $previous_block_number = $previous_block_number - 1;
        $previous_block_hash = '0x' . dechex($previous_block_number);
        print 'previous number: ' . $previous_block_number;
        print 'previous hash: ' . $previous_block_hash;
        $block = getBlock($previous_block_hash);
        print_r($block);
        print 'checking nonce: ' . $block['result']['nonce'];
      } while($nonce == $block['result']['nonce']);

      print 'block we looking for: ';
      print_r($block);
    }
  }

  // Wait 18 seconds
  usleep(18000000);
}

/**
 * Get data about the block we need
 * @param string $block_number "latest" or block number in HEX
 */
function getBlock($block_number = "latest") {

  $data = [
    "jsonrpc" => "2.0",
    "method" => "eth_getBlockByNumber",
    "params" => [$block_number, TRUE],
    "id" => "1"
  ];

  // Get the latest block info first
  $data_string = json_encode($data);
  $block_info = curl_init('http://127.0.0.1:8983');
  curl_setopt($block_info, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($block_info, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($block_info, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($block_info, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($data_string)
    ]
  );

  return curl_exec($block_info);
}