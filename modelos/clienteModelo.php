<?php

    require_once "mainModel.php";

    class clienteModelo extends mainModel{

        /*----------  Modelo agregar cliente  ----------*/
        protected static function agregar_cliente_modelo($datos){
            $sql=mainModel::conectar()->prepare("INSERT INTO cliente(cliente_nombre,cliente_tipo_identificacion,cliente_numero_identificacion,cliente_digito_verificacion,cliente_tipo_regimen_iva,cliente_direccion,cliente_ciudad,cliente_telefono,cliente_nombres_contacto,cliente_estado) VALUES(:Nombre,:TipoIdentificacion,:NumeroIdentificacion,:DigitoVerificacion,:TipoRegimenIva,:Direccion,:Ciudad,:Telefono,:NombresContacto,:Estado)");

            $sql->bindParam(":Nombre",$datos['Nombre']);
            $sql->bindParam(":TipoIdentificacion",$datos['TipoIdentificacion']);
            $sql->bindParam(":NumeroIdentificacion",$datos['NumeroIdentificacion']);
            $sql->bindParam(":DigitoVerificacion",$datos['DigitoVerificacion']);
            $sql->bindParam(":TipoRegimenIva",$datos['TipoRegimenIva']);
            $sql->bindParam(":Direccion",$datos['Direccion']);
            $sql->bindParam(":Ciudad",$datos['Ciudad']);
            $sql->bindParam(":Telefono",$datos['Telefono']);
            $sql->bindParam(":NombresContacto",$datos['NombresContacto']);
            $sql->bindParam(":Estado",$datos['Estado']);
            $sql->execute();

            return $sql;
        }


        /*----------  Modelo datos cliente  ----------*/
        protected static function datos_cliente_modelo($tipo,$id){
            if($tipo=="Unico"){
                $sql=mainModel::conectar()->prepare("SELECT * FROM cliente WHERE cliente_id=:ID");
                $sql->bindParam(":ID",$id);
            }elseif($tipo=="Conteo"){
                $sql=mainModel::conectar()->prepare("SELECT cliente_id FROM cliente");
            }
            $sql->execute();
            return $sql;
        }


        /*----------  Modelo actualizar cliente  ----------*/
        protected static function actualizar_cliente_modelo($datos){
            $sql=mainModel::conectar()->prepare("UPDATE cliente SET cliente_nombre=:Nombre,cliente_tipo_identificacion=:TipoIdentificacion,cliente_numero_identificacion=:NumeroIdentificacion,cliente_digito_verificacion=:DigitoVerificacion,cliente_tipo_regimen_iva=:TipoRegimenIva,cliente_direccion=:Direccion,cliente_ciudad=:Ciudad,cliente_telefono=:Telefono,cliente_nombres_contacto=:NombresContacto,cliente_estado=:Estado WHERE cliente_id=:ID");

            $sql->bindParam(":Nombre",$datos['Nombre']);
            $sql->bindParam(":TipoIdentificacion",$datos['TipoIdentificacion']);
            $sql->bindParam(":NumeroIdentificacion",$datos['NumeroIdentificacion']);
            $sql->bindParam(":DigitoVerificacion",$datos['DigitoVerificacion']);
            $sql->bindParam(":TipoRegimenIva",$datos['TipoRegimenIva']);
            $sql->bindParam(":Direccion",$datos['Direccion']);
            $sql->bindParam(":Ciudad",$datos['Ciudad']);
            $sql->bindParam(":Telefono",$datos['Telefono']);
            $sql->bindParam(":NombresContacto",$datos['NombresContacto']);
            $sql->bindParam(":Estado",$datos['Estado']);
            $sql->bindParam(":ID",$datos['ID']);
            $sql->execute();
            
            return $sql;
        }


        /*----------  Modelo eliminar cliente  ----------*/
        protected static function eliminar_cliente_modelo($id){
            $sql=mainModel::conectar()->prepare("DELETE FROM cliente WHERE cliente_id=:ID");

            $sql->bindParam(":ID",$id);
            $sql->execute();
            
            return $sql;
        }
    }