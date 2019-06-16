
function deleteInv(id){
	if(confirm("Estas seguro de eliminar este registro de inventario?")){
		$.ajax({
			type: "POST",
			url: "/proyecto/PHP/funcionesPhp.php",
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