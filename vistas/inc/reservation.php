<script>
        
    /*----------  Buscar cliente  ----------*/
    function buscar_cliente(){
        let input_cliente=document.querySelector('#input_cliente').value;

        input_cliente=input_cliente.trim();

        if(input_cliente!=""){

            let datos = new FormData();
            datos.append("buscar_cliente", input_cliente);

            fetch('<?php echo SERVERURL; ?>ajax/prestamoAjax.php',{
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.text())
            .then(respuesta =>{
                let tabla_clientes=document.querySelector('#tabla_clientes');
                tabla_clientes.innerHTML=respuesta;
            });

        }else{
            Swal.fire({
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir el DNI, Nombre, Apellido o Teléfono del Usuario',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }


    /*----------  Agregar cliente  ----------*/
    function agregar_cliente(id){
        $('#ModalCliente').modal('hide');
        Swal.fire({
            title: '¿Quieres agregar este Usuario?',
            text: "Se va a agregar este Usuario para realizar un préstamo o una reservación",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, agregar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if(result.value){

                let datos = new FormData();
                datos.append("id_agregar_cliente", id);

                fetch('<?php echo SERVERURL; ?>ajax/prestamoAjax.php',{
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.json())
                .then(respuesta =>{
                    return alertas_ajax(respuesta);
                });
                
            }else{
                $('#ModalCliente').modal('show');
            }
        });
    }


    /*----------  Buscar item  ----------*/
    function buscar_item(){
        let input_item=document.querySelector('#input_item').value;

        input_item=input_item.trim();

        if(input_item!=""){

            let datos = new FormData();
            datos.append("buscar_item", input_item);

            fetch('<?php echo SERVERURL; ?>ajax/prestamoAjax.php',{
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.text())
            .then(respuesta =>{
                let tabla_items=document.querySelector('#tabla_items');
                tabla_items.innerHTML=respuesta;
            });

            
        }else{
            Swal.fire({
                title: 'Ocurrió un error inesperado',
                text: 'Debes de introducir el Código o Nombre del item',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    /*----------  Procesar agregar item  ----------*/
    function procesar_agregar_item(formulario) {
        // Validar que todos los campos requeridos estén llenos
        let formato = formulario.querySelector('#detalle_formato').value;
        let cantidad = formulario.querySelector('#detalle_cantidad').value;
        let tiempo = formulario.querySelector('#detalle_tiempo').value;
        let costo = formulario.querySelector('#detalle_costo_tiempo').value;
        let id_item = formulario.querySelector('#id_agregar_item').value;

        // Validaciones básicas
        if (!formato || !cantidad || !tiempo || !costo || !id_item) {
            Swal.fire({
                title: 'Error',
                text: 'Todos los campos son obligatorios',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        if (parseInt(cantidad) < 1) {
            Swal.fire({
                title: 'Error',
                text: 'La cantidad debe ser mayor a 0',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        if (parseInt(tiempo) < 1) {
            Swal.fire({
                title: 'Error',
                text: 'El tiempo debe ser mayor a 0',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        if (parseFloat(costo) < 0) {
            Swal.fire({
                title: 'Error',
                text: 'El costo no puede ser negativo',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        // Crear FormData con los datos del formulario
        let datos = new FormData(formulario);
        
        // DEBUG: Mostrar los datos que se están enviando
        console.log('Datos enviados:');
        console.log('ID Item:', id_item);
        console.log('Formato:', formato);
        console.log('Cantidad:', cantidad);
        console.log('Tiempo:', tiempo);
        console.log('Costo:', costo);
        
        // Confirmar antes de enviar
        Swal.fire({
            title: '¿Confirmar agregar item?',
            html: `
                <strong>Cantidad:</strong> ${cantidad} items<br>
                <strong>Formato:</strong> ${formato}<br>
                <strong>Tiempo:</strong> ${tiempo}<br>
                <strong>Costo por tiempo:</strong> ${costo}
            `,
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, agregar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                // Enviar datos al servidor
        fetch('<?php echo SERVERURL; ?>ajax/prestamoAjax.php', {
            method: 'POST',
            body: datos
        })
        .then(respuesta => respuesta.json())
        .then(respuesta => {
            if (respuesta.Alerta === "simple") {
                if (respuesta.Tipo === "success") {
                    // Cerrar modal y limpiar formulario
                    $('#ModalAgregarItem').modal('hide');
                    formulario.reset();
                    
                    // Recargar la página para mostrar el item agregado
                    location.reload();
                }
            }
            return alertas_ajax(respuesta);
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        });
    }

    /*----------  Modales del item  ----------*/
    function modal_agregar_item(id){
        $('#ModalItem').modal('hide');
        $('#ModalAgregarItem').modal('show');
        document.querySelector('#id_agregar_item').setAttribute("value", id);
    }

    function modal_buscar_item(){
        $('#ModalAgregarItem').modal('hide');
        $('#ModalItem').modal('show');
    }
</script>