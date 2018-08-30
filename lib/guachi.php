<?php
/*
            ____                  _     _
           / ___|_   _  __ _  ___| |__ (_)
          | |  _| | | |/ _` |/ __| '_ \| |
          | |_| | |_| | (_| | (__| | | | |
           \____|\__,_|\__,_|\___|_| |_|_|
Copyright (c) 2014  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2018  Díaz  Víctor  aka  (Máster Vitronic)
<vitronic2@gmail.com>   <mastervitronic@vitronic.com.ve>
*/


/** esto deberia implementarlo en una clase
 *
 * Modo paranoia, si se llama directo a este script
 * lo mando a la entrada
 */
function paranoia() {
    if (empty($_SERVER['HTTP_REFERER'])) {
        require_once ROOT . 'modulos' . DS . 'error.php';
        exit();
    }
}

/**
 * retorna true si es un entero
 * @return boolean
 *
 */
function isInteger($input){
    //return(ctype_digit(strval($input)));
    return is_int($input) || is_float($input);
}

/*setea en modo cache a ezsql*/
function cache_sql($cbd, $timeout = false) {
    $cache_timeout = ($timeout !== false)?$timeout : 24;
    $cbd->cache_dir = ROOT.'cache_sql';
    $cbd->use_disk_cache = true;
    $cbd->cache_queries = true;
    $cbd->cache_timeout = $cache_timeout;
}

/*Alpine no tiene locale asi que con esto fijo el problema*/
function spanishdate($date) {
    $spanish = [
        'dia' => [
            0=>"Dom", 1=>"Lun",2=>"Mar",3=>"Mié",4=>"Jue",5=>"Vie",6=>"Sáb"
        ],
        'mes' => [
            1=>"Ene",2=>"Feb",3=>"Mar",4=>"Abr",5=>"May",6=>"Jun",
            7=>"Jul",8=>"Ago",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dic"
        ]
    ];
    return (object)[
        'dia'   => $spanish['dia'][date('w',strtotime($date))],
        'mes'   => $spanish['mes'][date('n',strtotime($date))],
        'fecha' => date('j',strtotime($date)),
        'anio'  => date('Y',strtotime($date)),
        'hora'  => date('h:i:s A',strtotime($date))
    ];
} 

/**
 * getBrowser
 *
 * Intenta detectar el browser usado
 */
function getBrowser() {
    function check_agent($agent) {
        return (strpos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) ;
    }
    $bowser = false;
    if (check_agent('MSIE')) {
        $bowser = 'IE';
    }elseif (check_agent('Trident')) {
        $bowser = 'IE';
    }elseif (check_agent('Firefox')) {
        $bowser = 'Firefox';
    }elseif (check_agent('Chrome')) {
        $bowser = 'Chrome';
    }elseif (check_agent('Opera Mini')) {
        $bowser = 'Opera';
    }elseif (check_agent('Opera')) {
        $bowser = 'Opera';
    }elseif (check_agent('Safari')) {
        $bowser = 'Safari';
    }elseif (check_agent('Dillo')) {
        $bowser = 'Dillo';
    }elseif (check_agent('w3m')) {
        $bowser = 'w3m';
    }elseif (check_agent('Elinks')) {
        $bowser = 'Elinks';
    }elseif (check_agent('Links')) {
        $bowser = 'Links';
    }
    return $bowser ;
}

/*intenta obtener la ip del cliente*/
function get_ip(){
    $ip = false;
    if( filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ) {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}