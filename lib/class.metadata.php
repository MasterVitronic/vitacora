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
class metadata {

    private $socialMeta;
    private $schema;
    private $schemaInfo;
    private $schemaTypes = [];
    private $socialCards = [];
    private $css         = [];
    private $author;
    private $canonical;
    private $title;
    private $description;
    private $meta;
    private $lang;
    private $google_verification;
    private $pagetype;
    private $image;
    private $twiter;
    private $siteName;
    private $publishedTime;
    private $modifiedTime;
    
    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    private function __construct() {

    }

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
     * setCanonicalUrl
     *
     * setea el la url canonica
     *
     * @access public
     */
    public function setCanonicalUrl($url) {
        $this->canonical = $url;
    }

    /**
     * setGoogleToken
     *
     * setea el token de google
     *
     * @access public
     */
    public function setGoogleToken($token) {
        $this->google_verification = $token;
    }

    /**
     * setAuthor
     *
     * setea el autor
     *
     * @access public
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * setModifiedTime
     *
     * setea el datetime de modificacion de articulo
     *
     * @access public
     */
    public function setModifiedTime($datetime) {
        $this->modifiedTime = $datetime;
    }

    /**
     * setPublishedTime
     *
     * setea el datetime de la publicacion
     *
     * @access public
     */
    public function setPublishedTime($datetime) {
        $this->publishedTime = $datetime;
    }

    /**
     * addCss
     *
     * Añade los css a usar en el metadata
     *
     * @access public
     */
    public function addCss($css) {
        array_push($this->css,['css' => $css]);
    }

    /**
     * setTypeSchema
     *
     * setea el/los tipo(s) de schema(s)
     *
     * @access public
     */
    public function addTypeSchema($type) {
        array_push($this->schemaTypes,['type' => $type]);
    }

    /**
     * addSocialCard
     *
     * setea  el/los tipo(s) de tarjeta(s) social(les)
     *
     * @access public
     */
    public function addSocialCard($type) {
        array_push($this->socialCards,['type' => $type]);
    }

    /**
     * setTitle
     *
     * setea el titulo
     *
     * @access public
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * setSiteName
     *
     * El nombre del sitio
     *
     * @access public
     */
    public function setSiteName($name) {
        $this->siteName = $name;
    }

    /**
     * setDescription
     *
     * setea la descriccion
     *
     * @access public
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * setDescription
     *
     * setea el lenguaje
     *
     * @access public
     */
    public function setLang($lang) {
        $this->lang = $lang;
    }

    /**
     * setPageType
     *
     * setea el tipo de pagina 
     *
     * @access public
     */
    public function setPageType($type) {
        $this->pagetype = $type;
    }

    /**
     * setImage
     *
     * setea la imagen para las tarjetas sociales
     *
     * @access public
     */
    public function setImage($url) {
        $this->image = $url;
    }

    /**
     * setImage
     *
     * setea la imagen para las tarjetas sociales
     *
     * @access public
     */
    public function setTwiter($username) {
        $this->twiter = $username;
    }

    /**
     * getSocialNetworks
     *
     * retorna las redes sociales
     *
     * @access private
     * @return array
     */
    private function getSocialNetworks() {
        /*@TODO, completar esto con las redes que faltan*/
        $socialNet = ['Facebook','Linkedin','Twiter','Youtube','Google'];
        $data = [];
        foreach ($socialNet as $valor) {
            if( $this->schemaInfo['Person'][$valor] ){
                array_push($data, $this->schemaInfo['Person'][$valor]);
            }
        }
        if($data){
            return ["sameAs" => $data];
        }
        return false;
    }

