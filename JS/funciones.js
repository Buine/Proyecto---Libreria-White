var det = false;
var gen = false;
var inf;
var idf;
var cods = [];
var titles = [];

function editar(cell, row){
	var info = prompt('Valor a ingresar', cell.innerText);
	if(info != null){
		if(info != "" && !isNaN(info)){
			cell.innerText = info;
			var can = document.getElementById("can"+row).innerText;
			var ent = document.getElementById("ent"+row).innerText;
			var sal = document.getElementById("sal"+row).innerText;
			var stock = parseInt(can);
			if (ent != "(Editar)"){ stock += parseInt(ent); }
			if (sal != "(Editar)"){	stock -= parseInt(sal); }
			document.getElementById("sto"+row).innerHTML = stock;
		} else {
			alert("No se realizo el cambio, verifica el valor a ingresar")
		}
	}
}

window.onload = function(){
	document.getElementById("gen").style.display = "none";
	document.getElementById("addBook").style.display = "none";
}

window.onbeforeunload = function(e) {
	if(gen){
		var dialogText = 'Al recargar la pagina no se guardara el inventario';
		e.returnValue = dialogText;
		return dialogText;
	}
};

function deleteInv(id){
	if(gen){ alert("No se puede eliminar inventario hasta terminar de generar el inventario") }
	else if(confirm("Estas seguro de eliminar este registro de inventario?")){
		$.ajax({
			type: "POST",
			url: "/proyecto/PHP/cargarInventario.php",
			data:{delete:id},
			success:function(data){
				var d = data.split("|$|");
				document.getElementById("p").innerHTML = d[0];
				document.getElementById("locals").innerHTML = d[1];
			},
			error:function(){
				alert("Sucedio un error al intentar eliminar el inventario");
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
		error:function(){
			alert("Sucedio un error al intentar cargar los detalles");
		}
	});
}

function generateInv(){
	if(gen){ alert("Actualmente se está generando un inventario"); return; }
	var select = document.getElementById("locals");
	var option = "";
	if(select.value == 'default'){
		alert("Eligue una direccion, o elige crear una");
		return;
	} else if (select.value == 'l'){
		option = select.options[select.selectedIndex].innerText;
		var dir = option.split(", ");
		document.getElementById("dir1").value = dir[0];
		document.getElementById("dir2").value = dir[1];
		document.getElementById("dir3").value = dir[2];
		document.getElementById("dir1").disabled = true;
		document.getElementById("dir2").disabled = true;
		document.getElementById("dir3").disabled = true;
	} else { option = "$%&"; }
	$.ajax({
		type: 'POST',
		url: "/proyecto/PHP/cargarGenerar.php",
		data: {local:option},
		success:function(data){
			//Aqui acción <-
			var d = data.split("|$|");
			idf = d[1];
			console.log(d[0]);
			document.getElementById("id").value = d[0];
			document.getElementById("id").disabled = true;
			document.getElementById("gen").style.display = "block";
			window.location.href = "#gen";
			document.getElementById("a").insertAdjacentHTML('beforeend', d[2]);
			if(d[3] != undefined){
				cods = d[3].split("*-*");
				titles = d[4].split("*-*");
			}
			gen = true;
		},
		error:function(){
			alert("Sucedio un error al intentar generar el inventario\nIntentelo más tarde");
		}
	})
}

function addBook(){
	document.getElementById("addBook").style.display = "block";
	alertify.genericDialog || alertify.dialog('genericDialog',function(){
    return {
        main:function(content){
            this.setContent(content);
        },
        setup:function(){
            return {
                focus:{
                    element:function(){
                        return this.elements.body.querySelector(this.get('selector'));
                    },
                    select:true
                },
                options:{
                    basic:true,
                    maximizable:false,
                    resizable:false,
                    padding:false
                }
            };
        },
        settings:{
            selector:undefined
        }
    };
});
alertify.genericDialog ($('#addBook')[0]).set('selector', 'input[type="number"]');
}

function vAddBook(cod,tit,aut,gen,pre,cos){
	if(cod.value != "" && tit != "" && aut != "" && gen != "" && pre != "" && cos != ""){
		if(!cods.includes(cod.value)){
			var row = document.getElementById("a").getElementsByClassName("row").length-1;
			var addhtml = 	`<div class="row">
								<div class="cell" data-title="Codigo" id="cod`+row+`">
									`+cod.value+`
								</div>
								<div class="cell" data-title="Titulo" id="tit`+row+`">
									`+tit.value+`
								</div>
								<div class="cell" data-title="Autor" id="aut`+row+`">
									`+aut.value+`
								</div>
								<div class="cell" data-title="Generos" id="gen`+row+`">
									`+gen.value+`
								</div>
								<div class="cell" data-title="Cantidad" id="can`+row+`">
									 0
								</div>
								<div class="cell" data-title="Entradas" id="ent`+row+`">
									<a onClick="editar(this,`+row+`)">(Editar)</a>
								</div>
								<div class="cell" data-title="Salidas" id="sal`+row+`">
									<a onClick="editar(this,`+row+`)">(Editar)</a>
								</div>
								<div class="cell" data-title="Precio" id="pre`+row+`">
									<a onClick="editar(this,`+row+`)">`+pre.value+` (Editar)</a>
								</div>
								<div class="cell" data-title=Costo id="cos`+row+`">
									<a onClick="editar(this,`+row+`)">`+cos.value+` (Editar)</a>
								</div>
								<div class="cell" data-title=Stock id="sto`+row+`">
									0
								</div>
							</div>`;
			document.getElementById("a").insertAdjacentHTML('beforeend', addhtml);
			alertify.closeAll();
			cod.value = "";
			tit.value = "";
			aut.value = "";
			gen.value = "";
			pre.value = "";
			cos.value = "";
		} else { alert("El codigo ya está en uso.\nCodigo: "+cod.value+", Titulo: "+titles[cods.indexOf(cod.value)]); }
	} else { 
		alert("Rellena todos los campos para continuar");	
	}
}

function sendInventario(table, fecha, dir1, dir2, dir3){
	var idinv = document.getElementById("id");
	if(idinv.value != "" && fecha.value != "" && dir1.value != "" && dir2.value != "" && dir3.value != ""){
		var numrow = table.getElementsByClassName("row").length-1;
		var json = '{"id" : "'+idinv.value+'", "fecha" : "'+fecha.value+'", "direccion" : { "ciudad" : "'+dir1.value+'", "barrio" : "'+dir2.value+'", "calle" : "'+dir3.value+'"} ,"libros" : [';
		for(var i = 0; i < numrow; i++){
			var cells = table.getElementsByClassName("row")[i+1].getElementsByClassName("cell")
			if(!isNaN(cells[5].innerText) && !isNaN(cells[6].innerText) && !isNaN((cells[7].innerText.split(" "))[0]) && !isNaN((cells[8].innerText.split(" "))[0])){
				var genres = cells[3].innerText.split(", ");
				json = json+'{ "codigo" : "'+cells[0].innerText+'", "titulo" : "'+cells[1].innerText+'", "autor" : "'+cells[2].innerText+'", "genero" : ["'+genres[0]+'"';
				for(var j = 1; j < genres.length; j++){
					json = json+', "'+genres[j]+'"';
				}
				json = json+'], "cantidad" : "'+cells[4].innerText+'", "entradas" : "'+cells[5].innerText+'", "salidas" : "'+cells[6].innerText+'", "precio" : "'+(cells[7].innerText.split(" "))[0]+'", "costo" : "'+(cells[8].innerText.split(" "))[0]+'" }';
				if(i+1 != numrow){ json = json+", "; }
			} else {
				alert("Debes editar obligatoriamente los campos 'entradas' y 'salidas'");
				return;
			}
			
		}
		var obs = prompt("Ingrese una observación");
		if(obs != ""){ json = json+'], "observaciones" : "'+obs+'" }'; }
		else { json = json+'], "observaciones" : "" }'; }
		console.log(json);
		$.ajax({
			type: "POST",
			url: "/proyecto/PHP/generarInventario.php",
			data:{generar:json},
			success:function(){
				//Accion aqui!
				alert("Se genero correctamente la orden de compra");
				gen = false;
				location.reload();
			},
			error:function(data){
				alert("Sucedio al intentar generar el inventario, intentelo despues");
				alert(data);
			}
		});
	} else {
		alert("Debes rellenar todos los campos");
		return;
	}
}