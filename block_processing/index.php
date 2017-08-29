<?php
  error_reporting(error_reporting() & ~E_NOTICE);
  include_once('../BigInteger.php');
  $config = include_once('../config.php');
  $redis = include_once('../RedisInit.php');

  $mysqli = mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
  $df = 0;

  // @ replace to redis call
  // $m = new Memcached();
  // $m->addServer('localhost', 11211);

while(1) {
  $state_work = $redis->get('state_work');
  if (!$state_work) {
    echo "\nstate_work null";
    $current .= "\nstate_work null";
    $state_work = 0;
  }
  if ($state_work == 0) {
  $df++;
  $existQuery = "SELECT balance FROM info WHERE id=1";
  $existResult = mysqli_query($mysqli, $existQuery) or die("Database Error");
  $existRow = mysqli_fetch_array($existResult);
  $real_balance_prev = new Math_BigInteger($existRow[0]);  // CONVERT HEX LAST DIFF TO DECIMAL

	////////////////////////////////////////LOGS////////////////////////////////////
	$logstate = true;
	$file = 'block-processing';
	$file = $file . '-' . date('Y M d') . '.txt';
  if(!file_exists($file)) {
    $fh = fopen($file, 'w');
    fclose($fh);
  }
	if ($logstate) {
		$current = file_get_contents($file);
  }
  $current .= "\n\n\n\n--------------NEW BLOCK SOLVED--------------\n";
  echo "\n\n\n\n--------------NEW BLOCK SOLVED--------------\n";
	///////////////////////////////////////////////////////////////////////////////

  $data = array("jsonrpc" => "2.0", "method" => "eth_accounts", "params" => [], "id" => 64);
  $data_string = json_encode($data);

  $ch1 = curl_init('http://127.0.0.1:8983');
  curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch1, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
  );

  $result1 = curl_exec($ch1);
  $block_info_last = json_decode($result1, true);
  $addrr = $block_info_last['result'];
  $coinbase = $addrr[0];
  // Check for coinbase account
  if (!$coinbase) {
    echo "\nCan't get coinbase - restarting eth";
    $current .= "\nCant get coinbase - restarting eth";
    //$restart_eth = exec('screen eth -b -j --json-rpc-port 8983 > /dev/null &');
  }
  else {
    ///////////////
    $ch1 = curl_init('http://api.etherscan.io/api?module=account&action=balance&address='.$addrr[0].'&tag=latest');
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    $result3 = curl_exec($ch1);  // < GET Balance from independent node since locally forks may happen and return invalid (higher) response

    if (!$result3) {
      $data = array("jsonrpc" => "2.0", "method" => "eth_getBalance", "params" => [$addrr[0],'latest'], "id" => 1);
      $data_string = json_encode($data);
      $ch1 = curl_init('http://127.0.0.1:8983');
      curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch1, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
      );
      $result3 = curl_exec($ch1);
      $block_info_last = json_decode($result3, true);
      $escapeDot = explode('.', sprintf('%f', hexdec($block_info_last['result'])));
      $balanceaddr = new Math_BigInteger($escapeDot[0]);
    }
    else {
      $block_info_last = json_decode($result3, true);
      $balanceaddr = new Math_BigInteger($block_info_last['result']);
    }

    $block_coins_size = new Math_BigInteger('0');
    $balanceToSave = new Math_BigInteger('0');

    if (!$real_balance_prev == 0) {
      $block_coins_size = $balanceaddr->subtract($real_balance_prev);
    }
    else {
      $block_coins_size = $balanceaddr;
    }
    if ($block_coins_size < 0) {
      $block_coins_size = new Math_BigInteger('0');
    }
    if (strpos($block_coins_size->toString(),'-') !== false) {
      $block_coins_size = new Math_BigInteger('0');
    }
    if (!$block_coins_size) {
      die('Invalid block coins size');
    }
    if ($block_coins_size <= 0) {
      die('Invalid block coins size = 0');
    }

    $uncle = new Math_BigInteger('0');
    $maxUncle = new Math_BigInteger('4800000000000000000');
    $standart_block_size = new Math_BigInteger('5000010000000000000');
    $uncle = $standart_block_size->add($maxUncle);
    echo "\nbalance:".$balanceaddr->toString();
    $current .= "\nbalance:".$balanceaddr->toString();
    echo "\nb_size_init:".$block_coins_size->toString();
    $current .= "\nb_size_init:".$block_coins_size->toString();

    if ($block_coins_size > $standart_block_size) {
      if ($block_coins_size < $uncle) {
        $balanceToSave = $balanceaddr->subtract($block_coins_size);
        echo "\nBacklog(uncle) detected using standart -> 5000000000000000000";
        $current .= "\nBacklog(uncle) detected using standart -> 5000000000000000000";
      }
      else {
        $block_coins_size = new Math_BigInteger('5000010000000000000');
        $balanceToSave = $balanceaddr->subtract($block_coins_size);
        echo "\nBacklog detected using standart -> 5000010000000000000";
        $current .= "\nBacklog detected using standart -> 5000010000000000000";
      }
    }
    else {
      $balanceToSave = $balanceaddr;
      echo "\nBlocksize normal PLAIN";
      $current .= "\nBlocksize normal PLAIN";
    }
    $too_bigg = new Math_BigInteger('12000000000000000000');
    if ($block_coins_size > $too_bigg) {
      $block_coins_size = $standart_block_size;
      echo "\nBlocksize normal _FIXXX";
      $current .= "\nBlocksize normal _FIXXX";
    }
    $Lenght = strlen($block_coins_size->toString());
    echo "\nLenght1:".$Lenght;
    $current .= "\nLenght1:".$Lenght;
    $big_ta = strlen($too_bigg->toString());
    echo "\nLenght2:".$big_ta;
    $current .= "\nLenght2:".$big_ta;
    $Lenght_123 = $block_coins_size->toString();
    echo "\nLenght1:".$Lenght_123;
    $current .= "\nLenght1:".$Lenght_123;
    $big_ta_123 = $too_bigg->toString();
    echo "\nLenght2:".$big_ta_123;
    $current .= "\nLenght2:".$big_ta_123;

    $current .= "\nLenght:".$Lenght;
    if ($Lenght > 20 && strpos($Lenght_123,'-') === false) {
      $block_coins_size = $standart_block_size;
      echo "\nBlocksize normal _FIXXX _LENGHT " . $block_coins_size->toString();
      $current .= "\nBlocksize normal _FIXXX _LENGHT " . $block_coins_size->toString();
    }
    $nullNumber = new Math_BigInteger('0');
    if ($block_coins_size < $nullNumber) {
      $block_coins_size = $nullNumber;
    }
    $ether_wei = 1000000000000000000;

    $pool_fee_revenue = '0x0';  //Pool fee revenue
    $poolFee = 100-1;  // fee -> 1%
    $current .= "\nBlock size:" . $block_coins_size->toString() . '';
    $current .= "\nPool fee:" . $poolFee . '';

    echo "\nBlock size:" . $block_coins_size->toString() . '';
    echo "\nPool fee:" . $poolFee . '% for miner';

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

    $result1 = curl_exec($ch1);
    $block_info_last = json_decode($result1, true);
    $block_info_last = $block_info_last['result'];
    $block_decimal = hexdec($block_info_last['number']);
    echo "\nCurrent block:".$block_decimal.'';
    $current .= "\nCurrent block:".$block_decimal.'';

    echo "\nCoinbase:".$coinbase.'';
    $current .= "\nCoinbase:".$coinbase.'';

    echo "\nBalance: ".$balanceaddr->toString().' wei';
    $current .= "\nBalance: ".$balanceaddr->toString().' wei';

    $existQuery = "SELECT DISTINCT blockid FROM shares";
    $existResultMinersss = mysqli_query($mysqli, $existQuery) or die("Database Error");
    while ($row=mysqli_fetch_row($existResultMinersss)) {
        $blockdecimalrow = $row[0];
        $block_hex = '0x'.dechex($row[0]);
        $data = array("jsonrpc" => "2.0", "method" => "eth_getBlockByNumber", "params" => [$block_hex, true], "id" => "1");
        $data_string = json_encode($data);
        $ch1 = curl_init('http://127.0.0.1:8983');
        curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_string))
        );
        $result1 = curl_exec($ch1);
        $block_info_last = json_decode($result1, true);
        $block_info_last = $block_info_last['result'];
        $minedBy = $block_info_last['miner'];
        if ($minedBy == $coinbase) {
          $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
          $found = 1;
          $tas22k = 'INSERT INTO blocks (blockid) VALUES ("' . $blockdecimalrow . '")';
          $query = mysqli_query($mysqli,$tas22k) or die("Database Error");

          echo "\nBlock: ".$blockdecimalrow." has been mined!\n";
          echo "\n".$block_coins_size->toString().' wei to split';
          $current .= "\nBlock: ".$blockdecimalrow." has been mined!\n";
          $current .= "\n".$block_coins_size->toString().' wei to split';
          $current .= "\nBlock MINEDBY: " . $minedBy . "";
          echo "\nBlock MINED BY: " . $minedBy . "";

          $dataitems = array();
          $existQuery = "SELECT blockid, blockPowHash, Digest, nonceFound, minerdiff FROM shares";
          $existResultMinersss = mysqli_query($mysqli,$existQuery) or die("Database Error");
          $gownos = 0;
          while ($row=mysqli_fetch_row($existResultMinersss)){
            $itemTest = array("block" => $row[0], "pow" => str_replace('0x', '', $row[1]), "digest" => str_replace('0x', '', $row[2]), "nonce" => str_replace('0x', '', $row[3]), "diff" => $row[4]);
            array_push($dataitems, $itemTest);
            $gownos++;
          }
          $data = array("array" => $dataitems);
          $data_string = base64_encode(json_encode($data));
          $filtempe = 'temp.txt';
          if(!file_exists($filtempe)) {
            $fh = fopen($filtempe, 'w');
            fclose($fh);
          }
          file_put_contents($filtempe, $data_string);
          $output=0;
          $output = shell_exec('/usr/bin/python /root/pyethereum/ethereum/nonce_fast.py ' . $filtempe .' ' . rand(5,123142312));
          $test = explode(' ', $output);

          $countAll=0;
          $countInvalid=0;
          for ($i=0; $i < count($test)-1; $i++) {
            if (strpos($test[$i],'Nonce') !== false) {
              $countAll++;
              if (strpos($test[$i],'False') !== false) {
                $countInvalid++;
                $task = "INSERT INTO shares_invalid SELECT * FROM shares WHERE nonceFound = '$nonceplain'";
                $query = mysqli_query($mysqli, $task) or die("Database Error");
                $task = "DELETE FROM shares WHERE nonceFound = '$nonceplain'";
                $query = mysqli_query($mysqli, $task) or die("Database Error");
              }
            }
          }
          echo "\n\n" . $countAll . '/' . $countInvalid . '/' . $gownos;
          $current .= "\n\n" . $countAll . '/' . $countInvalid . '/' . $gownos;

          $existQuery = "SELECT address, minerdiff, blockdiff, time FROM shares WHERE blockid <= $blockdecimalrow";
          $existResultMinersss = mysqli_query($mysqli,$existQuery) or die("Database Error");
          $count_response  = mysqli_num_rows($existResultMinersss);
          echo "\nShares:".$count_response;
          $current .= "\nShares:" . $count_response;

          $timestamps = array();
          $totalMinersDiff = new Math_BigInteger('0');
          $miner_payouts = array();
          while ($row=mysqli_fetch_row($existResultMinersss)){
            $miner_adr = $row[0];
            $miner_adr_balance = new Math_BigInteger($row[1]);
            $totalMinersDiff = $totalMinersDiff->add($miner_adr_balance);

            if (!isset($miner_payouts["'$miner_adr'"])) {
              $miner_payouts["'$miner_adr'"] = $miner_adr_balance;
            }
            else {
              $miner_adr_balance_fromArray = new Math_BigInteger($miner_payouts["'$miner_adr'"]);
              $setNewValue = $miner_adr_balance_fromArray->add($miner_adr_balance);
              $miner_payouts["'$miner_adr'"] = $setNewValue->toString();
            }
            array_push($timestamps, $row[3]);
          }
          $min_timestamp = min($timestamps);
          $max_timestamp = max($timestamps);
          $timestampDifference = ($max_timestamp - $min_timestamp)/60;
          echo "\nTimeRange:" . $timestampDifference . ' minutes';
          $current .= "\nTimeRange:" . $timestampDifference . ' minutes';

          if ($timestampDifference < 8) {
            $newTimeRange = $min_timestamp - 60*8;
            $existQuery = "SELECT address, minerdiff, blockdiff, time FROM shares_history WHERE time > $newTimeRange";
            $existResultMinersss = mysqli_query($mysqli,$existQuery)or die("Database Error");
            $count_response  = mysqli_num_rows($existResultMinersss);
            echo "\nShares_OLD_Taken:" . $count_response . '';
            $current .= "\nShares_OLD_Taken:" . $count_response . '';

            while ($row=mysqli_fetch_row($existResultMinersss)) {
              $miner_adr = $row[0];
              $miner_adr_balance = new Math_BigInteger($row[1]);
              $totalMinersDiff = $totalMinersDiff->add($miner_adr_balance);

              if (!isset($miner_payouts["'$miner_adr'"])) {
                $miner_payouts["'$miner_adr'"] = $miner_adr_balance;
                $old_new_added++;
              }
              else {
                $miner_adr_balance_fromArray = new Math_BigInteger($miner_payouts["'$miner_adr'"]);
                $setNewValue = $miner_adr_balance_fromArray->add($miner_adr_balance);
                $miner_payouts["'$miner_adr'"] = $setNewValue->toString();
                $old_old_old++;
              }
            }
            echo "\nShares_OLD_Taken__NEWADDED:" . $old_new_added.'';
            $current .= "\nShares_OLD_Taken__NEWADDED:" . $old_new_added.'';
            echo "\nShares_OLD_Taken__OLD_SUMMARY:" . $old_old_old . '';
            $current .= "\nShares_OLD_Taken__OLD_SUMMARY:" . $old_old_old . '';
          }

          echo "\n=============================================================================";
          echo "\nTotal Miners Diff:" . $totalMinersDiff->toString() . '  =  ' . $block_coins_size->toString() . ' wei';
          $current .= "\n=============================================================================";
          $current .= "\nTotal Miners Diff:" . $totalMinersDiff->toString() . '  =  '.$block_coins_size->toString() . ' wei';

          $totalsplit = new Math_BigInteger(0);
          $totalEther = new Math_BigInteger(0);
          $ether = new Math_BigInteger(0);
          $etherWithFeeTemp = new Math_BigInteger(0);
          $etherWithFee = new Math_BigInteger(0);
          $totalEtherWithFee = new Math_BigInteger(0);
          $subtractvalue = new Math_BigInteger(0);
          $poolRevenue = new Math_BigInteger(0);
          $tempdivider = new Math_BigInteger(100);

          foreach ($miner_payouts as $key => $value) {
            $valueadd = new Math_BigInteger($value);
            $totalsplit = $totalsplit->add($valueadd);
            $ether = $valueadd->multiply($block_coins_size);
            list($quotient23, $remainder) = $ether->divide($totalMinersDiff);
            $ether = $quotient23;
            $totalEther = $totalEther->add($ether);
            $fee_pool_pool = new Math_BigInteger($poolFee);
            $etherWithFeeTemp = $ether->multiply($fee_pool_pool);
            list($quotientFEE, $remainder) = $etherWithFeeTemp->divide($tempdivider);
            $etherWithFee = $quotientFEE;
            $totalEtherWithFee = $totalEtherWithFee->add($etherWithFee);
            $subtractvalue = $ether->subtract($etherWithFee);
            $poolRevenue = $poolRevenue->add($subtractvalue);
            $etherWithFeeToWEI = floatval($etherWithFee->toString()) / $ether_wei;

            echo "\n" . $key . ' => ' . $valueadd->toString() . ' => ' . $etherWithFee->toString() . ' wei =>' . sprintf('%f',$etherWithFeeToWEI);
            $current .= "\n" . $key . ' => ' . $valueadd->toString() . ' => ' . $etherWithFee->toString() . ' wei =>' . sprintf('%f',$etherWithFeeToWEI);
            $real_payput_addr = str_replace("'", '', $key);

            $existQuery = "SELECT balance FROM miners WHERE address='$real_payput_addr'";
            $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
            $existRow = mysqli_fetch_array($existResult);

            $real_balance = new Math_BigInteger($existRow[0]);
            $etherWithFee = $etherWithFee->add($real_balance);
            $formatedRevenueForMiner = $etherWithFee->toString();

            $task = "UPDATE miners SET balance='$formatedRevenueForMiner' WHERE address='$real_payput_addr';";
            $query = mysqli_query($mysqli, $task) or die("Database Error");
          }
          echo "\n".$totalsplit->toString().'/'.$totalMinersDiff->toString();
          $totalEtherWithFee = $totalEtherWithFee->add($poolRevenue);

          list($quotien32333, $remainder) = $poolRevenue->divide($ether_wei);
          $weiTotal = $quotien32333;

          echo "\n" . $block_coins_size->toString() . '/' . $totalEther->toString() . '/' . $totalEtherWithFee->toString();
          echo "\nPool fees revenue: " . $poolRevenue->toString() . ' wei => ' . $weiTotal->toString();

          $current .= "\n" . $totalsplit->toString() . '/' . $totalMinersDiff->toString();
          if ($totalsplit == $totalMinersDiff) {
            echo "\nDIFF Number Correct!";
            $current .= "\nDIFF Number Correct!";
          }
          else {
            echo "\n!!!!DIFF Number INCORRECT!!!!";
            $current .= "\n!!!!DIFF Number INCORRECT!!!!";
          }
          $current .= "\n" . $block_coins_size->toString() . '/' . $totalEther->toString() . '/' . $totalEtherWithFee->toString();
          $current .= "\nPool fees revenue: " . $poolRevenue->toString() . ' wei => ' . $weiTotal->toString();

          if ($block_coins_size >= $totalEther) {
            echo "\nWei Balance without fee Correct!";
            $current .= "\nWei Balance without fee Correct!";
          }
          else {
            echo "\n!!!!Wei Balance with fee INCORRECT!!!!";
            $current .= "\n!!!!Wei Balance with fee INCORRECT!!!!";
          }
          if ($block_coins_size >= $totalEtherWithFee) {
            echo "\nWei Balance with fee Correct!";
            $current .= "\nWei Balance with fee Correct!";
          } else {
            echo "\n!!!!Wei Balance with fee INCORRECT!!!!";
            $current .= "\n!!!!Wei Balance with fee INCORRECT!!!!";
          }

          $existQuery = "SELECT balance FROM miners WHERE address='$pool_fee_revenue'";
          $existResult = mysqli_query($mysqli, $existQuery) or die("Database Error");
          $existRow = mysqli_fetch_array($existResult);
          $real_balance = new Math_BigInteger($existRow[0]);
          $poolRevenue = $poolRevenue->add($real_balance);
          $formatedPoolRevenue = $poolRevenue->toString();

          $task = "UPDATE miners SET balance='$formatedPoolRevenue' WHERE address='$pool_fee_revenue';";
          $query = mysqli_query($mysqli, $task) or die("Database Error");

          $task = "INSERT INTO shares_history SELECT * FROM shares WHERE blockid <= $blockdecimalrow";
          $query = mysqli_query($mysqli, $task) or die("Database Error");

          $task = "DELETE FROM shares WHERE blockid <= $blockdecimalrow";
          $query = mysqli_query($mysqli, $task) or die("Database Error");


          $existQuery = "SELECT address, balance FROM miners WHERE balance!='0'";
          $existResult = mysqli_query($mysqli, $existQuery) or die("Database Error");
          $total = new Math_BigInteger(0);
          while ($row=mysqli_fetch_row($existResult)){
            $payer_adr = $row[0];
            $balanceforthisaddr = new Math_BigInteger($row[1]);
            $total = $total->add($balanceforthisaddr);
          }
          $updatebalanceaddr = $total->toString();


          $task = "UPDATE info SET balance='$updatebalanceaddr' WHERE id=1;";
          $query = mysqli_query($mysqli, $task) or die("Database Error");
        }
        if ($found == 1) {
          echo "\n\nOne block found\n\n";
          $current .= "\n\nOne block found\n\n";
          $found = 0;
          if ($logstate) {
            file_put_contents($file, $current);
          }
          $current = '';
          break;
        }
    }
  }
    echo "\n\n=====LOOP DONE=====\n";
    $current .= "\n\n=====LOOP DONE=====\n";

    echo "\nsleeping...";
    $current .= "\nsleeping...";
    echo "\nRound:".$df;
    $current .= "\nRound:".$df;
    sleep(30);
  }
  else {
    echo "\nWithdraw in background";
    $current .= "\nWithdraw in background";
    sleep(30);
  }
}

?>
