/**
* Archivo de funciones para la subida de imagenes en hyperform
*
* $id$
*/

/**
* Metodo que visualiza el input file para subir un archivo
* @param name: nombre del campo
*/
enable_upload_file = function(name){
	if(name){
		var inputUploadFile = $(name+'_span');
		if(inputUploadFile){
			inputUploadFile.show();
		}
	}
}