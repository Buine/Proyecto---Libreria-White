<?php  
	if($_POST['delete']){
		$dbconn = pg_connect("host=localhost dbname=libreria user=postgres password=1234")
		or die('No se ha podido conectar: ' . pg_last_error());
		$query = "DELETE FROM inventario WHERE inv_id =".($_POST['delete']);
		$result = pg_query($dbconn, $query);
		recharge($dbconn);
	}
	
	function recharge($dbconn){
		$query = "SELECT * FROM inventario";
			$result	= pg_query($dbconn, $query) or die ("Error con la consulta");
			$nr = pg_num_rows($result);
			$html = '
				<div class="row header">
					  <div class="cell" data-title="Id">
						ID
					  </div>
					  <div class="cell" data-title="Fecha">
						Fecha
					  </div>
					  <div class="cell" data-title="Dirección">
						Direccion
					  </div>
					  <div class="cell" data-title="Observaciones">
						Observaciones
					  </div>
					  <div class="cell" data-title="Acciones">
						Acciones
					  </div>
				</div>';
			for($i = 0;$i < $nr; $i++){
				$row = pg_fetch_array($result, $i);
				$info = json_decode($row["inv_json"]);
				$dir = $info->direccion;
				$html = $html.'
				<div class="row">
					  <div class="cell">
						'.($info->id).'
					  </div>
					  <div class="cell">
						'.($info->fecha).'
					  </div>
					  <div class="cell">
						'.($dir->ciudad).', '.($dir->barrio).', '.($dir->calle).'
					  </div>
					  <div class="cell">
						'.($info->observaciones).'
					  </div>
					  <div class="cell">
						<a onClick="deleteInv('.($row["inv_id"]).')">Eliminar</a>
						<a onClick="details('.($row["inv_id"]).')">Detalles</a>
					  </div>
				</div>';
			}
		cargarLocales($dbconn,$html);
	}

	function cargarLocales($dbconn, $html){
		$html = $html.'|$|
		<option selected value="default">Selecciona una dirección</option>';
		$query = "SELECT DISTINCT(UPPER(dir))
						FROM (SELECT ((inv_json::json -> 'direccion' ->> 'ciudad') || ', ' || (inv_json::json -> 'direccion' ->> 'barrio') || ', ' || (inv_json::json -> 'direccion' ->> 'calle')) as dir
						FROM inventario) as direcciones";
			$result = pg_query($dbconn, $query);
			$nr = pg_num_rows($result);
			for($i = 0; $i < $nr; $i++){
				$row = pg_fetch_array($result, $i);
				$html = $html.'<option>'.$row[0].'</option>';	
			}
		$html = $html.'<option value="new">Crear una nueva dirección</option>';
		echo $html;
	}
?>