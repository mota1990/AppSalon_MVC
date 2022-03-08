<?php
    namespace Controllers;

    use MVC\Router;

    class CitaController{
        public static function index(Router $router){

            if(!$_SESSION){
                session_start();
            }

            estaAutenticado();

            $router->render('cita/index', [
                'nombre' => $_SESSION['nombre'],
                'id' => $_SESSION['id']
            ]);
        }
    }





?>