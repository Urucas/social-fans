var backend = backend || {}

backend.novedades = {

	saveNov: function() {
		var editor_data   = CKEDITOR.instances.ckeditor_contenido.getData();
		$("#nov_con").val(editor_data);
		var fecha =$("#nov_fecha_anio").val()+"-"+$("#nov_fecha_mes").val()+"-"+$("#nov_fecha_dia").val();
		$("#nov_fec").val(fecha);		

		$("#novform").ajaxForm({
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

	dellImg: function(id,nid) {
		if(id == undefined) return;

		if(confirm("¿Esta seguro que desea eliminar la imagen ?")) {
			$.post("/backend/eliminarimgnov/",{"id":id}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				alert(response.msg);

			}, 'json');
					backend.novedades.editNov(nid);

		}
		// alert(oid);

	},

	editNov: function(nid) {
		$('#novform')[0].reset();
		CKEDITOR.instances.ckeditor_contenido.setData("");
		$("#lst_imgs").html("");
		$("#lst_input_img").html("");
		
		if(nid){
			$.post("/backend/getcontenidonovedad/",{"nid":nid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
					$("#nov_id").val(response.novedad.id);		
					fecha=response.novedad.fecha.split("-");
					$("#nov_fecha_anio").val(fecha[0]);
					$("#nov_fecha_mes").val(parseInt(fecha[1]));
					$("#nov_fecha_dia").val(parseInt(fecha[2]));
					$("#nov_tit").val(response.novedad.titulo);
					$('#vista').attr("src","/uploadsfotos/novedades/novedad_"+response.novedad.id+".jpg");
					CKEDITOR.instances.ckeditor_contenido.setData(response.novedad.contenido);
					console.log(response.novedad);
					for (i = 1; i < response.imagenes.length+1; i++) {
						    $("#lst_imgs").append('<img src="'+response.imagenes[i-1].url_img+'" border="0" width="70" height="70" name="vista_'+i+'" id="vista_'+i+'" alt="Imagen Novedad"><a onclick="backend.novedades.dellImg('+response.imagenes[i-1].id+","+response.imagenes[i-1].id_novedad+')" name="del_img_" id="del_img_"><b> &nbsp;&nbsp; Eliminar Imagen</b></a><br>');

					}

					$("#editornov").show();
			}, 'json');
		}else{
			var today = new Date();
			var dia = today.getDate();
			var mes = today.getMonth();
			var anio = today.getFullYear();
			$("#nov_fecha_anio").val(anio);
			$("#nov_fecha_mes").val(mes+1);
			$("#nov_fecha_dia").val(dia);
			$("#editornov").show();
		}

	

	},
	dellNov: function(nid) {
		if(nid == undefined) return;

		if(confirm("Esta seguro que desea eliminar el articulo ?")) {
			$.post("/backend/eliminarnovedad/",{"id":nid}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				alert(response.msg);
				$("#editornov").hide();
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

    agregarimg: function() {
    	var n = $("input[type='file']").length;

    	$("#lst_input_img").append('<input type="file" name="imagen_'+(n+1)+'" id="imagen_'+(n+1)+'" />');


    },

	cargarContenido: function(pa) {
		$.post("/backend/getnovedades", {pa:pa}, function(response){
			if(response.error) { alert(response.error); return; }
			if(response.novedades == false) return;
			var html = "";
			for(var i=0;i<response.novedades.length;i++){
						html+= "<tr draggable='true' id='novedad_"+response.novedades[i].id+"'><td>"+response.novedades[i].titulo+"</td>";
                        html+= "<td>"+response.novedades[i].fecha+"</td>";
                        html+= '<td><a href="#form" onclick="backend.novedades.editNov('+response.novedades[i].id+')">Editar</a></td>';
                        html+= '<td><a href="#" onclick="backend.novedades.dellNov('+response.novedades[i].id+')">Eliminar</a></td></tr>';
					}
			var htmlp='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.novedades.cargarContenido(1)">« Primero</a>';
			for(var p=0;p<response.total_pages;p++){
				if(pa==(p+1)){
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.novedades.cargarContenido('+(p+1)+')"><b>'+(p+1)+'</b></a>';
				}else{
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.novedades.cargarContenido('+(p+1)+')">'+(p+1)+'</a>';
				}
			}
			htmlp+='<a  href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.novedades.cargarContenido('+response.total_pages+')">Ultimo »</a>'
			$("#lst_nov").html(html);
			$("#pagination").html(htmlp);

		}, "json");
	}
}

$(document).ready(function(){
	CKEDITOR.on('instanceReady', function(){
		backend.novedades.cargarContenido();
	});
});

