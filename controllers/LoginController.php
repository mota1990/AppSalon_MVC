<?php
    namespace Controllers;

    use Classes\Email;
    use MVC\Router; 
    use Model\Usuario;


    class LoginController{
        public static function login(Router $router){
            $alertas = [];
            $auth = new Usuario;

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                
                $auth = new Usuario($_POST);

                $alertas = $auth->validarLogin();

                if(empty($alertas)){
                    //Comprobamos que el usuario existe
                    $usuario = Usuario::where('email', $auth->email);

                    if($usuario){
                        //Verificar el password
                        if($usuario->comprobarPasswordAndConfirmado($auth->password)){
                            //Autetinticar el usuario
                            session_start();

                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;

                            //Redireccionamiento
                            if($usuario->admin === "1"){
                                $_SESSION['admin'] = $usuario->admin ?? null;
                                header('Location: /admin');
                            }else{
                                header('Location: /cita');
                            }
                        }
                    }else{
                        Usuario::setAlerta('error', 'Usuario no Encontrado');
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/login', [
                'alertas' => $alertas,
                'auth' => $auth
            ]);
        }

        public static function logout(){
            if(!$_SESSION){
                session_start();
            }

            $_SESSION = [];
            header('Location: /');
        }

        public static function olvide(Router $router){
            
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $auth = new Usuario($_POST);
                $alertas = $auth->validarEmail();

                if(empty($alertas)){
                    $usuario = Usuario::where('email', $auth->email);

                    //Comprobamos que exista y que este confirmado
                    if($usuario && $usuario->confirmado === "1"){
                       
                        //Generar un token
                        $usuario->crearToken();
                        $usuario->guardar();

                        //Enviar EMail
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarIntruccion();
                        //Alerta Exito
                        Usuario::setAlerta('exito', 'Revisa tu Correo');
                        $alertas = Usuario::getAlertas();

                    }else{
                        Usuario::setAlerta('error', 'El Usuario No Existe o no esta confirmado');
                        $alertas = Usuario::getAlertas();
                    }
                }
            }

            $router->render('auth/olvide-password', [
                'alertas' => $alertas
            ]);
        }

        public static function recuperar(Router $router){

            $alertas = [];
            $error = false;

            $token = sanitizar($_GET['token']);

            //Buscar usuario por su token
            $usuario = Usuario::where('token', $token);

            if(empty($usuario)){
                Usuario::setAlerta('error', 'Token No Valido');
                $error = true;
            }

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                //Leer el nuevo password y guardarlo

                $password = new Usuario($_POST);

                $alertas = $password->validarPassword();

                if(empty($alertas)){
                    $usuario->password = '';
                    $usuario->password = $password->password;
                    $usuario->hashPassword();
                    $usuario->token = '';
                    $resultado = $usuario->guardar();

                    if($resultado){
                        header('Location: /');
                    }
                }
            }

             $alertas = Usuario::getAlertas();

            $router->render('auth/recuperar-password', [
                'alertas' => $alertas,
                'error' => $error
            ]);
        }

        public static function crear(Router $router){
            $usuario = new Usuario;
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                $usuario->sincronizar($_POST);
                $alertas = $usuario->validarNuevaCuenta();
                
                //Revisar que alertas este vacio
                if(empty($alertas)){
                    //Vereificar que el usuario no este registrado
                    $resultado = $usuario->existeUsuario();

                    if($resultado->num_rows){
                        //Ya esta Registrado
                        $alertas = Usuario::getAlertas();
                    }else{
                        //No esta Registrado

                        //Hashear el passowrd
                        $usuario->hashPassword();

                        //Generar Token unico
                        $usuario->crearToken();

                        //Enviar El Email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();

                        //Crear el usuario
                        $resultado = $usuario->guardar();
                        
                        if($resultado){
                            header('Location: /mensaje');
                        }
                    }
                }
            }

            $router->render('auth/crear-cuenta', [
                'usuario' => $usuario,
                'alertas' => $alertas
            ]);
        }

        public static function mensaje(Router $router){
            $router->render('auth/mensaje');
        }

        public static function confirmar(Router $router){

            $alertas = [];
            $token = sanitizar($_GET['token']);
            $usuario = Usuario::where('token', $token);

            if(empty($usuario)){
                //Mostrar mensaje de error
                Usuario::setAlerta('error', 'Token no Valido');
            }else{
                //Cambiar campo confirmado de usaurio
                $usuario->confirmado = "1";
                $usuario->token = '';
                $usuario->guardar();
                Usuario::setAlerta('exito', 'Cuenta Confirmada Correctamente');
            }

            //Obtener Alertas
            $alertas = Usuario::getAlertas();    
            
            //Renderizar Vista
            $router->render('auth/confirmar-cuenta', [
                'alertas' => $alertas
            ]);
        }
    }



?>