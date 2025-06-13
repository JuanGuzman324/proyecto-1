<?php
if($peticionAjax){
	require_once "../modelos/itemModelo.php";
}else{
	require_once "./modelos/itemModelo.php";
}

class itemControlador extends itemModelo{

	public function agregar_item_controlador(){
		$codigo=mainModel::limpiar_cadena($_POST['item_codigo_reg']);
		$nombre=mainModel::limpiar_cadena($_POST['item_nombre_reg']);
		$stock=mainModel::limpiar_cadena($_POST['item_stock_reg']);
		$estado=mainModel::limpiar_cadena($_POST['item_estado_reg']);
		$detalle=mainModel::limpiar_cadena($_POST['item_detalle_reg']);

		if($codigo=='' || $nombre=='' || $stock=='' || $estado=='' || $detalle==''){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(!mainModel::verificar_datos("[a-zA-Z0-9\\-]{1,45}",$codigo)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El CODIGO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(!mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}",$nombre)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El NOMBRE no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(!mainModel::verificar_datos("[0-9]{1,9}",$stock)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El STOCK no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\\(\\)\\.,#\\- ]{1,190}",$detalle)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El DETALLE no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}
		
		if($estado!='Habilitado' && $estado!='Deshabilitado'){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El ESTADO del item no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$check_codigo=mainModel::ejecutar_consulta_simple("SELECT item_codigo FROM item WHERE item_codigo='$codigo'");
		if($check_codigo->rowCount()>0){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"Ya existe un item registrado con este codigo",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$check_nombre=mainModel::ejecutar_consulta_simple("SELECT item_nombre FROM item WHERE item_nombre='$nombre'");
		if($check_nombre->rowCount()>0){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"Ya existe un item registrado con este nombre",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$datos_item_reg=[
			"Codigo"=>$codigo,
			"Nombre"=>$nombre,
			"Stock"=>$stock,
			"Estado"=>$estado,
			"Detalle"=>$detalle
		];

		$agregar_item=$this->agregar_item_modelo($datos_item_reg);
		if($agregar_item->rowCount()==1){
			$alerta=[
				"Alerta"=>"recargar",
				"Titulo"=>"Item registrado",
				"Texto"=>"Los datos del item se registraron correctamente",
				"Tipo"=>"success"
			];
			echo json_encode($alerta);
			exit();
		}else{
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"No hemos podido registrar el item, por favor intente nuevamente",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}
	}

	public function paginador_item_controlador($pagina,$registros,$privilegio,$id,$url,$busqueda){

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
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM item WHERE item_codigo LIKE '%$busqueda%' OR item_codigo LIKE '%$busqueda%' OR item_nombre LIKE '%$busqueda%' OR item_stock LIKE '%$busqueda%' or item_estado LIKE '&$busqueda&'or item_detalle LIKE '%$busqueda%' ORDER BY item_nombre ASC LIMIT $inicio,$registros";
			}else{
				$consulta="SELECT SQL_CALC_FOUND_ROWS * FROM item ORDER BY item_nombre ASC LIMIT $inicio,$registros";
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
							<th>CODIGO</th>
							<th>NOMBRE</th>
							<th>STOCK</th>
							<th>ESTADO</th>
							<th>DETALLE</th>';
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
						<td>'.$rows['item_codigo'].'</td>
						<td>'.$rows['item_nombre'].'</td>
						<td>'.$rows['item_stock'].'</td>
						<td>'.$rows['item_estado'].'</td>
						<td><button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" title="'.$rows['item_nombre'].' '.$rows['item_stock'].'" data-content="'.$rows['item_detalle'].'">
							<i class="fas fa-info-circle"></i>
						</button></td>';
						if($privilegio==1 || $privilegio==2){

							$tabla.='<td>
							<a href="'.SERVERURL.'item-update/'.mainModel::encryption($rows['item_id']).'/" class="btn btn-success">
									<i class="fas fa-sync-alt"></i>	
							</a>
						</td>';
						if($privilegio==1){
							$tabla.='<td>
								<form class="FormularioAjax" action="'.SERVERURL.'ajax/itemAjax.php" method="POST" data-form="delete" autocomplete="off">
									<input type="hidden" name="item_id_del" value="'.mainModel::encryption($rows['item_id']).'">
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
				$tabla.='<p class="text-right">Mostrando item '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.'</p>';

				$tabla.=mainModel::paginador_tablas($pagina,$Npaginas,$url,7);
			}

			return $tabla;
	}

	public function eliminar_item_controlador(){

			/* recibiendo id del item*/
			$id=mainModel::decryption($_POST['item_id_del']);
			$id=mainModel::limpiar_cadena($id);

			/* comprobando el item en BD */
			$check_item=mainModel::ejecutar_consulta_simple("SELECT item_id FROM item WHERE item_id='$id'");
			if($check_item->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El item que intenta eliminar no existe en el sistema",
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


			$eliminar_item=itemModelo::eliminar_item_modelo($id);

			if($eliminar_item->rowCount()==1){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"item eliminado",
					"Texto"=>"El item ha sido eliminado del sistema exitosamente",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido eliminar el item, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
	}

	public function datos_item_controlador($tipo,$id){
			$tipo=mainModel::limpiar_cadena($tipo);

			$id=mainModel::decryption($id);
			$id=mainModel::limpiar_cadena($id);

			return itemModelo::datos_item_modelo($tipo,$id);
	}

	public function actualizar_item_controlador(){

			// Recibiendo el id
			$id=mainModel::decryption($_POST['item_id_up']);
			$id=mainModel::limpiar_cadena($id);

			// comprobar el item en la BD
			$check_item=mainModel::ejecutar_consulta_simple("SELECT * FROM item WHERE item_id='$id'");
			if($check_item->rowCount()<=0){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos encontrado el item en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}else{
				$campos=$check_item->fetch();
			}

			$codigo=mainModel::limpiar_cadena($_POST['item_codigo_up']);
			$nombre=mainModel::limpiar_cadena($_POST['item_nombre_up']);
			$stock=mainModel::limpiar_cadena($_POST['item_stock_up']);
			$estado=mainModel::limpiar_cadena($_POST['item_estado_up']);
			$detalle=mainModel::limpiar_cadena($_POST['item_detalle_up']);

			/*== comprobar campos vacios ==*/
			if($codigo=='' || $nombre=='' || $stock=='' || $estado=='' || $detalle==''){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
			}
			/*== Verificando integridad de los datos ==*/
			if(!mainModel::verificar_datos("[a-zA-Z0-9\\-]{1,45}",$codigo)){
			$alerta=[
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrió un error inesperado",
				"Texto"=>"El CODIGO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
			}

			if(!mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}",$nombre)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El NOMBRE no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if(!mainModel::verificar_datos("[0-9]{1,9}",$stock)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El STOCK no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}

			if(!mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\\(\\)\\.,#\\- ]{1,190}",$detalle)){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El DETALLE no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			
			if($estado!='Habilitado' && $estado!='Deshabilitado'){
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El ESTADO del item no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
			/*==Comprobar dni==*/
			if($codigo!=$campos['item_codigo']){
				$check_codigo=mainModel::ejecutar_consulta_simple("SELECT item_codigo FROM item WHERE item_codigo='$codigo'");
				if($check_codigo->rowCount()>0){
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El CODIGO ingresado ya se encuentra registrado en el sistema",
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
			$datos_item_up=[
				"Codigo"=>$codigo,
				"Nombre"=>$nombre,
				"Stock"=>$stock,
				"Estado"=>$estado,
				"Detalle"=>$detalle,
				"ID"=>$id
			];

			if(itemModelo::actualizar_item_modelo($datos_item_up)){
				$alerta=[
					"Alerta"=>"recargar",
					"Titulo"=>"Item actualizado",
					"Texto"=>"Los datos del item han sido actualizados con exito",
					"Tipo"=>"success"
				];
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido actualizar los datos del item, por favor intente nuevamente",
					"Tipo"=>"error"
				];
			}
			
			echo json_encode($alerta);
	}
}