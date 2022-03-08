<?php
    namespace Model;

    class Usuario extends ActiveRecord{
        //Base de datos
        protected static $tabla = 'usuarios';
        protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 
        'admin', 'confirmado', 'token'];

        public $id;
        public $nombre;
        public $apellido;
        public $email;
        public $password;
        public $telefono;
        public $admin;
        public $confirmado;
        public $token;

        public function __construct($args = [])
        {
            $this->id = $args['id'] ?? null;
            $this->nombre = $args['nombre'] ?? '';
            $this->apellido = $args['apellido'] ?? '';
            $this->email = $args['email'] ?? '';
            $this->password = $args['password'] ?? '';
            $this->telefono = $args['telefono'] ?? '';
            $this->admin = $args['admin'] ?? '0';
            $this->confirmado = $args['confirmado'] ?? '0';
            $this->token = $args['token'] ?? '';
        }

        //Mensaje de validacion para la creacion de la cuenta
        public function validarNuevaCuenta(){
            if(!$this->nombre){
                self::$alertas['error'][] = 'El Nombre es Obligatorio';
            }

            if(!$this->apellido){
                self::$alertas['error'][] = 'El Apellido es Obligatorio';
            }

            if(!$this->email){
                self::$alertas['error'][] = 'El E-mail es Obligatorio';
            }

            if(!$this->password){
                self::$alertas['error'][] = 'El Password es Obligatorio';
            }

            if(strlen($this->password) < 8) {
                self::$alertas['error'][] = 'El Password debe contener al menos 8 Caracteres';
            }

            return self::$alertas;
        }

        //Validar usuario al loguearse
        public function validarLogin(){
            if(!$this->email){
                self::$alertas['error'][] = 'El email es Obligatorio';
            }

            if(!$this->password){
                self::$alertas['error'][] = 'El password es Obligatorio';
            }

            return self::$alertas;
        }

        public function validarEmail(){
            if(!$this->email){
                self::$alertas['error'][] = 'El email es Obligatorio';
            }

            return self::$alertas;
        }

        public function validarPassword(){
            if(!$this->password){
                self::$alertas['error'][] = 'El password es obligatorio';
            }

            if(strlen($this->password) < 8){
                self::$alertas['error'][] = 'El Password debe contener al menos 8 Caracteres';
            }

            return self::$alertas;
        }

        //Revisa si el usuario existe
        public function existeUsuario(){
            $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1 ";
            
            $resultado = self::$db->query($query);

            if($resultado->num_rows) {
                self::$alertas['error'][] = 'El Usuario Ya Esta Registrado';
            }

            return $resultado;

        }

        public function hashPassword(){
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        }

        public function crearToken(){
            $this->token = uniqid();
        }

        public function comprobarPasswordAndConfirmado($password){
            $resultado = password_verify($password, $this->password);

            if(!$resultado || !$this->confirmado){
                self::$alertas['error'][] = 'El password es incorrecto o tu cuenta no ha sido confirmada';
            }else{
                return true;
            }
        }

    }

?>