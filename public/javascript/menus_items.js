document.addEventListener('DOMContentLoaded', function() {
    
    const btnadicionar = document.getElementById('adiciona');

    // Añade un evento de clic al botón
    btnadicionar.addEventListener('click', function() {

        var tipo_impuesto = document.querySelector('#impue_default').value;
        var porce_iva_default = document.querySelector('#porce_iva_default').value;
        var porce_imp_default = document.querySelector('#porce_imp_default').value;
        var input_iva = document.getElementById('flid_porcentaje_iva')
        var input_impo = document.getElementById('flid_porcentaje_impoconsumo')
        input_iva.value = '';
        input_impo.value= '';
        
        switch (tipo_impuesto) {
            case 'IV':
                input_iva.value = porce_iva_default;
                break;
            case 'IM':
                input_impo.value = porce_imp_default;
                break;
            default:
                break;
        }

        var campo = document.getElementById("flid_menus_id");
        campo.focus();

    });

    // Obtenemos el modal
    var modal = document.getElementById("miModal");

    // // Obtenemos el botón para abrir el modal
    // var btnAbrir = document.getElementById("adiciona");

    // Obtenemos el elemento de cierre del modal
    var spanCerrar = document.getElementById("cerrarModal");

    // Cuando el usuario hace clic en el botón, se abre el modal
    // btnAbrir.onclick = function() {
    //     modal.style.display = "block";
    // }

    // Cuando el usuario hace clic en la X, se cierra el modal
    spanCerrar.onclick = function() {
        modal.style.display = "none";
    }

    // Cuando el usuario hace clic fuera del modal, se cierra
    // window.onclick = function(event) {
    //     if (event.target == modal) {
    //         modal.style.display = "none";
    //     }
    // }

    // Manejo del evento de envío del formulario
    document.getElementById("formulario").onsubmit = function(event) {
        event.preventDefault(); // Evita que se envíe el formulario
        var opcionSeleccionada = document.querySelector('input[name="opciones"]:checked');

        if (opcionSeleccionada) {

            let valor_seleccionado = opcionSeleccionada.value;
            let valor_impuesto     = opcionSeleccionada.dataset.value;

            if(valor_seleccionado == 'impoconsumo'){
                document.getElementById('flid_porcentaje_impoconsumo').value = valor_impuesto;
            }
            if(valor_seleccionado == 'iva'){
                document.getElementById('flid_porcentaje_iva').value = valor_impuesto;
            }
            
            modal.style.display = "none"; // Cerrar el modal después de seleccionar una opción

            var campo = document.getElementById("flid_menus_id");
            campo.focus();

        } else {
            alert("Por favor selecciona una opción.");
        }
    }


});