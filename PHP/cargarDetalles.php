<?php
if($_POST['details']){
	$connection = pg_connect("host=localhost port=5432 dbname=libreria user=postgres password=1234")
		or die("Error al conectar");
	details($connection, $_POST['details']);
}

function details($connection, $id){
	$query = "SELECT *, row_number() over(order by (SELECT 'sin_ordernar')::text)::int-1 as pos_vector
			FROM json_populate_recordset(null::record, (SELECT inv_json FROM inventario WHERE inv_id = ".$id.")::json -> 'libros') 
			as (codigo varchar, titulo varchar, autor varchar, genero varchar, cantidad int, entradas int, salidas int, precio float, costo float)";
	$result = pg_query($connection, $query);
	$nr = pg_num_rows($result);
	$html = '
		<div class="detalles">
			<h4>DETALLES</h4>
			<h5>DE INVENTARIO</h5>
			<div class="table" id="d">
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
						Entradas
					</div>
					<div class="cell">
						Salidas
					</div>
					<div class="cell">
						Precio
					</div>
					<div class="cell">
						Costo
					</div>
				</div>';
	for($i = 0; $i < $nr; $i++){
		$row = pg_fetch_array($result, $i);
		$gen = json_decode($row[3]);
		$generos = "";
		for($j = 0; $j < sizeof($gen); $j++){ 
			if(!empty($generos)){
				$generos = $generos.', '.$gen[$j];
			} else { 
				$generos = $gen[$j]; 
			} 
		}
		$html = $html.'
			<div class="row">
				<div class="cell" data-title="Codigo">
					'.($row[0]).'
				</div>
				<div class="cell" data-title="Titulo">
					'.($row[1]).'
				</div>
				<div class="cell" data-title="Autor">
					'.($row[2]).'
				</div>
				<div class="cell" data-title="Generos">
					'.$generos.'
				</div>
				<div class="cell" data-title="Cantidad">
					'.($row[4]).'
				</div>
				<div class="cell" data-title="Entradas">
					'.($row[5]).'
				</div>
				<div class="cell" data-title="Salidas">
					'.($row[6]).'
				</div>
				<div class="cell" data-title="Precio">
					'.($row[7]).'
				</div>
				<div class="cell" data-title=Costo>
					'.($row[8]).'
				</div>
			</div>';
	}
	$html = $html.'
		</div>';
	echo $html;
}
?>