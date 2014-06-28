var backend = backend || {}

backend.inicio = {

	guardarTab: function(n) {
		$("#tab"+n+"-form-contenido").ajaxForm({
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

	cargarContenidoSlides: function() {
		$.post("/backend/getcontenidoslides", {}, function(response){
			if(response.error) { alert(response.error); return; }
			if(response.seccion == false) return;
			for(var i=0;i<response.slides.length;i++){
					$("#tab"+(i+1)+"_tit").val(response.slides[i].titulo_slide);
					$("#tab"+(i+1)+"_tit2").val(response.slides[i].titulo_slide2);
					$("#tab"+(i+1)+"_desc").val(response.slides[i].desc_slide);
					if(response.slides[i].activar == "activado"){
						$("#chk_act_"+(i+1)).attr('checked', true);
					}

			}
		}, "json");
	}

}

$(document).ready(function(){
	// CKEDITOR.replace('ckeditor_contenido');
	$("#tabs").tabs();
	// CKEDITOR.on('instanceReady', function(){
		backend.inicio.cargarContenidoSlides();
	// });


});

