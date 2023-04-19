<?php 

namespace MVC;
class Router {
    
    public $rutasGET = [];
    public $rutasPOST = [];

    public function get($url, $fn){
        $this->rutasGET[$url] = $fn;
    }

    public function post($url, $fn){
        $this->rutasPOST[$url] = $fn;
    }
    public function comprobarRutas(){
        // ARREGLO DE RUTAS PROTEGIDAS...
        session_start();

        $auth = $_SESSION['login'] ?? null;

        

        $rutas_protegidas = ['/admin','/propiedades/crear','/propiedades/actualizar', 
        '/propiedades/eliminar', '/vendedores/crear','/vendedores/actualizar','/vendedores/eliminar'];
        $urlActual = $_SERVER['REQUEST_URI'] === '' ? '/' : $_SERVER['REQUEST_URI'];
        $metodo = $_SERVER['REQUEST_METHOD'];
       
        if($metodo === "GET"){
            $urlActual = explode('?',$urlActual)[0];
            $fn = $this->rutasGET[$urlActual] ?? null;
        }else {
            $urlActual = explode('?',$urlActual)[0];
            $fn = $this->rutasPOST[$urlActual] ?? null;

        }

        // PROTEGER LAS RUTAS
        if(in_array($urlActual, $rutas_protegidas) && !$auth){
            header("Location: /");
        }

        if($fn){
            // LA URL EXISTE Y HAY UNA FUNCION ASOCIADA

            // PERMITE LLAMAR A UNA FUNCION CUANDO NO SABEMOS COMO SE LLAMA UNA FUNCION
            call_user_func($fn, $this);
        }else{
            echo "Pagina no encontrada";
        }
    }

    // MUESTRA UNA VISTA

    public function render($view, $datos = []){
        foreach($datos as $key => $value){
            // AL PONERLE EL DOBLE SIMBOLO DE PESOS
            // LO QUE HACEMOS ES DECIRLE QUE EL MENSAJE QUE 
            // RECIBAMOS SERA LA LLAVE Y EL VALUE
            // LA VARIABLE VALUE

            // ES DECIR COMO LE VAMOS A ESTAR PASANDO MULTIPLES 
            // VALORES DE NUETRA CLASE PropiedadController
            // no sabemos cual es el valor que estamos recibiendo
            // EN LA ITERACION TOMARA LA LLAVE DE LA ITERACION

            // VARIBLE DE VARIABLE
            $$key = $value;
        }
        // INICIAR UN ALMECENAMIENTO EN MEMORIA
        ob_start();

        // LE PASAMOS LA VISTA - GUARADAR EN MEMORIA
        include_once __DIR__ . "/views/$view.php";

        // SE GUARDA EN LA LA VARIABLE CONTENIDO - LIMPIAMOS LA MEMORIA
        $contenido = ob_get_clean(); // LIMPIAR BUFFER

        include_once __DIR__ . "/views/layout.php";
    }
}