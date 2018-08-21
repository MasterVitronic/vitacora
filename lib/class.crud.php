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

class crud {

    private $cbd;
    private $sql;
    public $datos = [];
    public $tabla; /* nombre de la tabla */
    private $check;
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
        global $cbd;
        $this->cbd = $cbd;
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
        trigger_error("Operación Invalida:" .
                " clonación no permitida", E_USER_ERROR);
    }

    /**
     * Método magico __wakeup
     */
    public function __wakeup() {
        trigger_error("Operación Invalida:" .
                " deserializar no esta permitido " .
                get_class($this) . " Class. ", E_USER_ERROR);
    }

    /**
     * Establece una variable para ser usada en esta clase
     *
     * @access public
     */
    public function establece($variable, $valor) {
        $this->$variable = $valor;
    }

    /**
     * datos
     *
     * Setea el array a guardar
     *
     * @entrada array(asociativo)
     * @salida objeto
     * @error -
     * @access public
     */
    public function datos($datos) {
        $this->datos = $datos;
    }

    /**
     * reemplaza
     *
     * reemplaza los campos de un array a partir de otro array
     *
     * @entrada (object)array(asociativo),array(asociativo)
     * @salida array() modificado
     * @error false
     * @access public
     */
    /* public function reemplaza($datos, $modificar) {
      if (is_array($datos) and is_array($modificar)) {
      $this->datos = array_replace($datos, $modificar);
      }
      } */
    public function reemplaza($modificar) {
        if ($this->datos and is_array($modificar)) {
            $this->datos = array_replace($this->datos, $modificar);
        }
    }

    /**
     * remover()
     *
     * Elimina, de un array los campos declarados
     *
     *
     *
     * @entrada array(asociativo),array(simple)
     * @salida  -
     * @error   -
     */
    public function remover($no_incluir) {
        if (isset($this->datos) and isset($no_incluir)) {
            foreach ($no_incluir as $campo => $valor) {
                if (is_object($this->datos)) {
                    unset($this->datos->$valor);
                }
            }
            $this->datos = $this->datos;
        }
    }
    /**
     * check
     *
     * Verificaciones de sanidad en la base de datos
     *
     *
     * @error false
     * @access public
     */
    public function check($condicion, $verificar, $tabla = false,$id_campo = false,$id = false) {
        $update = false;
        if ($id) {
            $update = " and $id_campo!='$id'";
        }
        switch ($condicion) {
            case 'existe':
                if ($verificar) {
                    foreach ($verificar as $campo => $valor) {
                        if ($this->cbd->get_var("select $valor from $tabla where $valor='" . $this->datos->$valor . "' $update")) {
                            return $this->check = (object)['campo'=>$valor,'valor'=>$this->datos->$valor];
                        }
                    }
                    return $this->check = false;
                }
                break;
            case 'nulo':
                if (is_array($verificar)) {
                    foreach ($verificar as $campo => $valor) {
                        if (empty($this->datos->$valor)) {
                            return $this->check = $valor;
                        }
                    }
                    return $this->check = false;
                }
                break;
        }
    }

    /**
     * crear_insert
     *
     * Crea la sentencia insert SQL a partir de un array asociativo
     *
     * @access public
     */
    public function crear_insert($tabla) {
        $datos = $this->datos;
        if(is_object($this->datos)){
             $datos = get_object_vars($this->datos);
        }
        $sql = "insert into $tabla";
        $sql .= '(' . implode(',', array_keys($datos)) . ')';
        $sql .= "values('" . implode("','", $datos) . "')";
        $this->sql = $sql;
    }
    /**
     * crear_update
     *
     * Crea la sentencia update SQL a partir de un array asociativo
     *
     * @access public
     */
    public function crear_update($tabla,$id_campo,$id) {
        foreach ($this->datos as $campo => $valor) {
            //$values[] = "$campo='$valor'";
            if($valor == 'default'){
                $values[] = "$campo=default";
            }else{
                $values[] = "$campo='$valor'";   
            }            
        }
        $sql = "update $tabla set ";
        $sql .= implode(",", $values);
        $sql .= " where $id_campo='$id'";
        $this->sql = $sql;
    }
    /**
     * crear_delete
     *
     * Crea la sentencia delete SQL a partir de la tabla y el id
     *
     * @access public
     */
    public function crear_delete($tabla,$id_campo,$id) {
        $sql = "delete from $tabla where $id_campo='$id'";
        $this->sql = $sql;
    }
    /**
     * show_sql
     *
     * Imprime en pantalla la sentencia SQL, para efectos de depuración
     *
     * @access public
     */
    public function show_sql() {
        return $this->sql;
    }

    /**
     * guardar
     *
     * Ejecuta la sentencia previamente creada con crear_insert
     *
     * @access public
     */
    public function query() {
        if (!$this->cbd->query($this->sql)) {
            return false;
        }
        return true;
    }
    /**
     * exec
     *
     * Ejecuta la sentencia previamente creada con crear_insert
     *
     * @access public
     */
    public function exec() {
        return $this->cbd->exec($this->sql);
    }    
    /**
     * metodo last_id
     *
     * retorna el id de la ultima fila afectada
     *
     * @access public
     */
    public function last_id($desc) {
            return $this->cbd->get_var("select currval('$desc')");
    }

}
