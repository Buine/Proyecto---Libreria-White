var det = false;

function deleteInv(id){
	if(confirm("Estas seguro de eliminar este registro de inventario?")){
		$.ajax({
			type: "POST",
			url: "/proyecto/PHP/cargarInventario.php",
			data:{delete:id},
			success:function(data){
				var d = data.split("|$|");
				document.getElementById("p").innerHTML = d[0];
				document.getElementById("locals").innerHTML = d[1];
			},
			error:function(data){
				alert(data);
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
		error:function(data){
			alert(data);
		}
	});
}
