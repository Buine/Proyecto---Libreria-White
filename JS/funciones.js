var det = false;


function deleteInv(id){
	if(confirm("Estas seguro de eliminar este registro de inventario?")){
		$.ajax({
			type: "POST",
			url: "/proyecto/PHP/cargarInventario.php",
			data:{delete:id},
			success:function(data){
				document.getElementById("p").innerHTML = data;
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
				var body = document.getElementById("b");
				body.insertAdjacentHTML('beforeend', html);
				det = true;
			} else {
				document.getElementById("preview").innerHTML = data;
			}
		},
		error:function(data){
			alert(data);
		}
	});
}
