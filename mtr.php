<?php

// database settings 
$db_username = 'info_site';
$db_password = 'info_site20195';
$db_name = 'info_site';
$db_host = 'localhost';

//mysqli
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

header("Content-type:text/plain");
$file = explode(",", file_get_contents("/etc/fbx/sites_mtr.txt"));
array_pop($file);
foreach ($file as $url){ 

    error_log($url."\n");

    $cmd =  shell_exec("/usr/sbin/mtr -w --ipinfo 0 -c5 $url | awk 'NR>1 {print $2 ,$3 , $4, $5, $6, $7, $8, $9, $10}'");

    // echo "\n".$url."\n";
    $oparray = preg_split('#[\r\n]+#', trim($cmd));
    array_shift($oparray);


    if(isset($cmd)){
        $delete = $mysqli->query("DELETE FROM monitor_mtr WHERE url='$url'");
        if (!$delete) {  
            echo "ERROR - não pode deletar linhas";
            exit();
        }
    }

    error_log($delete."\n");
    foreach ($oparray as $key => $value){ 
        $info = explode(" ", $value);
        // error_log(print_r($info,true));        

        $info[2] = (int) str_replace("%","",$info[2]);

        $info[3] = (float) $info[3];
        $info[4] = (float) $info[4];
        $info[5] = (float) $info[5];
        $info[6] = (float) $info[6];
        $info[7] = (float) $info[7];
        $info[8] = (float) $info[8];

        $results = $mysqli->query("INSERT INTO monitor_mtr (url, ASN,ip,package_loss,snt,last,avg,best,wrst,stdev) VALUES ('$url','$info[0]',".
        "'$info[1]',$info[2],$info[3],$info[4],$info[5],$info[6],$info[7],$info[8])");
        if (!$results) {  
            echo "ERROR";
            exit();
        }else{
            echo "ok \n";
        } 

        // [0] => AS15169 ASN
        // [1] => gru10s10-in-f14.1e100.net IP
        // [2] => 0.0% PACKAGE LOSS
        // [3] => 20 SENT
        // [4] => 1.0 
        // [5] => 0.9
        // [6] => 0.8
        // [7] => 1.6
    }
