<?php 

namespace Controllers;
use MVC\Router;
use Model\Admin;

class LoginController {

    public static function login( Router $router){
        $errores = [];

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $auth = new Admin($_POST);
            $errores = $auth->validar();

            if(empty($errores)) {
                $resultado = $auth->existeUsuario();
                if(!$resultado){
                    // VERIFICAR SI EL USUARIO EXISTE (mensaje de error)
                    $errores = Admin::getErrores();
                }else{
                  
                    // VERIFICAR EL PASSWORD
                    $autenticado = $auth->comprobarPassword($resultado);

                    if($autenticado){
                    // AUTENTICAR AL USUARIO
                    $auth->autenticar();

                    }else{
                        // VERIFICAR SI EL USUARIO EXISTE (mensaje de error)

                        $errores = Admin::getErrores();

                    }
                }

                
            }
        }
        $router->render('auth/login',[
            'errores'=>$errores
        ]);
    }

    public static function logout(){
        session_start();

        $_SESSION = [];

        header('Location: /');
        
    }
}