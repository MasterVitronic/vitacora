<?php
/*
           ____  _     _             _
          / ___|(_) __| | ___  _ __ (_) __ _
          \___ \| |/ _` |/ _ \| '_ \| |/ _` |
           ___) | | (_| | (_) | | | | | (_| |
          |____/|_|\__,_|\___/|_| |_|_|\__,_|
Copyright (c) 2017  Díaz  Víctor  aka  (Máster Vitronic)
<vitronic2@gmail.com>   <mastervitronic@vitronic.com.ve>
*/
paranoia();
$auth->check_logged();
/* Lista de resultados del dia */
if (isset($limpio->resultados)) {
    $search = limpiador::clear_data($_POST['search']['value']);
    $limit = '';
    if ( isset($limpio->start) and $limpio->length != '-1' ) {
            $limit = "limit ".intval($limpio->length)." offset ".intval($limpio->start);
    }
    /*para ahorrarme code*/
    $and_fecha= " and date(fecha)=date('now','localtime') ";
    if(is_date($search) === true){
        $and_fecha = " and date(fecha)='". fecha_sql(str_replace('/', '-', trim($search))) . "'";
    }
    /*el total de tickets vendidos*/
    $recordsTotal = $cbd->get_var('select count(fecha_registro) from resultados '
            . " where 1=1 "/*pequeña trampita*/
            . " $and_fecha ");
    $sql = 'select '
           . 'numero,loteria,'
           . 'hora,alias '
           . 'from resultados '
           . "where 1=1 $and_fecha "
           . "order by loteria desc,cast(hora as time) desc";
    $consulta = $cbd->get_results($sql);
    if ($consulta) {
        for ($primera = 0, $i = $primera, $ultima = count($consulta) - 1; $i <= $ultima; $i++) {
            $datos [] = [
                'linea'         => ($ultima-$i+$primera+1),/*conteo inverso para los registros(los ultimos son los primeros)*/
                'numero'        => $consulta[$i]->numero,
                'loteria'       => $consulta[$i]->loteria,
                'hora'          => date(format_time,strtotime($consulta[$i]->hora)),
                'alias'         => $consulta[$i]->alias
            ];
        }
    }
    /*calculos para el paginador de datatables*/
    if($search){
        /*OK existe search asi que ahora veo si existen coincidencias*/
        if(!count($datos)){
            /*no existieron conincidencias los filtrados tons son 0*/
            $recordsFiltered = 0;
        }else{
            /*existieron coincidencias los filtrados son la cantidad de registros encontrados*/
            $recordsFiltered = count($datos);
        }
    }else{
        /*no hay busqueda por search asi que los filtrados son iguales a los totales*/
        $recordsFiltered = $recordsTotal;
    }
    header_json();
    echo json_encode([
        'draw'            => intval( $limpio->draw ),
        'recordsTotal'    => intval( $recordsTotal ),
        'recordsFiltered' => intval( $recordsFiltered ),
        'data'            => $datos
    ]);
}