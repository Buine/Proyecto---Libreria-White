<?php
	if($_POST['local']){
		$dbconn = pg_connect("host=localhost dbname=libreria user=postgres password=1234")
		or die('No se ha podido conectar: ' . pg_last_error());
		$query = "(SELECT (inv_json ->> 'id')::int as id FROM inventario ORDER BY inv_json ->> 'id' DESC LIMIT 1)
					UNION
					(SELECT inv_id as id FROM inventario ORDER BY inv_id DESC LIMIT 1)";
		$result = pg_query($dbconn, $query);
		$nr = pg_num_rows($result);
		if($nr > 0){
			$row = pg_fetch_array($result, 0);
			$row2 = pg_fetch_array($result, 1);
			$html = ((int)$row["id"]+1).'|$|'.((int)$row2["id"]+1).'|$|'; // <---- |$| es un separador para realizar split
		} else {
			$html = "1|$|1|$|";
		}
		
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
						<div class="cell" data-title="Codigo" id="cod'.$i.'">
							'.($row[0]).'
						</div>
						<div class="cell" data-title="Titulo" id="tit'.$i.'">
							'.($row[1]).'
						</div>
						<div class="cell" data-title="Autor" id="aut'.$i.'">
							'.($row[2]).'
						</div>
						<div class="cell" data-title="Generos" id="gen'.$i.'">
							'.$generos.'
						</div>
						<div class="cell" data-title="Cantidad" id="can'.$i.'">
							'.((int)$row[4]+(int)$row[5]-(int)$row[6]).'
						</div>
						<div class="cell" data-title="Entradas" id="ent'.$i.'">
							<a onClick="editar(this,'.$i.')">(Editar)</a>
						</div>
						<div class="cell" data-title="Salidas" id="sal'.$i.'">
							<a onClick="editar(this,'.$i.')">(Editar)</a>
						</div>
						<div class="cell" data-title="Precio" id="pre'.$i.'">
							<a onClick="editar(this,'.$i.')">'.($row[7]).' (Editar)</a>
						</div>
						<div class="cell" data-title=Costo id="cos'.$i.'">
							<a onClick="editar(this,'.$i.')">'.($row[8]).' (Editar)</a>
						</div>
						<div class="cell" data-title=Stock id="sto'.$i.'">
							'.((int)$row[4]+(int)$row[5]-(int)$row[6]).'
						</div>
					</div>';
			}
		}
		//Cargando codigos de libros creados hasta el momento, con sus titulos
		$query = "SELECT DISTINCT(libro.codigo) as cod, libro.titulo as title
					FROM inventario
					CROSS JOIN json_to_recordset(inv_json -> 'libros')
						AS libro(codigo TEXT, titulo TEXT)";
		$result = pg_query($dbconn, $query);
		$nr = pg_num_rows($result);
		if($nr > 0){
			$cod = ""; $title = "";
			for($i = 0; $i < $nr; $i++){
				if ($i == 0){ 
					$row = pg_fetch_array($result, $i);
					$cod = $row[0];
					$title = $row[1];
				} else {
					$row = pg_fetch_array($result, $i);
					$cod = $cod.'*-*'.$row[0];
					$title = $title.'*-*'.$row[1];
				}
			}
			$html = $html.'|$|'.$cod.'|$|'.$title;
		}
		echo $html;
	}
?>