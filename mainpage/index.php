<?php
error_reporting(error_reporting() & ~E_NOTICE);
include_once('../BigInteger.php');
$jsonquery = file_get_contents('php://input');
$json = json_decode($jsonquery, true);
$config = include('../config.php');
$redis = include_once('../RedisInit.php');

$minerdata = $_GET["miner"];
$host = $_SERVER["REMOTE_ADDR"];

//Share stats reset
$shareCounter = 5000;
//Pool Diff
$miner_diff = 15000000;
//$miner_diff = 25000000;

//$m = new Memcached();
//$m->addServer('localhost', 11211);

//If not miner > website
if (strpos($minerdata,'@') === false) {
  echo '<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>Ethereum Mining Pool</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ethereumpool is stable, transparent and fair mining pool with low fee and great support! Just switch your rig to us, and see it on yourself :)">
    <meta name="author" content="Ethereumpool.co">
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="Ethereum Mining Pool"/>
    <meta property="og:description"        content="Stable, transparent and fair mining pool with low fee and great support! Just switch your rig to us, and see it on yourself :)"/>
    <link rel="shortcut icon" href="../favicon.ico">  
    <meta name="keywords" content="eth,gpu,mining,mine,ethereum,calculator,profitability,profit,how,to,ether,ethers">
    <link href="http://fonts.googleapis.com/css?family=Merriweather+Sans:700,300italic,400italic,700italic,300,400" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <!-- Global CSS -->
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">   
    <!-- Plugins CSS -->    
    <link rel="stylesheet" href="../assets/plugins/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/plugins/elegant_font/css/style.css">
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="../assets/css/styles-2.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head> 
<body class="blog-home-page">   
    <div class="header-wrapper header-wrapper-blog-home">
        <!-- ******HEADER****** --> 
        <header id="header" class="header navbar-fixed-top">  
            <div class="container">       
                <h1 class="logo">
                    <a href="../"><span class="highlight">Ethereum</span>Pool</a>
                </h1><!--//logo-->
                <nav class="main-nav navbar-right" role="navigation">
                    <div class="navbar-header">
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button><!--//nav-toggle-->
                    </div><!--//navbar-header-->
                    <div id="navbar-collapse" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li class="active nav-item"><a href="..">Home</a></li>
                            <li class="nav-item"><a href="../stats">Stats</a></li> 
                            <li class="nav-item"><a href="../charts">Charts</a></li>
                            <li class="nav-item"><a href="../stats/miner/">Miner Stats</a></li>               
                            <li class="nav-item last"><a href="../how">How to Mine?</a></li>
                            <li class="nav-item last"><a href="mailto:mail@mail.com">Support</a></li>
                        </ul><!--//nav-->
                    </div><!--//navabr-collapse-->
                </nav><!--//main-nav-->
            </div><!--//container-->
        </header><!--//header-->   
        
    
    <!-- ******Contact Section****** --> 
    <section class="contact-section section">
        <div class="container">
            <h2 class="title text-center"><br>The most reliable mining pool yet!</h2>
            <p class="intro text-left"></p>
             <p class="intro text-left"><font color="F22613"></p></font>
            <form id="contact-form" class="contact-form form" method="post" action="push.php">                    
                <div class="row text-left">
                    <div class="contact-form-inner col-md-8 col-sm-12 col-xs-12 col-md-offset-2 col-sm-offset-0 xs-offset-0">
                        <div class="row">                                                                                       
							Ethereum Pool is all about transparency and fair mining! Pool fee is 0.8% and network fee on withdraw<br>
							Withdraw is twice a day if your balance exceed 0.5 ether.<br>
							<br>
							<!--center><a class="twitter-timeline" width="500" height="300" href="https://twitter.com/twitter" data-widget-id="638177314422046722">Tweets by @ethereumpool</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                            </center--><br><br><b>In How to mine section you will find answers for questions:</b><br>
							How connect to pool?<br>
                            How to set up ethereum client (wallet)?<br>
                            How to use ethereum?<br>
							How to install ethereum on linux?<br>
							How to install ethereum on osx (mac)?<br>
							How to install ethereum on windows?<br>
							How does pool calculate revenue for each miner?<br>
							When i will receive withdraw?<br>
							If Pool is safe?<br>
							How to get MHash value?<br>
							How to check ethereum balance?<br>
							How to create ethereum address?<br>
							How to send ethers?<br>
							How to download and install ethminer?<br>
							How to download and install geth?<br>
							And much more in linked sites!<br>All for free<br><br>

									<br><br><br>

						</div><!--//row-->
                    </div>
                </div><!--//row-->
                <div id="form-messages"></div>
            </form><!--//contact-form-->
        </div><!--//container-->
    </section><!--//contact-section-->
    
            
   <!-- ******FOOTER****** --> 
    <footer class="footer">
        <div class="footer-content">
            <div class="container">
                <div class="row">
                    <div class="footer-col col-md-3 col-sm-4 links-col">
                        <div class="footer-col-inner">
                            <h3 class="sub-title">Quick Links</h3>
                            <ul class="list-unstyled">
                                <li><a href="..">Home</a></li>
                                <li><a href="../stats">Pool statistics</a></li>
                                <li><a href="../charts">Charts</a></li>
                                <li><a href="../stats/miner/">Miner statistics</a></li>
                                <li><a href="../how">How to start mine?</a></li>                           
                                <li><a href="mailto:mail@mail.com">Support</a></li>
                            </ul>
                        </div><!--//footer-col-inner-->
                    </div><!--//foooter-col-->
                     <div class="footer-col col-md-6 col-sm-8 blog-col">
                                <br>
                            </div><!--//foooter-col--> 
                    <div class="footer-col col-md-3 col-sm-12 contact-col">
                        <div class="footer-col-inner">
                            <h3 class="sub-title"></h3>
                            <p class="intro"></p>
                            <div class="row">
                                <p class="adr clearfix col-md-12 col-sm-4">
                                    <span class="adr-group">
                                    </span>
                                </p>
                            </div> 
                        </div><!--//footer-col-inner-->            
                    </div><!--//foooter-col-->   
                </div>   
            </div>        
        </div><!--//footer-content-->
    
 
    <!-- Main Javascript -->          
    <script  type="text/javascript" src="../assets/plugins/jquery-1.11.2.min.js"></script>
    <script  type="text/javascript" src="../assets/plugins/jquery-migrate-1.2.1.min.js"></script>
    <script  type="text/javascript" src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script> 
    <script  type="text/javascript" src="../assets/plugins/bootstrap-hover-dropdown.min.js"></script>       
    <script  type="text/javascript" src="../assets/plugins/back-to-top.js"></script>             
    <script  type="text/javascript" src="../assets/plugins/jquery-placeholder/jquery.placeholder.js"></script>                                                                  
    <script  type="text/javascript" src="../assets/plugins/jquery-match-height/jquery.matchHeight-min.js"></script>     
    <script  type="text/javascript" src="../assets/plugins/FitVids/jquery.fitvids.js"></script>
    <script  type="text/javascript" src="../assets/js/main.js"></script>     
    
    <!-- Form Validation -->
    <script  type="text/javascript" src="../assets/plugins/jquery.validate.min.js"></script> 
    <script  type="text/javascript" src="../assets/js/form-validation-custom.js"></script> 
    
    <!-- Form iOS fix -->
    <script  type="text/javascript" src="../assets/plugins/isMobile/isMobile.min.js"></script>
    <script  type="text/javascript" src="../assets/js/form-mobile-fix.js"></script>     
</body>
</html>';
  die();
}