    /**
     * setPersonSchema
     *
     * retorna el schema de una persona
     *
     * @access private
     * @return array
     */
    private function getPersonSchema() {
        $personSchema=[
            "@context"  => "http://schema.org/",
            "@type"     => "Person",
            "name"      => $this->schemaInfo['Person']['name'],
            "gender"    => $this->schemaInfo['Person']['gender'],
            "url"       => $this->schemaInfo['Person']['url'],
            "image"     => $this->schemaInfo['Person']['image'],
            "telephone" => $this->schemaInfo['Person']['telephone'],
            "email"     => $this->schemaInfo['Person']['email'],
            "jobTitle"  => $this->schemaInfo['Person']['jobTitle'],
            "address"   => [
                    "@type"           => "PostalAddress",
                    "streetAddress"   => $this->schemaInfo['Person']['streetAddress'],
                    "addressLocality" => $this->schemaInfo['Person']['addressLocality'],
                    "addressRegion"   => $this->schemaInfo['Person']['addressRegion'],
                    "addressCountry"  => $this->schemaInfo['Person']['addressCountry'],
                    "postalCode"      => $this->schemaInfo['Person']['postalCode']
                ]
        ];
        $social = $this->getSocialNetworks();
        if($social){
            $personSchema = $personSchema + $social;
        }
        return json_encode($personSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * getOrganizationSchema
     *
     * retorna el schema de una Organizacion
     *
     * @access private
     * @return array
     */
    private function getOrganizationSchema() {
        $organizationSchema=[
              "@context"        => "http://schema.org",
              "@type"           => "Organization",
              "name"            => $this->schemaInfo['Organization']['name'],
              "url"             => $this->schemaInfo['Organization']['url'],
              "logo"            => $this->schemaInfo['Organization']['logo'],
              "contactPoint"    => [
                  [
                    "@type"            => "ContactPoint",
                    "telephone"        => $this->schemaInfo['Organization']['telephone'],
                    "contactType"      => $this->schemaInfo['Organization']['contactType'],
                    "availableLanguage"=>$this->schemaInfo['Organization']['availableLanguage']
                  ]
              ]
        ];
        return json_encode($organizationSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);        
    }

    /**
     * getSiteSchema
     *
     * retorna el schema del sitio
     *
     * @access private
     * @return array
     */
    private function getSiteSchema() {
        $siteSchema=[
            "@context"  => "http://schema.org",
            "@type"     => "WebSite",
            "url"       => $this->schemaInfo['Site']['url']
        ];
        return json_encode($siteSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * initSchema
     *
     * Inicializa el schema.org
     *
     * @access private
     */
    private function initSchema() {
        $config = parse_ini_file(ROOT . DS . 'guachi.ini', true);
        $schema = $config['schemaOrg'];
        if($schema['status']){
            $this->schemaInfo = $schema;
        }
    }

    /**
     * setSchema
     *
     * setea el schema
     *
     * @access private
     */
    private function setSchema($schema) {
        switch ($schema){
            case 'Person':
                $this->schema = $this->getPersonSchema();
                break;
            case 'Organization':
                $this->schema = $this->getOrganizationSchema();
                break;
            case 'Site':
                $this->schema = $this->getSiteSchema();
                break;
        }
    }

    /**
     * getSchemas
     *
     * retorna el schema compuesto soicitado
     *
     * @access private
     * @return string
     */
    private function getSchemas() {
        $this->initSchema();
        $result = '';
        if($this->schemaTypes){
            foreach ($this->schemaTypes as $schema) {
                $this->setSchema($schema['type']);
                $result .= "\t\t".'<script type="application/ld+json">' . PHP_EOL
                            . $this->schema . PHP_EOL
                            ."\t\t" . '</script>'.PHP_EOL;
            }
        }
        return trim($result);
    }

    /**
     * getOpenGraphMeta
     *
     * retorna la metadata OpenGraph
     *
     * @access private
     * @return string
     * @TODO falta una funcion que retorne el ancho y alto de la imagen
     */
    private function getOpenGraphMeta() {
        $meta = '<!-- Open Graph data -->'.PHP_EOL
                ."\t\t" .'<meta property="og:title"        content="'.$this->title.'">' .PHP_EOL
                ."\t\t" .'<meta property="og:type"         content="'.$this->pagetype.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:url"          content="'.$this->canonical.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:description"  content="'.$this->description.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:site_name"    content="'.$this->siteName.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:locale"       content="'.$this->lang.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:updated_time" content="'.$this->modifiedTime.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:image"        content="'.$this->image.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:image:secure_url" content="'.$this->image.'">'.PHP_EOL
                ."\t\t" .'<meta property="og:image:width"  content="300">'.PHP_EOL
                ."\t\t" .'<meta property="og:image:height" content="300">';
        if($this->pagetype === 'article'){
             $meta .= PHP_EOL."\t\t" .'<meta property="article:published_time" content="'.$this->publishedTime.'">'.PHP_EOL
                ."\t\t" .'<meta property="article:modified_time"  content="'.$this->modifiedTime.'" />'.PHP_EOL
                ."\t\t" .'<meta property="article:publisher"      content="'.$this->schemaInfo['Person']['Facebook'].'">';
        }
        return $meta;
    }

    /**
     * getTwiterMeta
     *
     * retorna la metadata de Twiter
     *
     * @access private
     * @return string
     */
    private function getTwiterMeta() {
        $meta = '<!-- Twiter card -->'.PHP_EOL
                ."\t\t" .'<meta name="twitter:title"       content="'.$this->title.'"/>'.PHP_EOL
                ."\t\t" .'<meta name="twitter:description" content="'.$this->description.'"/>'.PHP_EOL
                ."\t\t" .'<meta name="twitter:card"        content="summary"/>'.PHP_EOL
                ."\t\t" .'<meta name="twitter:site"        content="'.$this->twiter.'"/>'.PHP_EOL
                ."\t\t" .'<meta name="twitter:image"       content="'.$this->image.'"/>'.PHP_EOL
                ."\t\t" .'<meta name="twitter:creator"     content="'.$this->twiter.'"/>';
        return $meta;
    }

    /**
     * getGoogleMeta
     *
     * retorna la metadata de google
     *
     * @access private
     * @return string
     */
    private function getGoogleMeta() {
        $meta =  '<!-- Google Authorship and Publisher Markup -->'.PHP_EOL
                 //."\t\t" .'<meta itemprop="name"            content="'.$this->title.'"/>'.PHP_EOL
                 //."\t\t" .'<meta itemprop="description"     content="'.$this->description.'"/>'.PHP_EOL
                 //."\t\t" .'<meta itemprop="image"           content="'.$this->image.'"/>'.PHP_EOL
                 ."\t\t" .'<link rel="author"               href="'.$this->schemaInfo['Person']['Google'].'/posts"/>'.PHP_EOL
                 ."\t\t" .'<link rel="publisher"            href="'.$this->schemaInfo['Person']['Google'].'"/>'.PHP_EOL

                 ."\t\t" .'<!-- Robots -->'.PHP_EOL
                 ."\t\t" .'<meta name="googlebot"           content="index,follow">'.PHP_EOL
                 ."\t\t" .'<meta name="robots"              content="index,follow">'.PHP_EOL
                 ."\t\t" .'<meta name="revisit-after"       content="2 days">'

                 ;
        return $meta;
    }

    /**
     * setSocialMeta
     *
     * setea la social meta
     *
     * @access private
     */
    private function setSocialMeta($socialMeta) {
        switch ($socialMeta){
            case 'OpenGraph':
                $this->socialMeta = "\t\t" .$this->getOpenGraphMeta();
                break;
            case 'Twiter':
                $this->socialMeta = "\t\t" .$this->getTwiterMeta();
                break;
            case 'Google':
                $this->socialMeta = "\t\t" .$this->getGoogleMeta();
                break;
        }
    }

    /**
     * getSocialCards
     *
     * retorna el schema compuesto soicitado
     *
     * @access private
     * @return string
     */
    private function getSocialCards() {
        $result = '';
        if($this->socialCards){
            foreach ($this->socialCards as $socialmeta) {
                $this->setSocialMeta($socialmeta['type']);
                $result .= $this->socialMeta . PHP_EOL ;
            }
        }
        return trim($result);
    }

    /**
     * getMetadata
     *
     * retorna la metadata de la pagina
     *
     * @access public
     * @return string
     */
    public function getMetadata() {
        $this->meta = [
            'title'                 => $this->title,
            'descripcion'           => $this->description,
            'author'                => $this->author,
            'canonical_url'         => $this->canonical,
            'guachi_version'        => GUACHI_VERSION,
            'lang'                  => $this->lang,
            'css'                   => $this->css,
            /*SEO*/
            'google_verification'   => $this->google_verification,
            'schemas'               => $this->getSchemas(),
            'socialmeta'            => $this->getSocialCards()
        ];
        return $this->meta;
    }  
}
