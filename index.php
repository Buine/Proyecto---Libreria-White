<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Libreria White</title>
	<link rel="stylesheet" href="css/styles.css">
	<script src="JS/funciones.js" type="text/javascript"></script>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=yes">
	<link rel="shortcut icon" type="image/png" href="IMG/favicon.png"/>
	<link href="https://fonts.googleapis.com/css?family=Oswald&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed|Roboto:400,400i&display=swap" rel="stylesheet">
	<link href="//db.onlinewebfonts.com/c/021120d820562daab169a2337ab13040?family=Helvetica+Neue" rel="stylesheet">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.4/build/alertify.min.js"></script>
	<!-- CSS -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.4/build/css/alertify.min.css"/>
	<!-- Default theme -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.4/build/css/themes/default.min.css"/>
	
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
	
<body id="b">
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
					  <div class="cell" data-title="Id">
						<?= $info->id ?>
					  </div>
					  <div class="cell" data-title="Fecha">
						<?= $info->fecha ?>
					  </div>
					  <div class="cell" data-title="Dirección">
						<?= strtoupper($dir->ciudad) ?>, <?= strtoupper($dir->barrio) ?>, <?= strtoupper($dir->calle) ?>
					  </div>
					  <div class="cell" data-title="Observación">
						<?= $info->observaciones ?>
					  </div>
					  <div class="cell" data-title="Acciones">
						<a onClick="deleteInv(<?= $row["inv_id"] ?>)">Eliminar</a>
						<a onClick="details(<?= $row["inv_id"] ?>)">Detalles</a>
					  </div>
				</div>
			<?php
			}
			?>

		</div>
		<div class="buttons">
			<select class="locals" id="locals">
				<option selected value="default">Selecciona una dirección</option>
				<?php 
				$query = "SELECT DISTINCT(UPPER(dir))
							FROM (SELECT ((inv_json::json -> 'direccion' ->> 'ciudad') || ', ' || (inv_json::json -> 'direccion' ->> 'barrio') || ', ' || (inv_json::json -> 'direccion' ->> 'calle')) as dir
							FROM inventario) as direcciones";
				$result = pg_query($dbconn, $query);
				$nr = pg_num_rows($result);
				for($i = 0; $i < $nr; $i++){
					$row = pg_fetch_array($result, $i);
					?>
					<option value="l"><?= $row[0] ?></option>
				<?php
				}
				?>
				<option value="new">Crear una nueva dirección</option>
			</select>
			<button type="button" class="s_button" onClick="generateInv()">Generar un nuevo inventario</button>
		</div>
	</div>
    <!-- Aqui se inserta el preview | det = detalles -->
	<div id="det">
	
	</div>
	<!-- Aqui se inserta el generar | gen = generar -->
	<div id="gen" class="gen">
		<h4>GENERANDO</h4>
		<h5>INFORMACIÓN GENERAL</h5>
		<div class="window">
			<div class="head"></div>
			<fieldset class="x">
					<label for="id">ID del Inventario</label>
					<input type="number" required="required" class="input" id="id" placeholder="ID">
					<label for="fecha" class="f">Fecha</label>
					<input type="date" required="required" class="input" id="fecha">
			</fieldset>
			<fieldset class="y">
					<label for="dir1">Dirección</label>
					<input type="text" required="required" class="input2" id="dir1" placeholder="Ciudad">
					<input type="text" required="required" class="input2" id="dir2" placeholder="Barrio">
					<input type="text" required="required" class="input2" id="dir3" placeholder="Calle">
			</fieldset>
			<div class="head"></div>
			<div class="table" id="a">
				<div class="row header">
					<div class="cell">
						Codigo
					</div>
					<div class="cell">
						Titulo
					</div>
					<div class="cell">
						Autor
					</div>
					<div class="cell">
						Genero
					</div>
					<div class="cell">
						Cantidad
					</div>
					<div class="cell">
						Entradas*
					</div>
					<div class="cell">
						Salidas*
					</div>
					<div class="cell">
						Precio*
					</div>
					<div class="cell">
						Costo*
					</div>
					<div class="cell">
						Stock
					</div>
				</div>
			</div>
			<h6>Los campos con * pueden ser ser editados</h6>
			<button type="button" class="s_button" onClick="sendInventario(a,fecha,dir1,dir2,dir3)">Generar Inventario</button>
			<button type="button" class="s_button b" onClick="addBook()">Añadir un nuevo libro</button>
		</div>
	</div>
</body>
<form id="addBook" class="addBook">
	<h4>AGREGAR LIBRO</h4>
	<h5>VERIFICA TODOS LOS CAMPOS</h5>
    <fieldset>
        <input type="number" placeholder="Codigo" id="cod"/> 
        <input type="text" placeholder="Titulo" id="tit"/>
		<input type="text" placeholder="Autor" id="aut"/>
		<input type="text" placeholder="Generos" id="gen"/>
		<input type="number" placeholder="Precio" id="pre"/>
		<input type="number" placeholder="Costo" id="cos"/> 

		<button type="button" class="s_button" onClick="vAddBook(cod,tit,aut,gen,pre,cos)">Añadir libro</button>
    </fieldset>
</form>
</html>