<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Libreria White</title>
	<script src="JS/funciones.js" type="text/javascript"></script>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=yes">
	<link rel="shortcut icon" type="image/png" href="IMG/favicon.png"/>
	<link href="https://fonts.googleapis.com/css?family=Oswald&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed|Roboto:400,400i&display=swap" rel="stylesheet">
	<link href="//db.onlinewebfonts.com/c/021120d820562daab169a2337ab13040?family=Helvetica+Neue" rel="stylesheet">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="CSS/styles.css">
</head>
	<header class="header">
		<div class="top">
			<h2>LIBRERIA</h2>
			<h1 class="shadow">WHITE</h1>
			<h1>WHITE</h1>
		</div>
		<div class="center">
			<div class="texts">
				<h3 class="stroke">INVENTARIO DE LIBROS</h3>
				<h3>INVENTARIO DE LIBROS</h3>
				<h3 class="stroke">INVENTARIO DE LIBROS</h3>
			</div>
		</div>
	</header>	
	
<body>
	<div class="list">
		<h4>LISTA</h4>
		<h5>DE INVENTARIOS</h5>
		<div class="table" id="p" hidden="true">

				<div class="row header">
					  <div class="cell">
						ID
					  </div>
					  <div class="cell">
						Fecha
					  </div>
					  <div class="cell">
						Direccion
					  </div>
					  <div class="cell">
						Observaciones
					  </div>
					  <div class="cell">
						Acciones
					  </div>
				</div>
			
			<?php
			$dbconn = pg_connect("host=localhost dbname=libreria user=postgres password=1234")
    		or die('No se ha podido conectar: ' . pg_last_error());
			$query = "SELECT * FROM inventario";
			$result	= pg_query($dbconn, $query) or die ("Error con la consulta");
			$nr = pg_num_rows($result);
			for($i = 0;$i < $nr; $i++){
				$row = pg_fetch_array($result, $i);
				$info = json_decode($row["inv_json"]);
				$dir = $info->direccion;
			?>
				<div class="row">
					  <div class="cell">
						<?= $info->id ?>
					  </div>
					  <div class="cell">
						<?= $info->fecha ?>
					  </div>
					  <div class="cell">
						<?= $dir->ciudad ?>, <?= $dir->barrio ?>, <?= $dir->calle ?>
					  </div>
					  <div class="cell">
						<?= $info->observaciones ?>
					  </div>
					  <div class="cell">
						<a onClick="deleteInv(<?= $row["inv_id"] ?>)">Eliminar</a>
					  </div>
				</div>
			<?php
			}
			?>

		</div>
		<button type="button" class="newInventario">Generar un nuevo inventario</button>
	</div>
</body>
</html>