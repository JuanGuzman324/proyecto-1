<?php

	if($peticionAjax){
		require_once "../modelos/clienteModelo.php";
	}else{
		require_once "./modelos/clienteModelo.php";
	}

    class clienteControlador extends clienteModelo{

        /*--------- controlador agregar cliente ---------*/
		public function agregar_cliente_controlador(){

            $dni=mainModel::limpiar_cadena($_POST['cliente_dni_reg']);
            $nombre=mainModel::limpiar_cadena($_POST['cliente_nombre_reg']);
			$apellido=mainModel::limpiar_cadena($_POST['cliente_apellido_reg']);
			$telefono=mainModel::limpiar_cadena($_POST['cliente_telefono_reg']);
			$direccion=mainModel::limpiar_cadena($_POST['cliente_direccion_reg']);

			/*---------- Comprobar campos vacios----------*/
            if($dni=="" || $nombre=="" || $apellido=="" || $telefono=="" || $direccion==""){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son obligatorios",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
            }

            /*== Verificando integridad de los datos ==*/
			if(!mainModel::verificar_datos("[0-9-]{1,27}",$dni)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El DNI no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if(!mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El nombre no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if(!mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$apellido)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El apellido no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if($telefono!=""){
				if(!mainModel::verificar_datos("[0-9\(\)\+]{8,20}",$telefono)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El telefono no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			if($direccion!=""){
				if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}",$direccion)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La dirección no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

            $check_dni=mainModel::ejecutar_consulta_simple("SELECT cliente_dni FROM cliente WHERE cliente_dni='$dni'");
			if($check_dni->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El DNI ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

            $datos_cliente_reg=[
				"DNI"=>$dni,
				"Nombre"=>$nombre,
				"Apellido"=>$apellido,
				"Telefono"=>$telefono,
				"Direccion"=>$direccion
			];

			$agregar_cliente=clienteModelo::agregar_cliente_modelo($datos_cliente_reg);

			if($agregar_cliente->rowCount()==1){
				$alerta=[
					"Alerta"=>"limpiar",
					"Titulo"=>"Cliente registrado",
					"Texto"=>"Los datos del cliente han sido registrados con éxito",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el cliente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
        } /* Fin controlador */

		/*--------- Controlador paginar cliente ---------*/
		public function paginador_cliente_controlador($pagina,$registros,$privilegio,$id,$url,$busqueda){

			$pagina=mainModel::limpiar_cadena($pagina);
			$registros=mainModel::limpiar_cadena($registros);
			$privilegio=mainModel::limpiar_cadena($privilegio);

			$url=mainModel::limpiar_cadena($url);
			$url=SERVERURL.$url."/";

			$busqueda=mainModel::limpiar_cadena($busqueda);
			$tabla="";

			$pagina= (isset($pagina) && $pagina>0) ? (int) $pagina : 1 ;
			$inicio= ($pagina>0) ? (($pagina*$registros)-$registros) : 0 ;

			if(isset($busqueda) && $busqueda!=""){
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM cliente WHERE cliente_dni LIKE '%$busqueda%' OR cliente_nombre LIKE '%$busqueda%' OR cliente_apellido LIKE '%$busqueda%' OR cliente_telefono LIKE '%$busqueda%' or cliente_direccion LIKE '&$busqueda&' ORDER BY cliente_nombre ASC LIMIT $inicio,$registros";
			}else{
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM cliente ORDER BY cliente_nombre ASC LIMIT $inicio,$registros";
			}

			$conexion = mainModel::conectar();

			$datos = $conexion->query($consulta);
			$datos = $datos->fetchAll();

			$total = $conexion->query("SELECT FOUND_ROWS()");
			$total = (int) $total->fetchColumn();

			$Npaginas=ceil($total/$registros);

			
			$tabla.='<div class="table-responsive">
				<table class="table table-dark table-sm">
					<thead>
						<tr class="text-center roboto-medium">
							<th>#</th>
							<th>DNI</th>
							<th>NOMBRE</th>
							<th>APELLIDO</th>
							<th>TELÉFONO</th>
							<th>DIRECCIÓN</th>';
							if($privilegio==1 || $privilegio==2){
								$tabla.='<th>ACTUALIZAR</th>';
							}
							if($privilegio==1 ){
								$tabla.='<th>ELIMINAR</th>';
							}
						$tabla.='</tr>
					</thead>
					<tbody>';
			if($total>=1 && $pagina<=$Npaginas){
				$contador=$inicio+1;
				$reg_inicio=$inicio+1;
				foreach($datos as $rows){
					$tabla.='
					<tr class="text-center" >
						<td>'.$contador.'</td>
						<td>'.$rows['cliente_dni'].'</td>
						<td>'.$rows['cliente_nombre'].'</td>
						<td>'.$rows['cliente_apellido'].'</td>
						<td>'.$rows['cliente_telefono'].'</td>
						<td><button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" title="'.$rows['cliente_nombre'].' '.$rows['cliente_apellido'].'" data-content="'.$rows['cliente_direccion'].'">
							<i class="fas fa-info-circle"></i>
						</button></td>';
						if($privilegio==1 || $privilegio==2){

							$tabla.='<td>
							<a href="'.SERVERURL.'client-update/'.mainModel::encryption($rows['cliente_id']).'/" class="btn btn-success">
									<i class="fas fa-sync-alt"></i>	
							</a>
						</td>';
						if($privilegio==1){
							$tabla.='<td>
								<form class="FormularioAjax" action="'.SERVERURL.'ajax/clienteAjax.php" method="POST" data-form="delete" autocomplete="off">
									<input type="hidden" name="cliente_id_del" value="'.mainModel::encryption($rows['cliente_id']).'">
									<button type="submit" class="btn btn-warning">
											<i class="far fa-trash-alt"></i>
									</button>
								</form>
							</td>';
						}
						

						}
					$tabla.='</tr>';
					$contador++;
				}
				$reg_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='<tr class="text-center" ><td colspan="9">
					<a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">Haga clic aca para recargar el listado</a>
					</td></tr>';
				}else{
					$tabla.='<tr class="text-center" ><td colspan="9">No hay registros en el sistema</td></tr>';
				}
			}

			$tabla.='</tbody></table></div>';

			if($total>=1 && $pagina<=$Npaginas){
				$tabla.='<p class="text-right">Mostrando cliente '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.'</p>';

				$tabla.=mainModel::paginador_tablas($pagina,$Npaginas,$url,7);
			}

			return $tabla;
		} /* Fin controlador */

		/*--------- Controlador eliminar cliente ---------*/
		public function eliminar_cliente_controlador(){

			/* recibiendo id del cliente*/
			$id=mainModel::decryption($_POST['cliente_id_del']);
			$id=mainModel::limpiar_cadena($id);

			/* comprobando el cliente en BD */
			$check_cliente=mainModel::ejecutar_consulta_simple("SELECT cliente_id FROM cliente WHERE cliente_id='$id'");
			if($check_cliente->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El cliente que intenta eliminar no existe en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/* comprobando los prestamos */
			$check_prestamos=mainModel::ejecutar_consulta_simple("SELECT cliente_id FROM prestamo WHERE cliente_id='$id' LIMIT 1");
			if($check_prestamos->rowCount()>0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No podemos eliminar este cliente debido a que tiene prestamos asociados, recomendamos deshabilitar el cliente si ya no sera utilizado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			/* comprobando privilegios */
			session_start(['name'=>'SPM']);
			if($_SESSION['privilegio_spm']!=1){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No tienes los permisos necesarios para realizar esta operacion",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}


			$eliminar_cliente=clienteModelo::eliminar_cliente_modelo($id);

			if($eliminar_cliente->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"cliente eliminado",
					"Texto"=>"El cliente ha sido eliminado del sistema exitosamente",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido eliminar el cliente, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		} /* Fin controlador */

		/*--------- Controlador datos cliente ---------*/
		public function datos_cliente_controlador($tipo,$id){
			$tipo=mainModel::limpiar_cadena($tipo);

			$id=mainModel::decryption($id);
			$id=mainModel::limpiar_cadena($id);

			return clienteModelo::datos_cliente_modelo($tipo,$id);
		} /* Fin controlador */

		/*--------- Controlador actualizar cliente ---------*/
		public function actualizar_cliente_controlador(){

			// Recibiendo el id
			$id=mainModel::decryption($_POST['cliente_id_up']);
			$id=mainModel::limpiar_cadena($id);

			// comprobar el cliente en la BD
			$check_cliente=mainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE cliente_id='$id'");
			if($check_cliente->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado el cliente en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}else{
				$campos=$check_cliente->fetch();
			}

			$dni=mainModel::limpiar_cadena($_POST['cliente_dni_up']);
			$nombre=mainModel::limpiar_cadena($_POST['cliente_nombre_up']);
			$apellido=mainModel::limpiar_cadena($_POST['cliente_apellido_up']);
			$telefono=mainModel::limpiar_cadena($_POST['cliente_telefono_up']);
			$direccion=mainModel::limpiar_cadena($_POST['cliente_direccion_up']);

			/*== comprobar campos vacios ==*/
			if($dni=="" || $nombre=="" || $apellido==""){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son obligatorios",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			/*== Verificando integridad de los datos ==*/
			if(!mainModel::verificar_datos("[0-9-]{1,27}",$dni)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El DNI no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			if(!mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El nombre no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			if(!mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$apellido)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El apellido no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			if($telefono!=""){
				if(!mainModel::verificar_datos("[0-9\(\)\+]{8,20}",$telefono)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El telefono no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}

			if($direccion!=""){
				if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}",$direccion)){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La dirección no coincide con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}
			/*==Comprobar dni==*/
			if($dni!=$campos['cliente_dni']){
				$check_dni=mainModel::ejecutar_consulta_simple("SELECT cliente_dni FROM cliente WHERE cliente_dni='$dni'");
				if($check_dni->rowCount()>0){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El DNI ingresado ya se encuentra registrado en el sistema",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}
            

			// Comprobar privilegios
			session_start(['name'=>'SPM']);
			if($_SESSION['privilegio_spm']<1 || $_SESSION['privilegio_spm']>2){
				$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"No cuentas con los permisos necesarios para realizar esta operación",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
			}
				
			/*== Preparando datos para enviarlos al modelo ==*/
			$datos_cliente_up=[
				"DNI"=>$dni,
				"Nombre"=>$nombre,
				"Apellido"=>$apellido,
				"Telefono"=>$telefono,
				"Direccion"=>$direccion,
				"ID"=>$id
			];

			if(clienteModelo::actualizar_cliente_modelo($datos_cliente_up)){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Cliente actualizado",
					"Texto"=>"Los datos del cliente han sido actualizados con exito",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar los datos, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			
			echo json_encode($alerta);
		} /* Fin controlador */
    }