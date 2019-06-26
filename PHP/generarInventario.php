<?php
	if($_POST['generar']){
		$connection = pg_connect("host=localhost port=5432 dbname=libreria user=postgres password=1234")
			or die("Error al conectar");
		$query = "INSERT INTO inventario(inv_json) VALUES ('".($_POST['generar'])."')";
		$result = pg_query($connection, $query);
	}
?>