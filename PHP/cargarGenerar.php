<?php
	if($_POST['local']){
		$dbconn = pg_connect("host=localhost dbname=libreria user=postgres password=1234")
		or die('No se ha podido conectar: ' . pg_last_error());
		$query = "SELECT inv_id FROM inventario ORDER BY inv_id DESC LIMIT 1";
		$result = pg_query($dbconn, $query);
		$row = pg_fetch_array($result, 0);
		$html = ((int)$row["inv_id"]+1).'|$|'; // <---- |$| es un separador para realizar split
		
		if($_POST['local'] != "$%&"){ //<--- Se eligio una direccion existente
			$query = "SELECT *, row_number() over(order by (SELECT 'sin_ordernar')::text)::int-1 as pos_vector
						FROM json_populate_recordset(null::record, (SELECT inv_json FROM inventario 
						WHERE inv_id =
						
						(SELECT id
						FROM
						(SELECT inv_id as id, (inv_json ->> 'fecha')::date as fecha, ((inv_json::json -> 'direccion' ->> 'ciudad') || ', ' || (inv_json::json -> 'direccion' ->> 'barrio') || ', ' || (inv_json::json -> 'direccion' ->> 'calle')) as dir
						FROM inventario
						ORDER BY fecha DESC, id DESC) as inv_table
						WHERE UPPER(dir) = '".($_POST['local'])."' LIMIT 1)

						)::json -> 'libros') 
						as (codigo varchar, titulo varchar, autor varchar, genero varchar, cantidad int, entradas int, salidas int, precio float, costo float)";
			$result = pg_query($dbconn, $query);
			$nr = $nr = pg_num_rows($result);
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
							'.((int)$row[4]+(int)$row[5]-(int)$row[6]).'
						</div>
						<div class="cell" data-title="Entradas">
							Editar
						</div>
						<div class="cell" data-title="Salidas">
							Editar
						</div>
						<div class="cell" data-title="Precio">
							'.($row[7]).'
						</div>
						<div class="cell" data-title=Costo>
							'.($row[8]).'
						</div>
						<div class="cell" data-title=Stock>
							'.((int)$row[4]+(int)$row[5]-(int)$row[6]).'
						</div>
					</div>';
			}
		}
		echo $html;
	}
?>