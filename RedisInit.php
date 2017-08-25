<?php
  // @TODO all the hosts, ports etc can come from config file?
  // Init REDIS
  $redis = new Redis();
  return $redis->connect('127.0.0.1', 6379);
?>