//Get data from miner
$minderdata_array = explode('@', $minerdata);
$hash_rate = $minderdata_array[0];
$payout_addr = $minderdata_array[1];
$rig_name = $minderdata_array[2];


//Fix data if possible, if not die
if (strpos($payout_addr,'/') !== false) {
  $arrayWithAddr = explode('/', $payout_addr);
  for ($i=0; $i < count($arrayWithAddr)-1; $i++) {
    if (strlen($arrayWithAddr[$i]) == 42) {
      $payout_addr = $arrayWithAddr[$i];
    }
  }
}
if (strpos($payout_addr,'0x0x') !== false) {
  $payout_addr = substr($payout_addr, 2);
}
if (strlen($payout_addr) < 42) {
  die('Invalid Ethereum address');
}
if ($hash_rate < 0.01) {
  die();
}

/*
MINER METHODS
eth_getWork
eth_submitWork
eth_submitHashrate
eth_awaitNewWork
eth_progress
*/

//Remove invalid requests
$hash_rate = mysql_fix_escape_string($hash_rate);
$payout_addr = mysql_fix_escape_string($payout_addr);
if ($payout_addr == '' || $hash_rate == '' || (strpos($payout_addr,'0x') === false)) {
  die();
}


//Get Method
$method = $json['method'];


