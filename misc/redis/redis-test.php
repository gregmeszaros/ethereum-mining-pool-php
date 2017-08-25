<?php
  // Connecting to Redis server on localhost
  $redis = include_once('../../RedisInit.php');

  echo "Connection to server succeeded";

  // Check whether server is running or not
  echo "Server is running: " . $redis->ping();
?>