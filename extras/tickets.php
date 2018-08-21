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
/* Lista de tickets vendidos */
if (isset($limpio->vendidos)) {
    $search = limpiador::clear_data($_POST['search']['value']);
    $and_serial_ticket = (isset($search) and !empty($search)) ? " and serial_ticket like '%".$search."%' " : '';
    $limit = '';
    if ( isset($limpio->start) and $limpio->length != '-1' ) {
            $limit = "limit ".intval($limpio->length)." offset ".intval($limpio->start);
    }
    /*para ahorrarme code*/
    $and_fecha= (isset($search) and !empty($search)) ? '' : " and strftime('%Y-%m-%d',fecha_registro)=strftime('%Y-%m-%d',datetime('now','localtime')) ";
    if(is_date($search) === true){
        $and_fecha = " and strftime('%Y-%m-%d',fecha_registro)='". fecha_sql(str_replace('/', '-', trim($search))) . "'";
        $and_serial_ticket = '';
    }
    /*el total de tickets vendidos*/
    $recordsTotal = $cbd->get_var('select count(fecha_registro) from tickets '
            . ' inner join seriales_tickets on (seriales_tickets.id_serial_ticket=tickets.id_serial_ticket)'
            . " where 1=1 "/*pequeña trampita*/
            . " $and_fecha ");
    /*la consulta Sql*/
    $sql = 'select '
            . ' h_tickets.id_ticket,'
            . " strftime('%H:%M',h_tickets.fecha_registro) as hora_venta,"
            . ' seriales_tickets.serial_ticket,'
            . ' sum(items_tickets.monto) as monto_total'
            . ' from tickets h_tickets'
            . ' inner join seriales_tickets on (seriales_tickets.id_serial_ticket=h_tickets.id_serial_ticket)'
            . ' inner join items_tickets    on (items_tickets.id_ticket=h_tickets.id_ticket)'
            . " where 1=1 "/*pequeña trampita*/
            . " $and_serial_ticket "
            . " $and_fecha"
            . ' group by h_tickets.id_ticket,seriales_tickets.serial_ticket'
            . " order by h_tickets.fecha_registro desc $limit";
    $consulta = $cbd->get_results($sql);
    if ($consulta) {
        for ($primera = 0, $i = $primera, $ultima = count($consulta) - 1; $i <= $ultima; $i++) {
            $datos [] = [
                'linea'         => ($ultima-$i+$primera+1),/*conteo inverso para los registros(los ultimos son los primeros)*/
                'id_ticket'     => $consulta[$i]->id_ticket,
                'hora_venta'    => $consulta[$i]->hora_venta,
                'serial_ticket' => $consulta[$i]->serial_ticket,
                'monto_total'   => format_money($consulta[$i]->monto_total, locale_money, char_remove_money)
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
        'data' => $datos
    ]);
}

/*Genera un nuevo serial y lo envia al cliente*/
if (isset($limpio->new_serial) and $limpio->new_serial === 'true') {
    $serial_ticket = $tiket->new_serial();
    if ($serial_ticket) {
       $datos = [
            'ok'     => true,
            'serial' => $serial_ticket
        ];
    } else {
       $datos = ['ok'=>false];
    }
    header_json();
    echo json_encode($datos);
}

/*Registra un nuevo Ticket*/
if (isset($limpio->new_tiket) and $limpio->new_tiket === 'true') {
    //error_reporting(0);/**/
    /*@TODO hay un cuello de botella aqui de mas de 700ms pareciera ser el commit de la db*/
    $config = $auth->get_conf();
    $serial_ticket  = limpiador::clear_data($_POST['ticket']['serial_ticket']);
    $total_ticket   = intval(limpiador::clear_data($_POST['ticket']['total_ticket']));
    if (isset($serial_ticket) and isset($total_ticket) and $serial_ticket !== '000000000' and $total_ticket >= $config->jugada_minima) {
        if (isset($_POST['ticket']['items'])) {
            foreach ($_POST['ticket']['items'] as $campo => $valor) {
                /*armo cuidadosamente cada item del ticket*/
                $items[] = [
                    'alias'     => limpiador::clear_data($valor['alias']),
                    'hora'      => limpiador::clear_data($valor['hora']),
                    'loteria'   => limpiador::clear_data($valor['loteria']),
                    'id_loteria'=> limpiador::clear_data($valor['id_loteria']),
                    'id_horario'=> limpiador::clear_data($valor['id_horario']),
                    'numero'    => limpiador::clear_data($valor['numero']),
                    'monto'     => limpiador::clear_data($valor['monto'])
                ];
            }
        }
        /*lo voy a hacer asi de momento*/
        $id_serial_ticket = $cbd->get_var("select id_serial_ticket from seriales_tickets where serial_ticket='$serial_ticket'");
        /*lo primero es iniciar la transaccion*/
        $cbd->beginTransaction();
        /*result siempre es true a menos que algo salga mal*/
        $result = true;
        $hashid_serial = $tiket->gen_hashid($serial_ticket,$config->serial);
        $fecha_registro = date('Y-m-d H:i:s');/*hora sql*/
        $hash_ident = md5($config->serial.$hashid_serial.$fecha_registro.$total_ticket.$config->token.$config->operation_mode);
        /*ahora armo la sentencia sql*/
        $crud->datos([
            'id_usuario'        => $auth->id_usuario(),
            'id_serial_ticket'  => $id_serial_ticket,
            'total_monto'       => $total_ticket,
            'fecha_registro'    => $fecha_registro,
            'hashid_serial'     => $hashid_serial,
            'hash_ident'        => $hash_ident
        ]);
        $crud->crear_insert('tickets');
        /*Ejecuto la consulta y si tiene exito tons prosigo*/
        if($cbd->exec($crud->show_sql()) === true){
            /*Ok tuvo exito ahora a guardar los items*/
            /*obtengo el ultimo id de tickets*/
            $id_ticket = $cbd->lastInsertRowID();
            /*verifico que en efecto existan items, por lo menos 1*/
            if(count($items) >= 1 ){
                /*recorro el array items para registrar uno a uno los item*/
                foreach ($items as $valor) {
                    /*@TODO validar aqui item por item, sera mas lento pero temo que es necesario */
                    $obj_item   = (object) $valor;
                    $hash_ident = md5($config->serial.$obj_item->id_loteria.$obj_item->id_horario.$obj_item->numero.$obj_item->monto.$hashid_serial.$fecha_registro.$total_ticket.$config->token.$config->operation_mode);
                    $item = [
                        'id_loteria'=> $obj_item->id_loteria,
                        'id_horario'=> $obj_item->id_horario,
                        'id_ticket' => $id_ticket,
                        'numero'    => $obj_item->numero,
                        'alias'     => $obj_item->alias,
                        'monto'     => $obj_item->monto,
                        'hora'      => $obj_item->hora,
                        'loteria'   => $obj_item->loteria,
                        'hash_ident'=> $hash_ident
                    ];
                    /*Armo la consulta*/
                    $crud->datos($item);
                    $crud->crear_insert('items_tickets');
                    /*Por ultimo inserto el item en la db*/
                    if($cbd->exec($crud->show_sql()) === false){
                        /*Bueno si no puedo por alguna razon guardar un item tons termino aqui y result a false*/
                        $result = false;
                        break;
                    }
                }
            }else{
                $result = false; /*no hay items, result a false*/
            }
        }else{
            /*Bueno si no puedo por alguna razon guardar un ticket tons result a false*/
            $result = false;
        }
        if($result === true){
            /*Todo fue bien!, que alegria, ahora hago el commit*/
            $cbd->commit();

            /*aqui ahora envio esto a impresion*/
            $tiket->set_id_ticket($id_ticket);
            $tiket->is_printable(true);
            $tiket->set_conf_printer();
            $printer = $tiket->imprimir($tiket->render());
            /*Fin de la impresion*/

            /*Envio los datos que que sea procesado por el fronend y sea envia al server*/
            /*@TODO esto necesita mucho mas trabajo*/
            /*@TODO 2017-11-05 15:32:52 parece que ya quedo*/
            $datos = [
                'ok'     => true,
                'print'  => $printer,
                'send'   => $tiket->push_ticket(),
                'msg'    => 'Ticket generado con exito!.'
            ];
        }else{
            /*Algo fallo, cancelo todo en alta y envio el mensaje*/
            $cbd->rollBack();
            $datos = [
                'ok'=>false,
                'print'=>false,
                'msg' => 'Sidonia se ha negado a registrar esta jugada, sorry.'
            ];
        }
    }else{
        $datos = [
            'ok'=>false,
            'print'=>false,
            'msg' => 'Jugada Invalida, Verifique.'
        ];
    }
    /*la cabecera http json*/
    header_json();
    /*escribo los datos en json*/
    echo json_encode($datos);
}

/*Retorna el ticket al cliente*/
if (isset($limpio->get_ticket)) {
    $id_ticket = intval($limpio->get_ticket);
    $sql = ' select * '
            . 'from vista_tickets '
            . "where id_ticket='$id_ticket' "
            . 'order by loteria ';
    $tickets = $cbd->get_results($sql);
    if ($tickets) {
        $config = $auth->get_conf();
        for ($primera = 0, $i = $primera, $ultima = count($tickets) - 1; $i <= $ultima; $i++) {
            $premiado = $tiket->get_premiados($tickets[$i]->id_item_ticket,$id_ticket);
            $items [] = [
                'linea'         => ($ultima-$i+$primera+1),/*conteo inverso para los registros(los ultimos son los primeros)*/
                'numero'        => $tickets[$i]->numero,
                'alias'         => $tickets[$i]->alias,
                'loteria'       => strtoupper(substr($tickets[$i]->loteria, 0, 8)),
                'hora'          => $tickets[$i]->hora,
                'monto'         => format_money($tickets[$i]->monto, locale_money, char_remove_money),
                'premio'        => (isset($premiado->total_premio))?format_money($premiado->total_premio, locale_money, char_remove_money): format_money(0, locale_money, char_remove_money)
            ];
        }
        $premio = $tiket->get_premiados(false,$id_ticket);
        $balance = (isset($premio[0]->total_premio))?($tickets[0]->monto_total-$premio[0]->total_premio):$tickets[0]->monto_total;
        $total_premio = (isset($premio[0]->total_premio))?$premio[0]->total_premio:0;
        $datos = [
            'serial_ticket'     => $tickets[0]->serial_ticket,
            'fecha_registro'    => date(format_fecha, strtotime($tickets[0]->fecha_registro)),
            'hora_registro'     => date('h:i A', strtotime($tickets[0]->fecha_registro)),
            'estatus'           => ($tickets[0]->estatus==='t')?'Normal':'Anulado',
            'fecha_caduca'      => date(format_fecha, strtotime($tickets[0]->fecha_registro. ' + '.$config->dias_caducidad.' days')), // mas n dias
            'hora_caduca'       => date('h:i A', strtotime($tickets[0]->fecha_registro. ' + '.$config->dias_caducidad.' days')), // mas n dias
            'balance'           => format_money($balance, locale_money, char_remove_money), // menos los premios
            'monto_total'       => format_money($tickets[0]->monto_total, locale_money, char_remove_money), //total apuesta
            'premio'            => format_money($total_premio, locale_money, char_remove_money), //el premio en caso de existir
            'items'             => $items
        ];
    }
    header_json();
    echo json_encode($datos);
}

/*Retorna un array con todos los elementos para armar un ticket ya existente, y de esa forma duplicarlo*/
if (isset($limpio->duplicar_ticket)) {
    if (isset($_POST['horarios']['horarios'])) {
        $id_ticket = intval($limpio->duplicar_ticket);
        $sql = ' select * '
                . 'from items_tickets '
                . "where id_ticket='$id_ticket' "
                . 'group by numero,id_loteria '
                . 'order by id_item_ticket desc';
        $items = $cbd->get_results($sql);
        if ($items) {
            foreach ($_POST['horarios']['horarios'] as $horario) {
                foreach ($items as $campo => $valor) {
                    $id_loteria = limpiador::clear_data($horario['id_loteria']);
                    if ($id_loteria == $valor->id_loteria) {
                        $loteria    = limpiador::clear_data($horario['loteria']);
                        $id_horario = limpiador::clear_data($horario['id_horario']);
                        $hora       = limpiador::clear_data($horario['hora']);
                        $apuestas->set_numero($valor->numero);
                        $apuestas->set_id_horario($id_horario);
                        $apuestas->set_id_loteria($id_loteria);
                        $perfil_apuesta = $apuestas->perfil_apuesta();
                        $datos [] = [
                            'numero'        => $valor->numero,
                            'alias'         => $valor->alias,
                            'loteria'       => $valor->loteria,
                            'id_loteria'    => $id_loteria,
                            'hora'          => $hora,
                            'id_horario'    => $id_horario,
                            'monto'         => $valor->monto,
                            'saldo'         => format_money($apuestas->saldo_numero(), locale_money, char_remove_money),
                            'monto_minimo'  => $perfil_apuesta->apuesta_minima
                        ];
                    }
                }
            }
        }
    }
    /*la cabecera http json*/
    header_json();
    /*escribo los datos en json*/
    echo json_encode($datos);
}

/*Los horarios posibles de un ticket dado para duplicar*/
if (isset($limpio->get_horarios_from_ticket)) {
    $id_ticket = intval($limpio->get_horarios_from_ticket);
    $config = $auth->get_conf();
    $sql = 'select id_horario,hora,loteria,'
            .'loterias.id_loteria '
            .'from horas_sorteos '
            .'inner join horarios on(horarios.id_hora_sorteo=horas_sorteos.id_hora_sorteo) '
            .'inner join loterias on(loterias.id_loteria=horarios.id_loteria) '
            .'where horarios.id_loteria in ( '
            ."  select distinct id_loteria from items_tickets where id_ticket='$id_ticket' "
            .') '
            ."and  hora > strftime('%H:%M',datetime((strftime('%s','now')+".$config->final_horario."),'unixepoch','localtime')) "
            .'order by loteria,hora asc';
    $consulta = $cbd->get_results($sql);
    if ($consulta) {
        foreach ($consulta as $valor) {
            $datos[] = [
                'id_horario' => $valor->id_horario,
                'hora'       => $valor->hora,
                'id_loteria' => $valor->id_loteria,
                'loteria'    => strtoupper(substr($valor->loteria, 0, 8))
            ];
        }
    }
    /*la cabecera http json*/
    header_json();
    /*escribo los datos en json*/
    echo json_encode($datos);
}

