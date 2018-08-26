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


/*camelCase  :-) */
class taxonomy {

    private $cbd;
    private $controlador;
    private $get;
    private $limpiador;
    public  $Warnings = [] ;
    private $dirTheme = 'admin'. DS . admin_theme . DS ;
    public  $request = [];
    public  $dataToSave = [] ;
    private $sql;
    
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
        $this->cbd          = $cbd;
        $this->controlador  = controlador::iniciar();
        $this->limpiador    = limpiador::iniciar();
        $this->get          = $this->limpiador->recolectar($this->controlador->get_modulo(),tipo_db,true);
        $this->limit        = '';
    }

    /**
     * __destruct
     *
     * Destructor, destruye automaticamente la clase.
     *
     * @access public
     */
    public function __destruct() {
        
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
     * Método Warnings
     *
     * setea mensajes de error a mostrar luego de una peticion POST
     */
    public function setWarning($errs) {
        array_push($this->Warnings,['warning' => $errs]);
    }

    /**
     * Método setDefaultWarning
     *
     * Setea los mensajes por default segun sea el caso
     */
    public function setDefaultWarning($data,$mode) {
        $checkNull = [
            'type'        => 'Seleccione un tipo.',
            'name'        => 'Un nombre para la taxonomia es requerido '
        ];
        $checkExists = [
            'tag'         => 'Etiqueta registrada',
            'category'    => 'Categoria registrada'
        ];
        if ($mode == 'null') {
            foreach ( $checkNull as $campo => $valor ) {
                if($data == $campo){
                    $this->setWarning($valor);
                }
            }
        }elseif($mode == 'exist'){
            foreach ( $checkExists as $campo => $valor ) {
                if($data->campo == $campo ){
                    $this->setWarning($valor);
                }
            }            
        }
    }

    /**
     * Método getTags
     *
     * Retorna todas las etiquetas 
     */
    private function getTags() {
        $this->sql = "select id_tag,tag from tags "
                ."order by id_tag desc";
        $results = $this->cbd->get_results($this->sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $data[]=[
                     'id_tag'   => $valor->id_tag,
                     'tag'      => $valor->tag
                ];
            }
            return $data;
        }
        return false;        
    }

    /**
     * Método getCategories
     *
     * Retorna todas las categorias
     */
    private function getCategories() {
        $this->sql = "select id_category,category from categories "
                ."order by id_category desc";
        $results = $this->cbd->get_results($this->sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $data[]=[
                     'id_category'  => $valor->id_category,
                     'category'     => $valor->category
                ];
            }
            return $data;
        }
        return false;
    }

    /**
     * Método setRequest
     *
     * setea el request
     */ 
    public function setRequest() {
        $request = isset($this->get->arg1) ? $this->get->arg1 : false;
        if( isset($request) ){
            $this->request = (object)[
                'mode' => $request,
                'id'   => isset($this->get->arg2) ? $this->get->arg2 : false
            ];
        }
        return false;
    }

    /**
     * Método notFound
     *
     *
     * Retorna 404 y el template correspondiente
     */    
    public function notFound() {
        /*el header 404*/
        header("HTTP/1.0 404 Not Found");
        return $this->dirTheme . '404';       
    }

    /**
     * Método Exist
     *
     *
     * Verifica si una taxonomia existe
     */
    private function Exist() {
        $request = $this->request->mode ;
        if ( $request === 'categories' or $request === 'tags' ) {
            $id = ($request === 'tags') ? 'id_tag' : 'id_category';
            $this->sql = "select $id from $request where $id='".$this->request->id."'";
            if($this->cbd->get_var($this->sql)){
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Método getTemplate
     *
     *
     * Retorna el template a usar
     */    
    public function getTemplate() {
        if ( $this->get->arg === 'taxonomy' ) {
            $this->setRequest();
            $request = $this->request->mode ;
            if ( $request ) {
                if ( $request === 'categories' or $request === 'tags' ) {
                    if($this->Exist($this->request->id)){
                        return $this->dirTheme . 'taxonomy/taxonomy';
                    }else{
                        return $this->notFound();
                    }
                }else{
                    return $this->notFound();
                }
            }
            return $this->dirTheme . 'taxonomy/taxonomy';
        }
    }

    /**
     * Método getTypes
     *
     *
     * Retorna los tipos de taxonomia 
     */
    private function getTypes() {
        $request = $this->request->mode ;
        $Tag      = ($request === 'tags')?'selected':'';
        $category = ($request === 'categories')?'selected':'';
        if(!$request){
           if( isset($this->dataToSave['type']) ){
                if ($this->dataToSave['type'] == 'tags') {
                    $Tag      = 'selected';
                }else{
                    $category = 'selected';
                }
           }
        }
        $result['types']=[
            ['option'    => '<option '.$category.' value="categories">Categoria</option>'],
            ['option'    => '<option '.$Tag.' value="tags">Etiqueta</option>']
        ];
        return $result;
    }

    /**
     * Método saveTaxonomy
     *
     * Guarda la taxonomia en la db
     */
    public function saveTaxonomy($table,$value,$mode = false) {
        $campo  = ($table === 'tags') ?  'tag' : 'category';
        /*result siempre es true a menos que algo salga mal*/
        $result = true;
        /*lo primero es iniciar la transaccion*/
        $this->cbd->beginTransaction();
        if($mode === 'update'){
            $id        = ($this->request->mode==='tags') ? 'id_tag':'id_category';
            $id_value  = $this->request->id;
            $this->sql = "update $table set $campo='$value' where $id = '$id_value' ";
        }else{
            $this->sql = "insert into $table ($campo) values('$value')";
        }
        if($this->cbd->exec($this->sql) === false){
            $result = false;
        }
        if($result === true){
            $this->cbd->commit();
        }else{
            $this->cbd->rollBack();
        }
        return $result;
    }

    /**
     * Método getTaxonomies
     *
     *
     * Retorna todas las data para renderizar el modulo
     */
    private function getTaxonomies() {
        $data  = [];
        $table = $this->request->mode ;
        $campo = ($this->request->mode==='tags') ? 'tag'   :'category';
        $id    = ($this->request->mode==='tags') ? 'id_tag':'id_category';
        if(is_numeric($this->request->id)){
            $this->sql = "select $campo from $table "
                        ."where $id = '".$this->request->id."'";
            $result = $this->cbd->get_var($this->sql);
            $data   = [
                'name' => $result
            ];
        }
        $result = $data + [
            'tags'          => $this->getTags(),
            'categories'    => $this->getCategories()
        ];
        return $result;
    }

    /**
     * Método getContent
     *
     *
     * Retorna un articulo para editar
     */
    public function getContent() {
        $dataEdit = $this->dataToSave + [
            'warnings'   => $this->Warnings
        ];
        return $dataEdit + $this->getTaxonomies() + $this->getTypes();
    }
}





