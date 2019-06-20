var det = false;
var gen = false;

window.onbeforeunload = function(e) {
	if(gen){
		var dialogText = 'Al recargar la pagina no se guardara el inventario';
		e.returnValue = dialogText;
		return dialogText;
	}
};

function deleteInv(id){
	if(gen){ alert("No se puede eliminar inventario hasta terminar de generar el inventario") }
	else if(confirm("Estas seguro de eliminar este registro de inventario?")){
		$.ajax({
			type: "POST",
			url: "/proyecto/PHP/cargarInventario.php",
			data:{delete:id},
			success:function(data){
				var d = data.split("|$|");
				document.getElementById("p").innerHTML = d[0];
				document.getElementById("locals").innerHTML = d[1];
			},
			error:function(){
				alert("Sucedio un error al intentar eliminar el inventario");
			}
		});
	}
}

function details(id){
	$.ajax({
		type: 'POST',
		url: "/proyecto/PHP/cargarDetalles.php",
		data: {details:id},
		success:function(data){
			if(!det){
				var html = '<div class="preview" id="preview">'+data+'</div>';
				document.getElementById("det").innerHTML = html;
				det = true;
			} else {
				document.getElementById("preview").innerHTML = data;
			}
			window.location.href = "#preview";
		},
		error:function(){
			alert("Sucedio un error al intentar cargar los detalles");
		}
	});
}

function generateInv(){
	var select = document.getElementById("locals");
	var option = "";
	if(select.value == 'default'){
		alert("Eligue un local, o elige crear uno");
		return;
	} else if (select.value == 'l'){
		option = select.options[select.selectedIndex].innerText;
	}
	$.ajax({
		type: 'POST',
		url: "/proyecto/PHP/cargarGenerar.php",
		data: {local:option},
		success:function(data){
			//Aqui acción <-
			gen = true;
		},
		error:function(){
			alert("Sucedio un error al intentar generar el inventario\nIntentelo más tarde");
		}
	})
}
