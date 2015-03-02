	//esconde ou mostra o loading do ajax. para ser usado antes e no final de uma requisição ajax.
	//se a img tiver com id="ajax_loader", basta chamar a função como ajax()
	function ajax(elemento){
		if (elemento == null)
			elemento = "ajax_loader";
		div_ajax = document.getElementById(elemento);
		if (div_ajax != null){
			if (div_ajax.style.display == "")
				div_ajax.style.display = "none";
			else
				div_ajax.style.display = "";
		}
	}
	
	//esconde um elemento de forma elegante. requer scriptaculous
	function smart_fade(elemento){
		elemento = typeof(elemento) == "string" ? document.getElementById(elemento) : elemento;
		if (elemento.style.display == ""){
			$("#"+elemento.id).slideUp("fast");
		}
	}
	
	function hide(elemento){
		elemento = typeof(elemento) == "string" ? document.getElementById(elemento) : elemento;
		elemento.style.display = 'none';
	}
	
	function show(elemento){
		elemento = typeof(elemento) == "string" ? document.getElementById(elemento) : elemento;
		elemento.style.display = '';
	}
	
	function show_and_hide(elemento, intervalo){
		elemento = typeof(elemento) == "string" ? document.getElementById(elemento) : elemento;
		Effect.Appear(elemento, {duration:0.3});
		setTimeout("Effect.Fade(document.getElementById('"+elemento.id+"'), {duration:0.3})", intervalo);
	}
	
	function toggle(elemento){
		elemento = typeof(elemento) == "string" ? document.getElementById(elemento) : elemento;
		elemento.style.display = elemento.style.display == "none" ? "" : "none";
	}
	
	//remove os espaços no início e no fim de uma string
	function trim(str){
		return str.replace(/^\s+|\s+$/g,"");
	}
	
	function enter_detectado(evt){
		var key_code = evt.keyCode  ? evt.keyCode  :
					   evt.charCode ? evt.charCode :
					   evt.which    ? evt.which    : void 0;
		return key_code == 13;
	}
	
	
	//funções para o funcionamento das abas
	function showTab(name){
		$(".tab-content").css("display", "none");
		$("div.tabs a").removeClass("selected");
		$("#tab-content-" + name).css("display", "");
		$("#tab-" + name).addClass("selected");
		return false;
	}
	
	function moveTabRight(el) {
		var lis = Element.up(el, 'div.tabs').down('ul').childElements();
		var tabsWidth = 0;
		var i;
		for (i=0; i<lis.length; i++)
			if (lis[i].visible())
				tabsWidth += lis[i].getWidth() + 6;
		if (tabsWidth < Element.up(el, 'div.tabs').getWidth() - 60)
			return;
		i=0;
		while (i<lis.length && !lis[i].visible())
			i++;
		lis[i].hide();
	}
	
	function moveTabLeft(el) {
		var lis = Element.up(el, 'div.tabs').down('ul').childElements();
		var i = 0;
		while (i<lis.length && !lis[i].visible())
			i++;
		if (i>0)
			lis[i-1].show();
	}
	
	function displayTabsButtons() {
		var lis;
		var tabsWidth = 0;
		var i;
		$$('div.tabs').each(function(el) {
			lis = el.down('ul').childElements();
			for (i=0; i<lis.length; i++)
				if (lis[i].visible())
					tabsWidth += lis[i].getWidth() + 6;
			if ((tabsWidth < el.getWidth() - 60) && (lis[0].visible()))
				el.down('div.tabs-buttons').hide();
			else
				el.down('div.tabs-buttons').show();
		});
	}
	//fim das funções para funcionamento das abas