//On/off Logging
$logstate = true;
//If there is no log for particular user on ymdh time, then create
if($logstate) {
  $filename = $payout_addr.'='.date('Y M D H');
  $file = 'logs/'.$filename.'.txt';
  if(!file_exists($file)) {
    $fh = fopen($file, 'w');
    fclose($fh);
  }
  if ($logstate) {
    $current = file_get_contents($file);
  }
}
$current .= "\n\n\n\n---------------------------New Query\nMethod:".$method.' From IP:'.$host;



//Get Last Block information  Memcached - > Run /process_work in screen
$current .= "\nUser Hashrate S:" . $hash_rate . 'mhash';
$current .= "\nUser payout S:" . $payout_addr . '';


if ($method == 'eth_awaitNewWork' || $method == 'eth_progress') {
  //Redirect other methods to RPC
  $ch = curl_init('http://127.0.0.1:8983');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonquery);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonquery))
  );
  $result = curl_exec($ch);
  $current .= "\n\nResponse:" . $result;
  echo $result;
}
else if($method == 'eth_submitHashrate') {
  $hashrateReported = hexdec($json['params'][0]);
  $cur_time = time();
  $hashdata = array($payout_addr, $hash_rate, $hashrateReported, $cur_time);

  $currentMemArr = $redis->get('R_Hash:' . $payout_addr);
  $countFetchedArray = count($currentMemArr);

  $miner_total = array();
  if ($currentMemArr) {

    $firstStamp = $currentMemArr[0][3];
    $timeDifference = intval($cur_time) - intval($firstStamp);
    $current .= "\n\nTIME DIFF:".$timeDifference;
    if ($timeDifference >= 60) {
      $current .= "\n\nR_LONGER:".$timeDifference;
      for ($i=0; $i < $countFetchedArray-1; $i++) {
        $fetchedJson = $currentMemArr[$i];
        $miner_addr = $fetchedJson[0];
        $miner_id = $fetchedJson[1];
        $miner_hashrate = $fetchedJson[2];
        $miner_timestamp = $fetchedJson[3];

        if (!isset($miner_total["'$miner_id'"])) {
          $miner_total["'$miner_id'"] = $miner_hashrate;
        } else {
          $new_val = $miner_total["'$miner_id'"] + $miner_hashrate;
          $new_val = $new_val/2;
          $miner_total["'$miner_id'"] = $new_val;
        }
      }
      $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
      $lastItem = $countFetchedArray - 1;
      $last_timestamp = $currentMemArr[$lastItem][3];
      foreach ($miner_total as $key => $value) {
        $current .= "\n\nSHOULD WORK once:" . $value;
        $key = str_replace('"', '', $key);
        $key = str_replace("'", '', $key);
        $add2Stats = "INSERT INTO stats (user, userid, hashrate, val_timestamp) VALUES ('$payout_addr', '$key', '$value', '$last_timestamp')";
        $querydone = mysqli_query($mysqli,$add2Stats) or die("Database Error");
      }
      $new = array();
      array_push($new, $hashdata);
      $redis->set('R_Hash:' . $payout_addr, $new, 360);
    } else {
      array_push($currentMemArr, $hashdata);
      $redis->set('R_Hash:' . $payout_addr, $currentMemArr, 360);
    }
  } else {
    $new = array();
    array_push($new, $hashdata);
    $redis->set('R_Hash:' . $payout_addr, $new, 360);
  }
  //DO NOT USE GETH TO HANDLE HASHRATE
  $ch = curl_init('http://127.0.0.1:8983');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonquery);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonquery))
  );
  $result = curl_exec($ch);
  // might need to comment out
  $current .= "\n\nResponse hashrate:" . $result;
  //echo $result;
  //*/
  $data_redit = array("id" => 73, "jsonrpc" => "2.0", "result" => true);
  $data_string_redit = json_encode($data_redit);
  echo $data_string_redit;

} else if($method == 'eth_submitWork') {
  $current .= "\n\Work:".$jsonquery;
  $ch = curl_init('http://127.0.0.1:8983');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonquery);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonquery))
  );
  $result = curl_exec($ch);
  $current .= "\n\nResponse:" . $result;
  $submitWork = json_decode($result, true);
  $submitWorkResult = $submitWork['result'];
  echo '{"jsonrpc":"2.0","id":1,"result":true}'; //Override response from geth to consider share submitted.

  //Submit New User or update randomly ip and hashrate
  if($payout_addr != '') {
    $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
    $existQuery = "SELECT address, hashrate FROM miners WHERE address='$payout_addr'";
    $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
    $existRow = mysqli_fetch_array($existResult);
    $mineraddr = $existRow[0];
    $hashrate = $existRow[1];
    if ($mineraddr == '') {
      $tas22k = 'INSERT INTO miners (address, ip, hashrate, balance) VALUES ("'.$payout_addr.'", "'.$host.'", "'.$hash_rate.'", "0")';
      $query = mysqli_query($mysqli,$tas22k) or die("Database Error");
    } else {
      $dice = rand(1, 10000);
      if ($dice > 6000) {
        $task = "UPDATE miners SET ip='$host', hashrate='$hash_rate' WHERE address='$payout_addr';";
        $query = mysqli_query($mysqli,$task) or die("Database Error");
      }
    }
  }

  //ADJUST DIFF
  $shareCheckerKey = 'submiting_' . $payout_addr . '_' . $hash_rate;
  $CheckShareData = $redis->get($shareCheckerKey);
  $CheckShareData = $CheckShareData + 1;
  $redis->set($shareCheckerKey, $CheckShareData, 30);
  //////////////////////////////////
  //Override response from geth due it's changes, always pass work for futher processing
  if (1 == 1) {
    $jsonparm = $json['params'];
    $appKey = md5($hash_rate . $payout_addr);
    $current .= "\nAPPKEY:".$appKey;
    $dataForApp = $redis->get($appKey);
    if ($dataForApp[4] == $jsonparm[1]) {
      $current .= "\n==========================================================================";
      $current .= "\n=======================WORK HAS BEEN SUMBITED=============================";
      $current .= "\nUser:".$dataForApp[0];
      $current .= "\nUserDiff:".$dataForApp[1];
      $current .= "\nDiffDecimal:".$dataForApp[2];
      $current .= "\nBlockDiff:".$dataForApp[3];
      $current .= "\nBlockPowHash:".$dataForApp[4];
      $current .= "\nRealBlockTarget:".$dataForApp[5];
      $current .= "\nSeedHash:".$dataForApp[7];
      $current .= "\nBlock Number:".$dataForApp[6].' / '.hexdec($dataForApp[6]);
      $current .= "\n==========================================================================";
      $current .= "\nNonceFound:".$jsonparm[0];
      $current .= "\nPowHash:".$jsonparm[1];
      $current .= "\nMixDigest:".$jsonparm[2];
      $current .= "\n==========================================================================";
      $current .= "\n==========================================================================";


      $shareKey = 'share_ok';
      $shareData = $redis->get($shareKey);
      if ($shareData > $shareCounter) {
        //$m->set($shareKey,0,360);
        //$m->set('share_fail',0,360);
        $items = array(
          'share_ok' => 0,
          'share_fail' => 0
        );
        $reds->mSet($items, time() + 360);
      } else {
        $shareData = $shareData + 1;
        $redis->set($shareKey,$shareData,360);
      }

      $existQuery = "SELECT address FROM shares WHERE nonceFound='$jsonparm[0]'";
      $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
      $existRow = mysqli_fetch_array($existResult);
      $addrchecker = $existRow[0];
      if (!$addrchecker) {
        $timeNow = time();
        $tas22k = 'INSERT INTO shares (blockid, address, minertarget, minerdiff, blockdiff, blockPowHash, realBlockTarget, nonceFound, FoundPowHash, Digest, seedhash, time) VALUES ("'.$dataForApp[6].'", "'.$dataForApp[0].'", "'.$dataForApp[1].'", "'.$dataForApp[2].'", "'.$dataForApp[3].'", "'.$dataForApp[4].'", "'.$dataForApp[5].'", "'.$jsonparm[0].'", "'.$jsonparm[1].'", "'.$jsonparm[2].'", "'.$dataForApp[7].'", "'.$timeNow.'")';
        $query = mysqli_query($mysqli,$tas22k) or die("Database Error");
      } else {
        die('Dead');
      }
    } else {
      $current .= "\n===========WORK HAS NOT BEEN SUMBITED DUE POW HASH DIFFERENCE=============";
      $current .= "\nSubmitPow:".$jsonparm[1];
      $current .= "\nGetWorPow:".$dataForApp[4];
    }
  } else {
    $current .= "\n===========WORK HAS NOT BEEN SUMBITED DUE EXPIRED SOLUTION=============";

    $shareKey = 'share_fail';
    $shareData = $redis->get($shareKey);
    if ($shareData > $shareCounter) {
      //$m->set($shareKey,0,360);
      //$m->set('share_ok',0,360);
      $items = array(
        'share_ok' => 0,
        'share_fail' => 0
      );
      $redis->mSet($items, time() + 360);
    } else {
      $shareData = $shareData + 1;
      $redis->set($shareKey,$shareData,360);
    }


  }
}
else if($method == 'eth_getWork') {
  $shareCheckerKey = 'submiting_' . $payout_addr . '_' . $hash_rate;
  $keys = array(
    'blockinfo',
    'eth_getWork_response',
    $shareCheckerKey
  );

  $got = $redis->mGet($keys);
  while (!$got) {
    usleep(100000);
    $got = $redis->mGet($keys);
  }

  $result1 = $got[0];
  $result = $got[1];
  $key_Key = $shareCheckerKey;
  $CheckShareData = $got[$key_Key];

  //$result1 = $m->get('blockinfo');
  $block_info_last = json_decode($result1, true);

  $current .= "\n\nTEST NEW:" . print_r($block_info_last, TRUE);

  $last_block_result = $block_info_last['result'];
  $last_block_diff = $last_block_result['difficulty'];
  $last_block_timestamp = new Math_BigInteger(hexdec($last_block_result['timestamp']));
  $current_time = new Math_BigInteger(time());
  $lastBlockTime_BI = $current_time->subtract($last_block_timestamp);
  $lastBlockTimeHex = $lastBlockTime_BI->toHex();
  $lastBlockTime = hexdec($lastBlockTimeHex);
  $block_number = $last_block_result['number'];
  $current .= "\n\nLAST BLOCK Diff:" . $last_block_diff . ' / ' . hexdec($last_block_diff);
  $current .= "\nLAST BLOCK Time:" . $last_block_timestamp;
  $current .= "\nLAST BLOCK Time:" . $lastBlockTime.'s';
  //Miner Get Work   Memcached -> run process_work
  //$result = $m->get('eth_getWork_response');

  $current .= "\n\nRespons1:" . $result;
  $TargetBlock = json_decode($result, true);
  $targetBlockResult = $TargetBlock['result'];
  $diffTarget = $targetBlockResult[2];
  $last_block_diff = new Math_BigInteger(hexdec($last_block_diff));

  if($hash_rate) {
    $shareCheckerKey = 'submiting_' . $payout_addr . '_' . $hash_rate;
    //DONE HIGHER -
    //$CheckShareData = $m->get($shareCheckerKey);
    if (!$CheckShareData) {
      $fixed_diff = floatval($hash_rate);
    }
    else {
      $fixed_diff = floatval($hash_rate * $CheckShareData * 4);
    }
    $fixed_diff = $fixed_diff * $miner_diff;
    $fixed_diff = new Math_BigInteger($fixed_diff);
    $current .= "\nFixed diff value:".$fixed_diff;
  }
  else {
    die('You need to specify your hashrate!');
  }

  $a256 = new Math_BigInteger('115792089237316195423570985008687907853269984665640564039457584007913129639936');  //2^256

  //Convert diff decimal to hex 256bit
  $new_block_diff = new Math_BigInteger($fixed_diff);
  list($quotient, $remainder) = $a256->divide($new_block_diff);
  $target_diff = $quotient->toString();
  $target_diff = bcdechex($target_diff);

  $currentLenght = strlen($target_diff);
  $desiredLenght = 64;
  if ($currentLenght < $desiredLenght) {
    $toadd = $desiredLenght - $currentLenght;
    for ($i=0; $i < $toadd; $i++) {
      $fix .= '0';
    }
    $target_diff = '0x' . $fix . $target_diff;
  }

  //Save Getwork for user to validate later with submit work
  $appKey = md5($hash_rate . $payout_addr);
  $current .= "\nAPPKEY:" . $appKey;
  $block_number = hexdec($block_number) + 1;
  $dataWrite =  array($payout_addr, $target_diff, $fixed_diff, $last_block_diff, $targetBlockResult[0], $targetBlockResult[2], $block_number, $targetBlockResult[1]);
  //$m->set($appKey,$dataWrite,120);
  //$m->set($payout_addr,$dataWrite,40);

  $miner_reference = $hash_rate . $payout_addr;

  $items = array(
    $appKey => $dataWrite,
    $payout_addr => $dataWrite,
    $miner_reference => $rig_name
  );
  $redis->mSet($items);

  //Overwrite rpc method
  $data_redit = array("id" => 1, "jsonrpc" => "2.0", "result" => [$targetBlockResult[0], $targetBlockResult[1], $target_diff]);
  $data_string_redit = json_encode($data_redit);

  $current .= "\nTarget:" . $target_diff;
  $current .= "\n\nRespons2:" . $data_string_redit;

  echo $data_string_redit;
}
else {
  echo 'Method not allowed';
}

//Save logs if enabled
if ($logstate) {
  file_put_contents($file, $current);
}

function mysql_fix_escape_string($text){
  if(is_array($text))
    return array_map(__METHOD__, $text);
  if(!empty($text) && is_string($text)) {
    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
      array('', '', '', '', "", '', ''),$text);
  }
  $text = str_replace("'","",$text);
  $text = str_replace('"',"",$text);
  return $text;
}

function bcdechex($dec) {
  $hex = '';
  do {
    $last = bcmod($dec, 16);
    $hex = dechex($last).$hex;
    $dec = bcdiv(bcsub($dec, $last), 16);
  } while($dec>0);
  return $hex;
}

?>
