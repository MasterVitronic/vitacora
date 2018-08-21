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

class auth {

    private $cbd;
    private $guachi;
    private $usuario;
    private $contrasena;
    private $id_perfil;
    private $id_usuario;


    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    /**
     * __construct
     *
     * Constructor de la clase
     *
     * @access public
     *
     */
    public function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
        global $cbd;
        $this->cbd = $cbd;
        $this->guachi = guachi::iniciar();
    }

    /**
     * __destruct
     *
     * Destructor, destruye automaticamente la clase.
     *
     * @access public
     */
    public function __destruct() {
        unset($this->cbd);
    }

    /**
     * Inicia la instancia de la clase
     * @return object
     */
    public static function iniciar() {
        if (!self::$instancia instanceof self) {
            self::$instancia = new self;
        }
        return self::$instancia;
    }

    /**
     * Método magico __clone
     */
    public function __clone() {
        trigger_error('Operación Invalida:' .
                ' clonación no permitida', E_USER_ERROR);
    }

    /**
     * Método magico __wakeup
     */
    public function __wakeup() {
        trigger_error('Operación Invalida:' .
                ' deserializar no esta permitido ' .
                get_class($this) . " Class. ", E_USER_ERROR);
    }
  
    /**
     * metodo llave
     *
     * @access private
     */
    private function llave($modo) {
        switch ($modo) {
            case 'cerrar':
                return (session_destroy()) ? true : false;
            case 'entrar':
                $_SESSION = [
                    'usuario'       => $this->usuario,
                    'token_sesion'  => $this->guachi->g_crc(),
                    'id_perfil'     => $this->id_perfil,
                    'id_usuario'    => $this->id_usuario
                ];
                $this->guachi->g_mensaje('ok');
                return true;
        }
        return false;
    }

    /**
     * metodo autenticacion
     *
     * @access public
     */
    public function entrar($usuario, $contrasena) {
        $this->contrasena = hash('whirlpool', $contrasena . $usuario . salt);
        $sql =  'select id_usuario,estatus,id_perfil'
                . " from auth.usuarios where usuario='$usuario'"
                . " and contrasena='$this->contrasena'";
        $consulta = $this->cbd->get_row($sql);
        if (isset($consulta->id_usuario)) {
            if ($consulta->estatus === 'f') {
                $this->guachi->g_mensaje(gettext('Acceso revocado'), 'contrasena');
                return;
            }
            $this->usuario      = $usuario;
            $this->id_usuario   = $consulta->id_usuario;
            $this->id_perfil    = $consulta->id_perfil;
            $this->llave('entrar');
        } else {
            $this->guachi->g_mensaje(gettext('Acceso negado'), 'contrasena');
            return;
        }
    }

    /**
     * metodo salir
     *
     * @access public
     */
    public function salir() {
        return $this->llave('cerrar') == true ? true : false;
    }

    /**
     * metodo mis_permisos
     *
     * @access public
     */
    public function permisos($id_submodulo) {
        $sql = 'select       auth.perfiles.id_perfil,'
                .'           auth.permisos.lectura,'
                .'           auth.permisos.escritura,'
                .'           auth.permisos.borrado '
                .'from       auth.usuarios '
                .'inner join auth.horarios   on(auth.horarios.id_horario in (select unnest(auth.perfiles.ids_horarios))) '
                .'inner join auth.perfiles   on(auth.perfiles.id_perfil=auth.usuarios.id_perfil) '
                .'inner join auth.permisos   on(auth.permisos.id_perfil=auth.perfiles.id_perfil) '
                .'inner join auth.submodulos on(auth.submodulos.id_submodulo=auth.permisos.id_submodulo) '
                .'    where  auth.usuarios.estatus=true'
                .'    and    auth.perfiles.estatus=true'
                .'    and    auth.horarios.estatus=true'
                ."    and    auth.submodulos.id_submodulo='$id_submodulo'"
                ."    and    auth.usuarios.id_usuario='".$this->id_usuario()."'"
                ."    and '".strftime('%H:%M',time())."' between auth.horarios.desde and auth.horarios.hasta "
                ."limit 1 ";
        $consulta = $this->cbd->get_row($sql);
        return (object)[
          'permiso'     =>  isset($consulta->id_perfil)                                  ? true : false,
          'lectura'     => (isset($consulta->lectura)   and $consulta->lectura   == 't') ? true : false,
          'escritura'   => (isset($consulta->escritura) and $consulta->escritura == 't') ? true : false,
          'borrado'     => (isset($consulta->borrado)   and $consulta->borrado   == 't') ? true : false
        ];
    }
    
    /**
     * metodo id_usuario
     *
     * @access public
     */
    public function id_usuario() {
        return isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : false;
    }
    
    /**
     * metodo is_logged
     *
     * @access public
     */
    public function is_logged() {
       return isset($_SESSION['id_usuario']);
    }
    
    /**
     * metodo info_usuario
     *
     * @access public
     */
    public function info_usuario() {
        if ( $this->is_logged() ) {
            return (object) [
                'usuario'    => $_SESSION['usuario'],
                'id_perfil'  => intval($_SESSION['id_perfil']),
                'id_usuario' => intval($_SESSION['id_usuario'])
            ];
        }
        return false;
    }

}
