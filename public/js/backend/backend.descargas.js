var backend = backend || {}

backend.descargas = {

	saveSector: function() {
		$("#sectorform").ajaxForm({
               success:function(rta){
               	console.log(rta);
                   rta = JSON.parse(rta);
                   if(rta.error) {
                        alert(rta.error);
                        return;
                   }
                    alert(rta.msg);
					document.location.reload();

                }
             }).submit();
	},
	saveSeccion: function() {

		$("#seccionform").ajaxForm({
			success:function(rta){
				
				// console.log(rta);
				rta = JSON.parse(rta);
				if(rta.error) {
					alert(rta.error);
					return;
				}
				alert(rta.msg);
				document.location.reload();

			}
		}).submit();

	},

	saveArchivo: function() {

		$("#productoform").ajaxForm({
			success:function(rta){
				
				// console.log(rta);
				rta = JSON.parse(rta);
				if(rta.error) {
					alert(rta.error);
					return;
				}
				alert(rta.msg);
				document.location.reload();
			}
		}).submit();
	},

	editSector: function(sid) {
		$('#sectorform')[0].reset();

		if(sid){
			$.post("/backend/getsector/",{"id":sid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				$("#sector_id").val(response.sector.id);
				$("#sector_nombre").val(response.sector.nombre);
				$("#editorsector").show();
			}, 'json');
		}else{
			$("#editorsector").show();
		}
	},

	editSeccion: function(sid) {
		
		$('#seccionform')[0].reset();

		if(sid){
			$.post("/backend/getsecciondescargas/",{"id":sid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				$("#seccion_id").val(response.seccion.id);
				$("#seccion_nombre").val(response.seccion.nombre);
				$("#seccion_id_categoria").val(response.seccion.id_categoria);
				$("#editorseccion").show();
			}, 'json');
		}else{
			$("#editorseccion").show();
		}
	},

	editArchivo: function(pid) {
		
		$('#productoform')[0].reset();
		backend.descargas.selectSeccion('producto_id_categoria','producto_id_seccion');
		if(pid){
			$.post("/backend/getarchivo/",{"id":pid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				$("#producto_id").val(response.archivo.id);
				$("#producto_nombre").val(response.archivo.nombre);
				$("#producto_id_categoria").val(response.archivo.id_categoria);
				$("#producto_id_seccion").val(response.archivo.id_seccion);
				$("#producto_descripcion").val(response.archivo.descripcion);
				$('#vista').attr("src",response.archivo.url_img);

				$("#editorproducto").show();
			}, 'json');
		}else{
			$("#editorproducto").show();
		}
	},


	delSeccion: function(sid) {
		if(sid == undefined) return;

		if(confirm("Esta seguro que desea eliminar la Seccion ?")) {
			$.post("/backend/eliminarsecciondescargas/",{"id":sid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				alert(response.msg);
				document.location.reload();
			}, 'json');
		}
	},
	delArchivo: function(pid) {
	
		if(pid == undefined) return;

		if(confirm("Esta seguro que desea eliminar el Archivo?")) {
			$.post("/backend/eliminararchivo/",{"id":pid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				alert(response.msg);
				document.location.reload();
			}, 'json');
		}
	},

	addImage: function(e) {
	      var file = e.target.files[0],
	      imageType = /image.*/;
		  var n = e.currentTarget.id;
	      if (!file.type.match(imageType))
	      	return;
	  
	      var reader = new FileReader();
	      reader.onload = function(event) {
	    	  $('#vista').attr("src", event.target.result);
	      };
	      reader.readAsDataURL(file);
    },

	getSecciones: function() {
		
		var cid = $("#categoria").val();
		// alert(cid);
		$("#lst_secciones").html("");
		$.post("/backend/getseccionesdescargas", {"id":cid}, function(response){
			if(response.error) { alert(response.error); return; }
			if(response.secciones == false) return;
			// alert(response.total_pages);
			var html = "";
			for(var i=0;i<response.secciones.length;i++){
				html+= "<tr draggable='true' id='novedad_"+response.secciones[i].id+"'><td>"+response.secciones[i].nombre+"</td>";
                html+= '<td><a href="#form" onclick="backend.descargas.editSeccion('+response.secciones[i].id+')">Editar</a></td>';
                // html+= '<td><a href="#" onclick="backend.descargas.delSeccion('+response.secciones[i].id+')">Eliminar</a></td></tr>';
                html+= '</tr>';
			}
			$("#lst_secciones").html(html);

		}, "json");

	},
    selectSeccion: function(categoria_eid, seccion_eid, callback) {
		
		var sid = $("#"+categoria_eid).val();
		$("#"+seccion_eid).html("");
		$.post("/backend/getseccionesdescargas", {"id":sid}, function(response){
			if(response.error) { return; }
			if(response.secciones == false) return;
			var html = "";
			for(var i=0;i<response.secciones.length;i++){
				html+= '<option value="'+response.secciones[i].id+'">'+response.secciones[i].nombre+'</option>'; 
			}
			$("#"+seccion_eid).html(html);
			if(callback != undefined) {
				callback();
			}

		}, "json");
	}, 
	getArchivos: function() {
	
		var sid = $("#secciones").val();
		$("#lst_productos").html("");
		$.post("/backend/getarchivos", {"id":sid}, function(response){
			if(response.error) { alert(response.error); return; }
			if(response.archivos == false) return;
			var html = "";
			for(var i=0;i<response.archivos.length;i++){
				html+= "<tr draggable='true' id='novedad_"+response.archivos[i].id+"'><td>"+response.archivos[i].nombre+"</td>";
                html+= '<td><a href="#form" onclick="backend.descargas.editArchivo('+response.archivos[i].id+')">Editar</a></td>';
                html+= '<td><a href="#" onclick="backend.descargas.delArchivo('+response.archivos[i].id+')">Eliminar</a></td></tr>';
			}
			$("#lst_productos").html(html);

		}, "json");
	}
    
}

