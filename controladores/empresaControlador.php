<?php 
    
    if($peticionAjax){
        require_once "../modelos/empresaModelo.php";
    }else{
        require_once "./modelos/empresaModelo.php";
    }

    class empresaControlador extends empresaModelo{

        /* Modelo datos empresa */
        public function datos_empresa_controlador(){
            return empresaModelo::datos_empresa_modelo();
        }

        public static function agregar_empresa_controlador(){
            // Código para agregar empresa (no modificado)
            $nombre=mainModel::limpiar_cadena($_POST['empresa_nombre_reg']);
            $email=mainModel::limpiar_cadena($_POST['empresa_email_reg']);
            $telefono=mainModel::limpiar_cadena($_POST['empresa_telefono_reg']);
            $direccion=mainModel::limpiar_cadena($_POST['empresa_direccion_reg']);

            /*Verificar campos vacios */
            if($nombre=='' || $email=='' || $telefono=='' || $direccion==''){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrio un error inesperado",
                    "Texto"=>"No has llenado todos los campos obligatorios",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*Verficando integridad de los datos */
            // Expresión regular corregida
            if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ &%$\/()#!.,\-]{1,70}",$nombre)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"El NOMBRE no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Expresión regular corregida
            if(!mainModel::verificar_datos("[0-9()\\+]{8,20}",$telefono)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"El TELEFONO no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Expresión regular corregida
            if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ().,#\-]{1,190}",$direccion)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"La DIRECCIÓN no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Has ingresado un correo inválido",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*Comprobar empresas registradas */
            $check_empresas=mainModel::ejecutar_consulta_simple("SELECT empresa_id FROM empresa");
            if($check_empresas->rowCount()>=1){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Ya existe una empresa registrada, no puedes agregar más",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $datos_empresa_reg=[
                "Nombre"=>$nombre,
                "Email"=>$email,
                "Telefono"=>$telefono,
                "Direccion"=>$direccion
            ];

            $agregar_empresa=empresaModelo::agregar_empresa_modelo($datos_empresa_reg);
            if($agregar_empresa->rowCount()==1){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Empresa registrada",
                    "Texto"=>"Los datos de la empresa se registraron correctamente",
                    "Tipo"=>"success"
                ];
                echo json_encode($alerta);
                exit();
            }else{
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No hemos podido registrar la empresa, por favor intente nuevamente",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }
        }
        
        /* Controlador actualizar empresa */
        public static function actualizar_empresa_controlador(){
            // Obtener el ID de la empresa a actualizar
            $id = mainModel::limpiar_cadena($_POST['empresa_id_up']);
            
            // Limpiar los datos recibidos
            $nombre = mainModel::limpiar_cadena($_POST['empresa_nombre_up']);
            $email = mainModel::limpiar_cadena($_POST['empresa_email_up']);
            $telefono = mainModel::limpiar_cadena($_POST['empresa_telefono_up']);
            $direccion = mainModel::limpiar_cadena($_POST['empresa_direccion_up']);
            
            // Verificar campos vacíos
            if($nombre=='' || $email=='' || $telefono=='' || $direccion==''){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No has llenado todos los campos obligatorios",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }
            
            // Verificando integridad de los datos
            // Expresión regular corregida
            if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ &%$\/()#!.,\-]{1,70}",$nombre)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"El NOMBRE no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Expresión regular corregida
            if(!mainModel::verificar_datos("[0-9()\\+]{8,20}",$telefono)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"El TELEFONO no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            // Expresión regular corregida
            if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ().,#\-]{1,190}",$direccion)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"La DIRECCIÓN no coincide con el formato solicitado",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"Has ingresado un correo inválido",
                    "Tipo"=>"error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*comprobando privilegios de usuario */
            session_start(['name'=>'SPM']);
            if($_SESSION['privilegio_spm']<1 || $_SESSION['privilegio_spm']>2){
                $alerta=[
                        "Alerta"=>"simple",
                        "Titulo"=>"Ocurrió un error inesperado",
                        "Texto"=>"No tienes los permisos necesarios para realizar esta operación",
                        "Tipo"=>"error"
                    ];
                    echo json_encode($alerta);
                    exit();
            }

            // Actualizar datos
            $datos_empresa_up=[
                "ID"=>$id,
                "Nombre"=>$nombre,
                "Email"=>$email,
                "Telefono"=>$telefono,
                "Direccion"=>$direccion
            ];
            
            // Usar el método estático correctamente
            $actualizar_empresa = empresaModelo::actualizar_empresa_modelo($datos_empresa_up);
            
            if($actualizar_empresa->rowCount()==1){
                $alerta=[
                    "Alerta"=>"recargar",
                    "Titulo"=>"Empresa actualizada",
                    "Texto"=>"Los datos de la empresa se actualizaron correctamente",
                    "Tipo"=>"success"
                ];
            }else{
                $alerta=[
                    "Alerta"=>"simple",
                    "Titulo"=>"Ocurrió un error inesperado",
                    "Texto"=>"No hemos podido actualizar los datos de la empresa",
                    "Tipo"=>"error"
                ];
            }
            echo json_encode($alerta);
            exit();
        }
        
        /* Fin controlador */
    }
?>