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
class article {

    private $cbd;
    private $guachi;
    private $controlador;
    private $get;
    private $limpiador;
    private $limit;
    private $perpage = 3;
    public  $Warnings = [] ;
    private $dirTheme = 'admin'. DS . admin_theme . DS ;
    public  $request = [];
    private $postId = false;
    private $id_user ;
    public  $dataToSave = [] ;
    private $sql;
    public  $action = false;
    
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
        $this->guachi       = guachi::iniciar();
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
     * sql_insert
     *
     * Crea la sentencia insert SQL a partir de un array asociativo
     *
     * @access public
     */
    private function sql_insert($tabla) {
        $datos = $this->dataToSave;
        $sql = "insert into $tabla";
        $sql .= '(' . implode(',', array_keys($datos)) . ')';
        $sql .= "values('" . implode("','", $datos) . "')";
        $this->sql = $sql;
    }

    /**
     * sql_update
     *
     * Crea la sentencia update SQL a partir de un array asociativo
     *
     * @access public
     */
    private function sql_update($tabla,$index,$id) {
        foreach ($this->dataToSave as $campo => $valor) {
            $values[] = "$campo='$valor'";
        }
        $sql = "update $tabla set ";
        $sql .= implode(",", $values);
        $sql .= " where $index='$id'";
        $this->sql = $sql;
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
            'title'       => 'Titulo no debe ser nulo.',
            'articleBody' => 'Articulo no debe ser nulo',
            'url'         => 'Url no debe ser nulo',
        ];
        $checkExists = [
            'url'         => 'La Url ya esta registrada'
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
     * Método html2md
     *
     *
     * Convierte un string HTML en Markdown
     */
    public function html2md($str) {
        require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'Converter.php');
        require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'ConverterExtra.php');
        require_once(ROOT . 'lib'. DS . 'Markdownify' . DS . 'Parser.php');
        //$Markdownify      = new Markdownify\Converter;
        $Markdownify      = new Markdownify\ConverterExtra;
        return            $Markdownify->parseString($str);
    }

    /**
     * Método parser
     *
     *
     * Si encuenta codigo lo colorea con geshi
     */
    private function parser($s) {       
        require_once(ROOT . 'lib'. DS . 'Geshi'. DS .'geshi.php');
        require_once(ROOT . 'lib' . DS . 'class.parsedown.php');
        $offset = 0;
        $result = '';
        // Divido el texto del codigo
        $n = preg_match_all('|((\r?\n```+)\s*([a-zA-Z0-9_-]*)\r?\n)(.*?)\2\r?\n|s', $s, $matches, PREG_OFFSET_CAPTURE);
        for($i = 0; $i < $n; $i++) {
            $md = substr($s, $offset, $matches[4][$i][1] - $offset - strlen($matches[1][$i][0]));
            $result .= $md ;
            $code = html_entity_decode(trim($matches[4][$i][0]));
            $language = strtolower($matches[3][$i][0]);
            $lang = ($language) ? $language : 'text' ;
            $geshi = new GeSHi($code,  $lang );
            $geshi->enable_classes();
            $geshi->set_link_target('_blank');          
            $geshi->set_case_keywords(GESHI_CAPS_LOWER);
            $result .= $geshi->parse_code();
            $offset = $matches[4][$i][1] + strlen($matches[4][$i][0]) + strlen($matches[2][$i][0]);
        }
        $result .= substr($s, $offset) ; 
        $md2html = new ParsedownExtra();
        return $md2html->text($result);
    }

    /**
     * Método saveTaxonomy
     *
     * 
     */
    public function saveTaxonomy($data,$id_post,$taxonomy) {
        /*result siempre es true a menos que algo salga mal*/
        $result = true;
        if($taxonomy === 'tags'){
            $table  = 'posts_tagged';
            $id     = 'id_tag';
        }elseif($taxonomy === 'category'){
            $table  = 'posts_categories';
            $id     = 'id_category';
        }else{
            return false;
        }
        /*lo primero es iniciar la transaccion*/
        $this->cbd->beginTransaction();
        /*elimino cualquier refeencia anterior*/
        $this->sql = "delete from $table where id_post='$id_post'";
        if($this->cbd->exec($this->sql) === false){
            $result = false;
        }
        if($result === true){
            if (is_array($data)) {
                foreach ($data as $campo => $valor) {
                    $this->sql = "insert into $table($id,id_post)values('$valor','$id_post')";
                    if(is_numeric($valor)){
                        if($this->cbd->exec($this->sql) === false){
                            $result = false;
                            break;
                        }
                    }
                }
            }
        }
        if($result === true){
            $this->cbd->commit();
        }else{
            $this->cbd->rollBack();
        }
        return $result;
    }

    /**
     * Método saveTags
     *
     * Guarda las tags
     */
    public function saveTags($data,$id_post) {
        return $this->saveTaxonomy($data,$id_post,'tags');
    }

    /**
     * Método saveCategories
     *
     * Guarda las categorias
     */
    public function saveCategories($data,$id_post) {
        return $this->saveTaxonomy($data,$id_post,'category');
    }

    /**
     * Método getTags
     *
     * Retorna las etiquetas 
     */
    private function getTags($newPost = false) {
        $id_post = ($this->request->id) ? $this->request->id : '0';
        $this->sql = "select tags.id_tag,tag,id_post from tags "
                ."left outer join posts_tagged on (posts_tagged.id_tag=tags.id_tag) "
                ."where 1=1 or id_post='$id_post' "
                ."group by tags.id_tag order by tags.id_tag desc ";
        $results = $this->cbd->get_results($this->sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $selected = ($valor->id_post == $id_post) ? 'selected' : '';
                if(is_array($newPost)){
                    $selected = in_array($valor->id_tag,$newPost) ? 'selected' : '';
                }
                $data[]=[
                     'tag' => '<option '.$selected.' value="'.$valor->id_tag.'">'.$valor->tag.'</option>'
                ];
            }
            return $data;
        }
        return false;        
    }

    /**
     * Método getCategories
     *
     * Retorna las categorias de el posts
     */
    public function getCategories($newPost = false) {
        $id_post = ($this->request->id) ? $this->request->id : '0';
        $this->sql = "select categories.id_category,category,id_post from categories "
                ."left outer join posts_categories on (posts_categories.id_category=categories.id_category) "
                ."where 1=1 or id_post='$id_post' "
                ."group by categories.id_category order by categories.id_category desc";
        $results = $this->cbd->get_results($this->sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $selected = ($valor->id_post == $id_post) ? 'selected' : '';
                if(is_array($newPost)){
                    $selected = in_array($valor->id_category,$newPost) ? 'selected' : '';
                }
                $data[]=[
                     'category' => '<option '.$selected.' value="'.$valor->id_category.'">'.$valor->category.'</option>'
                ];
            }
            return $data;
        }
        return false;
    }

    /**
     * Método getFormatMd
     *
     * Retorna la opcion de formatear o no el  SRC MD segun sea el caso
     */
    private function getFormatMd() {
        $request = $this->request->mode ;
        $si = '';
        $no = '';
        if ( $request === 'new' ) {
            $si = 'selected';
        }else{
            $no = 'selected';
        }        
        $data=[
             ['normaliceMd' => '<option '.$si.' value="t">SI</option>'],
             ['normaliceMd' => '<option '.$no.' value="f">NO</option>']
        ];
        return $data;
    }

    /**
     * Método getPost
     *
     *
     * Retorna un articulo para editar
     */
    private function getPost() {
        if ( $this->request->mode ) {
            $id_post = $this->request->id ;
            $this->sql = "select * from posts "
                  ."where posts.id_post='$id_post'";
            $result = $this->cbd->get_row($this->sql);
            if ( $result ) {
                $data = [
                    'id_post'       => $result->id_post,
                    'url'           => $result->url,
                    'title'         => $result->title,
                    'description'   => $result->description,
                    'articleBody'   => $result->articleSrc,
                    'tags'          => $this->getTags(),
                    'categories'    => $this->getCategories(),
                    'normaliceMd'   => $this->getFormatMd(),
                    'warnings'      => $this->Warnings
                ];
                return $data;
            }
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
     * Método postExist
     *
     * Retorna false si un posts no existe
     */  
    public function postExist() {
        $id_post = $this->request->id ;
        if( $id_post ){
            $this->sql    = "select id_post from posts where id_post='$id_post'";
            $result = $this->cbd->get_var($this->sql);
            if($result) {
                $this->postId = $result;
                return true;
            }
        }
        $this->postId = false;
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
     * Método getTemplate
     *
     *
     * Retorna el template a usar
     */    
    public function getTemplate() {
        if ( $this->get->arg === 'article' ) {
            $this->setRequest();
            $request = $this->request->mode ;
            if ( $request ) {
                if ( $request === 'new' or $request === 'edit' ) {
                    if ($request === 'edit' and !$this->postExist() ) {
                        return $this->notFound();
                    }elseif($request === 'new' and  $this->request->id ){
                        return $this->notFound();
                    }
                    return $this->dirTheme . 'article/form';
                }elseif ( $request === 'list' ) {
                    return $this->dirTheme . 'article/list';
                }else{
                    return $this->notFound();
                }
            }
            return $this->dirTheme . 'article/list';
        }
    }

    /**
     * Método savePost
     *
     * Guarda la publicacion
     */
    public function savePost($data,$id_post = false) {
        $this->id_user = 1;
        $this->dataToSave = [
            'title'         => $data->title,
            'url'           => trim(str_replace(' ', '',strtolower($data->url))),
            'articleSrc'    => $data->articleSrc,
            'articleBody'   => $this->parser($data->articleSrc),
            'wordCount'     => $this->wordCount($data->articleBody),
            'id_post'       => $data->id_post,
            'dateModified'  => date("Y-m-d H:i:s")
        ];
        /*lo primero es iniciar la transaccion*/
        $this->cbd->beginTransaction();
        /*result siempre es true a menos que algo salga mal*/
        $result = true;
        if(is_numeric($id_post)){
            $this->sql_update('posts','id_post',$id_post);
        }else{
            unset($this->dataToSave['id_post']);
            $this->dataToSave=$this->dataToSave + ['id_user' => $this->id_user];
            $this->sql_insert('posts');
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
     * Método getContent
     *
     *
     * Retorna un articulo para editar
     */
    public function getContent() {
        $request = $this->request->mode ;
        $dataEdit = $this->dataToSave + [
            'tags'       => $this->getTags(isset($_POST['tags']) ? $_POST['tags'] : false),
            'categories' => $this->getCategories(isset($_POST['categories']) ? $_POST['categories'] : false),
            'warnings'   => $this->Warnings
        ];
        if ( $request === 'new' or $request    === 'edit' ){
            if($this->request->id and $request === 'edit' ){
                if( isset($this->dataToSave['id_post']) ){
                    return $dataEdit;
                }
                return $this->getPost();
            }elseif($request === 'new'){
                return $dataEdit + ['normaliceMd' => $this->getFormatMd()];
            }
        }
        return $this->getContentPagination();
    }

    /**
     * Método toggleDraft
     *
     * togglea el status de post
     */ 
    public function toggleDraft($id_post){
        $this->sql  = "select status from posts where id_post='$id_post'";
        $status     = $this->cbd->get_var($this->sql);        
        $this->dataToSave = [
            'status'=> ($status === 't') ? 'f' : 't'
        ];
        /*lo primero es iniciar la transaccion*/
        $this->cbd->beginTransaction();
        /*result siempre es true a menos que algo salga mal*/
        $result = true;
        if(is_numeric($id_post)){
            $this->sql_update('posts','id_post',$id_post);
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
     * Método getDescription
     *
     * Retorna la descripcion del post, recortando el texto total
     */ 
    private function getDescription($text, $length = 590){
        $text = strip_tags($text);
        if(strlen($text) > $length) {
            $text = substr($text, 0, strpos($text, ' ', $length)) .' ...' ;
        }
        return $text;
    }

    /**
     * Método getFirstImage
     *
     * Retorna la primera imagen de un string HTML
     */ 
    private function getFirstImage($str){
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $str, $matches);
        return isset($matches[1][0])?$matches[1][0]:'/img/default.png';
    }

    /**
     * Método CommentCount
     *
     *
     * Retorna la cantidad de comentarios de un post
     */
    public function CommentCount( $id_post ) {
        $this->sql = "select count(id_post) from comments where id_post='$id_post' ";
        $result = $this->cbd->get_var($this->sql);
        $msg = ' comentario' . ($result == 1 ? '' : 's');
        return ($result) ? $result . $msg : 'sin comentarios';
    }

    /**
     * Método getContentPagination
     *
     * Retorna el contenido para paginar
     */
    public function getContentPagination() {
        //$currentPage   = ($this->isPage()) ? $this->isPage() : 1 ;
        //$offset = ( ( $currentPage - 1 ) * $this->perpage );
        $this->sql = "select posts.* from posts "
              ."where 1=1 "
              ."order by posts.id_post desc ";
              //."limit $this->perpage offset $offset";
        $results = $this->cbd->get_results($this->sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $data['pages'][] = [
                        'id_post'       => $valor->id_post,
                        'comments'      => $this->CommentCount($valor->id_post),
                        'url'           => $valor->url,
                        'title'         => $valor->title,
                        'articleBody'   => $this->getDescription($valor->articleBody),
                        'image'         => $this->getFirstImage($valor->articleBody),
                        'humanDate'     => $this->humanDate($valor->datePublished),
                        'dateModified'  => date("c",strtotime($valor->dateModified)),
                        'toogle_icon'   => ($valor->status === 'f') ? 'btn-primary fa fa-plus-square' : 'btn-error fa fa-minus-square'
                ];
            }
            //$totalPages    = $this->getTotalPages();
            //$prevPage      = ($currentPage > 1) ? ($currentPage - 1) : '';
            //$nextPage      = ($currentPage < $totalPages) ? ($currentPage + 1) : '';          
            //$data = $data + [
                        //'totalPages'    => $totalPages,
                        //'currentPage'   => $currentPage,
                        //'prevPage'      => $prevPage,
                        //'nextPage'      => $nextPage
            //];
            return $data;
        }
        return false;
    }

    /**
     * Método wordCount
     *
     *
     * Cuenta el numero de palabras en u post
     */
    public function wordCount( $str ) {
        return str_word_count(strip_tags($str));
    }

    /**
     * Método humanDate
     *
     *
     * Retorna la fecha en formato humano
     */    
    public function humanDate($datetime) {
        $date = spanishdate($datetime);
        return sprintf('%d de %s %d %s', $date->fecha, $date->mes,$date->anio, $date->hora);
    }
}





















