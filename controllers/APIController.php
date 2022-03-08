<?php
    namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

    class APIController{
        public static function index() {
            $servicios = Servicio::all();
            echo json_encode($servicios);
        }

        public static function guardar() {
            //Almacena la cita y devuelve el id
            $cita = new Cita($_POST);
            $resultado = $cita->guardar();

            //Almacena los servicios con id de la cita
            $idServicios = explode(",", $_POST['servicios']);
            $id = $resultado['id'];
            
            
            foreach($idServicios as $idServicio){
                $args = [
                    'citaid' => $id,
                    'servicioid' => $idServicio
                ];

                $citaServicio = new CitaServicio($args);
                $citaServicio->guardar();
            }
            //retornamos una respuesta
            echo json_encode(['resultado' => $resultado]);
        }

        public static function eliminar(){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                
                $id = $_POST['id'];
                $cita = Cita::find($id);
                $cita->eliminar();

                header('Location:' . $_SERVER['HTTP_REFERER']);
            }
        }
    }



?>