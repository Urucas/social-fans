var backend = backend || {}

backend.lista = {

	dellLs: function(nid) {
		if(nid == undefined) return;

		if(confirm("¿Esta seguro que desea eliminar la Lista?")) {
			$.post("/backend/eliminarlista/",{"id":1}, function(response){
				if(response.error) {
					alert(response.error);
					return;
				}
				alert(response.msg);
				document.location.reload();
			}, 'json');
		}
	},

	guardararchivo: function() {

		$("#form-arc").ajaxForm({
			success:function(rta){
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
	
	cargarContenido: function() {
		$.post("/backend/getlista", {}, function(response){
			if(response.error) { alert(response.error); return; }
			if(response.lista == false) return;
			// alert(response.total_pages);
			var html = "";
			// console.log(response);
			for(var i=0;i<response.lista.length;i++){
						html+= "<tr draggable='true' id='lista_"+response.lista[i].id+"'><td>"+response.lista[i].nombre+"</td>";
                        html+= "<td><a href='"+response.lista[i].url_file+"'>"+response.lista[i].url_file+"</a></td>";
                        
                        if(response.lista[i].url_file==""){
                        	html+= '<td></td></tr>';
						}else{
							html+= '<td><a href="#" onclick="backend.lista.dellLs('+response.lista[i].id+')">Eliminar</a></td></tr>';
						}

					}
			$("#lst").html(html);

		}, "json");
	}
}

$(document).ready(function(){
		backend.lista.cargarContenido();
});

