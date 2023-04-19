<?php
namespace Model;
date_default_timezone_set('UTC');
date_default_timezone_set("America/Mexico_City");
class ActiveRecord{
    protected static $db;
    protected static $columnasDB = [];
    protected static $tabla = '';

    // ERRORES 
    protected static $errores = [];
    

    // DEFINIR LA CONEXION A LA DB
    public static function setDb($database){
        self::$db = $database;
    }

    

    public function guardar(){
        if(!is_null($this->id)){
            // ACTUALIZAR
            $this->actualizar();
        }else{
            // CREANDO UN NUEVO REGISTRO
            $this->crear();
        }
    }

    public function crear(){
        // SANITIZAR LOS DATOS

        $atributos = $this->sanitizarAtributos();
        
        
        // INSERTAR EN LA DB
        $query = " INSERT INTO " . static::$tabla . "( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";

        
    
        $resultado = self::$db->query($query);
        // MENSAJE DE EXITO
        if($resultado){
            //    REDIRECCIONAR AL USUARIO
             header("Location: /admin?resultado=1");
        }
       

        
    }

    public function actualizar(){
        // SANITIZAR LOS DATOS
        $atributos = $this->sanitizarAtributos();
        $valores = [];

        foreach($atributos as $key => $value){
            $valores[] = "{$key}='{$value}'";
        }
      
       $query =  "UPDATE " . static::$tabla . " SET ";
       $query .= join(', ', $valores);
       $query .= " WHERE id = '". self::$db->escape_string($this->id) . "' ";
       $query .= " LIMIT 1 ";
       

       $resultado = self::$db->query($query);
       
        if($resultado){
            //    REDIRECCIONAR AL USUARIO
            header("Location:/admin?resultado=2");
        }
    }

    public function eliminar(){
        // ELIMINAR EL REGISTRO
        $query = "DELETE FROM " .  static::$tabla . " WHERE id =  " . self::$db->escape_string($this->id) . " LIMIT 1";
        
        $resultado = self::$db->query($query);

        if($resultado){
            $this->borrarImagen();
            header('Location:/admin?resultado=3');
        }
    }

    // IDENTIFICAR Y UNIR LOS ATRIBUTOS DE LA DB
    public function atributos(){
        $atributos = [];
        foreach(static::$columnasDB as $columna){
            if($columna === "id") continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos(){
        $atributos = $this->atributos();
        $sanitizado = [];

        foreach($atributos as $key => $value){
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    // SUBIDA DE ARCHIVOS
    public function setImagen($imagen){

        // ELIMINA LA IMAGEN PREVIA
        if(!is_null($this->id)){
            $this->borrarImagen();
        }
        // ASIGNAR AL ATRIBUTO DE IMAGEN EL NOMBRE DE LA IMAGEN
        if($imagen){
            $this->imagen = $imagen;
        }
    }
    
    // ELIMINA EL ARCHIVO
    public function borrarImagen(){
        // COMPROBAR SI EXISTE EL ARCHIVO
        $existeArchivo = file_exists(CARPETA_IMAGENES.$this->imagen);

        if($existeArchivo){
            unlink(CARPETA_IMAGENES.$this->imagen);
        }
    }
    // VALIDACION
    public static function getErrores(){
        return static::$errores;
    }

    public function validar(){

        // LIMPRIAR EL ARREGLO 
        static::$errores = [];
        
        // GENERAR NUEVOS ERRORES EN CASO DE NECESITARSE, CON LA INFORMACION DE LA CLASE QUE SE ESTE HEREDANDO
        return static::$errores;
    }

    // LISTA TODAS LAS PROPIEDADES
    public static function all(){
        $query = "SELECT * FROM " . static::$tabla;
    
        $resultado = self::consultarSQL($query);
        return $resultado;

        
    }

    public static function get($cantidad){
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad;
    
        $resultado = self::consultarSQL($query);
        return $resultado;

        
    }

    // BUSCA UN REGISTRO POR SU ID
    public static function find($id){
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = $id";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
    public static function consultarSQL($query){
        // CONSULTAR EN LA DB
        $resultado = self::$db->query($query);

        // ITERAR LOS RESULTADOS
        $array = [];
        while($registro = $resultado->fetch_assoc()){
            $array[] = static::crearObjeto($registro);
        }
       

        // LIBERAR MEMORIA
        $resultado->free();

        // RETORNAR
        return $array;
    }

    protected static function crearObjeto($registro){
        $objeto = new static;

        foreach($registro as $key => $value){
            if(property_exists($objeto, $key)){
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // SINCRONIZA EL OBJETO EN MEMORIA CON LOS CAMBIOS REALIZADOS POR EL USUARIO

    public function sincronizar($args = []){
        foreach($args as $key => $value){
            // MAPEO DEL OBJETO EN MEMORIA CON LA NUEVA INFORMACION
            if(property_exists($this, $key) && !is_null($value)){
                $this->$key = $value;
            }
        }
    }

   
}