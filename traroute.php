<?php


// database settings 
$db_username = 'traceroute';
$db_password = 'traceroute20195';
$db_name = 'traceroute';
$db_host = 'localhost';

//mysqli
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

header("Content-type:text/plain");
$file = explode(",", file_get_contents("/etc/fbx/sites.txt"));

foreach ($file as $url){ 

    $cmd =  shell_exec("traceroute -n -q 1 $url | awk  '{print $2,$3}'");

    $oparray = preg_split('#[\r\n]+#', trim($cmd));
    array_shift($oparray);

    $data = array(
        "data" => array()
    );

    // $delete1 = $mysqli->query("DELETE FROM traceroutesIPS_reten WHERE destinatario='$url' and create_date < (NOW() - INTERVAL 10 MINUTE)");

    $delete = $mysqli->query("DELETE FROM traceroutesIPS WHERE destinatario='$url'");
    if (!$delete) {  
        echo "ERROR - não pode deletar linhas";
        exit();
    }

    foreach ($oparray as $key => $value){ 

        $info = explode(" ", $value);
        if ($info[0] != "*"){
            
            $ip = $info[0];
            $tr = (float) $info[1];
            $qtdSaltos = sizeof($oparray);
            
            // $results1 = $mysqli->query("INSERT INTO traceroutesIPS_reten (destinatario, quantidade_saltos,ip,tempo_resposta) VALUES ('$url',$qtdSaltos,'$ip',$tr)");
            $results = $mysqli->query("INSERT INTO traceroutesIPS (destinatario, quantidade_saltos,ip,tempo_resposta) VALUES ('$url',$qtdSaltos,'$ip',$tr)");
            if (!$results) {  
                echo "ERROR";
                exit();
            }else{
                echo "ok \n";
            } 
            
            
        }
    }
}
