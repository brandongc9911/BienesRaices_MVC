<?php 

namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use PHPMailer\PHPMailer\PHPMailer;


class PaginasControllers{
    public static function index(Router $router){
        $propiedades = Propiedad::get(3);
        $inicio = true;
        $router->render('paginas/index',[
            'inicio' => $inicio,
            'propiedades' => $propiedades
        ]);
    }

    public static function nosotros(Router $router){
        $router->render('paginas/nosotros');
    }

    public static function propiedades(Router $router){
        $propiedades = Propiedad::all();
        $router->render('paginas/propiedades', [
            'propiedades' => $propiedades

        ]);
        
    }

    public static function propiedad(Router $router){
        $id = validarORedireccionar('/propiedades');

        // BUSCAR PROPIEDAD POR SU ID
        $propiedad = Propiedad::find($id);
        $router->render('paginas/propiedad', [
            'propiedad' => $propiedad

        ]);
    }

    public static function blog(Router $router){
        $router->render('paginas/blog');
        
    }

    public static function entrada(Router $router){
        $router->render('paginas/entrada');
        
    }

    public static function contacto(Router $router){
        $mensaje = null;

        if($_SERVER['REQUEST_METHOD'] === "POST" ){
            $respuestas = $_POST['contacto'];

            // CREAR UNA INSTANCIA DE PHPMailer
            $mail = new PHPMailer();

            // CONFIGURAR SMTP
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '01394612e48e30';
            $mail->Password = '230b6d65402bc7';

            // TLS PARECIDO A SSL, PERO HOY EN DIA ESTE SE USA MAS PARA LOS CERTIFICADOS. EN EL CASO DE LOS CORREOS SE USA TLS
            $mail->SMTPSecure = 'tls';
            $mail->Port = 2525;

            // CONFIGURAR EL CONTENIDO DEL EMAIL
            $mail->setFrom('admin@bienesraices.com');
            $mail->addAddress('admin@bienesraices.com', 'BienesRaices.com');
            $mail->Subject = 'Tienes un nuevo mensaje';

            // HABILITAR HTML
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
          
            // DEFINIR EL CONTENIDO
            $contenido = '<html>';
            $contenido .= '<p>Tienes un nuevo mensaje</p>';
            $contenido .= '<p>Nombre: '. $respuestas['nombre'] .' </p>';

            // ENVIAR DE FORMA CONDICIONAL ALGUNOS CAMPOS DE EMAIL O TELEFONO
            if($respuestas['contacto'] === 'telefono'){
                $contenido .= '<p>Eligió ser contactado por teléfono: </p>';
                $contenido .= '<p>Teléfono: '. $respuestas['telefono'] .' </p>';
                $contenido .= '<p>Fecha contacto: '. $respuestas['fecha'] .' </p>';
                $contenido .= '<p>Hora: '. $respuestas['hora'] .' </p>';
            }
            else{
                $contenido .= '<p>Eligió ser contactado por email: </p>';
                $contenido .= '<p>Email: '. $respuestas['email'] .' </p>';

            }

            $contenido .= '<p>Mensaje: '. $respuestas['mensaje'] .' </p>';
            $contenido .= '<p>Vende o Compra: '. $respuestas['tipo'] .' </p>';
            $contenido .= '<p>Precio o Presupuesto: $'. $respuestas['precio'] .' </p>';
            $contenido .= '<p>Contacto por: '. $respuestas['contacto'] .' </p>';
            $contenido .= '</html>';
            

            $mail->Body = $contenido;

            // ENVIAR EL EMAIL
            if($mail->send()){
                $mensaje =  "Mensaje enviado";
            }else{
                $mensaje = "Mensaje no enviado";

            }
        }
        $router->render('paginas/contacto',[
            'mensaje' => $mensaje
        ]);
        
    }
}