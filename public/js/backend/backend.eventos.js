var backend = backend || {}

backend.eventos = {

	guardar: function() {
		$("#form_evento").ajaxForm({
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

	nuevo: function() {
	
		$('#form_evento')[0].reset();
		$("#user_id").val(0);
		$("#evento_id").val(0);
		$("#user_nombre").removeAttr("disabled");
		$("#editor_evento").show();

	},

	modalUsuarios: function() {
	
		$.post("/backend/getusuariosselectable", {pa:1}, function(response){
			if(response.error) { alert(response.error); return; }
			var html = "";
			$("#modal_usuarios").dialog({
	      		height: 240,
				width:500,
				dialogClass: "no-close",
		      	modal: true
		    });
			$("#list_usuarios").html(response.list);
		},'json');

	}, 

	selectuser: function(uid, unombre) {
		$("#user_id").val(uid);
		$("#user_nombre").val(unombre);
		$("#modal_usuarios").dialog("close");
	},

	editUser: function(eid) {
		$('#form_evento')[0].reset();
		if(!eid) return;
		$.post("/backend/getevento/",{"eid":eid}, function(response){
			if(response.error) {
				alert(response.error);
				return;
			}

			console.log(response.evento);
			var e = response.evento;
			$("#user_id").val(e.id_usuario);
			$("#user_nombre").val(e.nom_usuario);
			$("#user_nombre").attr("disabled","disabled");
			$("#evento_id").val(e.id_evento);
			$("#evento_code").val(e.code);
			$("#evento_nombre").val(e.nom_evento);
			$("#evento_fecha").val(e.fec_evento);
			$("#evento_cant_fotos").val(e.cant_fotos);
			$("#evento_fecha_limite").val(e.fec_limite);
			$("#editor_evento").show();
		}, 'json');
	},

   	cargarContenido: function(pa) {

		$.post("/backend/geteventos", {pa:pa}, function(response){
			if(response.error) { alert(response.error); return; }
			var html = "";
			$("#list_eventos").html(response.list);
			var htmlp='<a href="#" class="paginador-1" style="cursor:pointer;" onclick="backend.eventos.cargarContenido(1)">« Primero</a>';
			for(var p=0;p<response.total_pages;p++){
				if(pa==(p+1)){
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.eventos.cargarContenido('+(p+1)+')"><b>'+(p+1)+'</b></a>';
				}else{
					htmlp+='<a href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.eventos.cargarContenido('+(p+1)+')">'+(p+1)+'</a>';
				}
			}
			htmlp+='<a  href="#" class="paginador-'+(p+1)+'" style="cursor:pointer;" onclick="backend.eventos.cargarContenido('+response.total_pages+')">Ultimo »</a>';
			$("#pagination").html(htmlp);
		}, "json");
	}
}

$(document).ready(function(){
	backend.eventos.cargarContenido();
	$( "#evento_fecha" ).datepicker();
	$( "#evento_fecha_limite" ).datepicker();
});

