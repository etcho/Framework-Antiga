	
	//Formata campo monetário automaticamente. exemplo de uso:
	//onKeyUp="moeda(this)"
	function moeda(z){  
		v = z.value;
		v=v.replace(/\D/g,"")  //permite digitar apenas números
        v=v.replace(/[0-9]{12}/,"inválido")   //limita pra máximo 999.999.999,99
        v=v.replace(/(\d{1})(\d{8})$/,"$1.$2")  //coloca ponto antes dos últimos 8 digitos
        v=v.replace(/(\d{1})(\d{5})$/,"$1.$2")  //coloca ponto antes dos últimos 5 digitos
        v=v.replace(/(\d{1})(\d{1,2})$/,"$1,$2")        //coloca virgula antes dos últimos 2 digitos
		z.value = v;
	}
	
	function formata_moeda(valor){
		v = valor+"";
		v=v.replace(/(\d{1})(\d{8})$/,"$1.$2"); 
        v=v.replace(/(\d{1})(\d{5})$/,"$1.$2");
        v=v.replace(/(\d{1})(\d{1,2})$/,"$1,$2");
		return v;
	}
	
	function somente_numero(campo){
		var digits="0123456789";
		var campo_temp;
		for (var i=0;i<campo.value.length;i++){
			campo_temp=campo.value.substring(i,i+1);
			if (digits.indexOf(campo_temp)==-1)
				campo.value = campo.value.substring(0,i);
		}
	}
	
	function somente_numero_e_virgula(campo){
		var digits="0123456789,";
		var campo_temp;
		for (var i=0;i<campo.value.length;i++){
			campo_temp=campo.value.substring(i,i+1);
			if (digits.indexOf(campo_temp)==-1)
				campo.value = campo.value.substring(0,i);
		}
	}
	
	function formata_cep(elemento){
		v = elemento.value
        v = v.replace(/D/g,"")                            
        v = v.replace(/^(\d{5})(\d)/,"$1-$2") 
        
		if (elemento.maxLength > 255)
			elemento.value = v;
		else
			elemento.value = v.substring(0, elemento.maxLength);
    }
	
	function formata_telefone(telefone){
		$(telefone).bind('input propertychange',function(){
			var texto = $(this).val();
			
			texto = texto.replace(/[^\d]/g, '');
			if (texto.length > 0) {
				texto = "(" + texto;
				if (texto.length > 3) {
					texto = [texto.slice(0, 3), ") ", texto.slice(3)].join('');  
				}
				if (texto.length > 12) {
					if (texto.length > 13)
						texto = [texto.slice(0, 10), "-", texto.slice(10)].join('');
					else
						texto = [texto.slice(0, 9), "-", texto.slice(9)].join('');
				}
				if (texto.length > 15)
					texto = texto.substr(0,15);
			}
			return $(this).val(texto);
		})
	}
	
	function formata_cpf(elemento){
		v = elemento.value;
		v=v.replace(/\D/g,"");
		v=v.replace(/(\d{3})(\d)/,"$1.$2");
		v=v.replace(/(\d{3})(\d)/,"$1.$2");
		v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2");
		
		if (elemento.maxLength > 255)
			elemento.value = v;
		else
			elemento.value = v.substring(0, elemento.maxLength);
	}
	
	function formata_cnpj(elemento){
		v = elemento.value;
		v=v.replace(/\D/g,"");
		v=v.replace(/^(\d{2})(\d)/,"$1.$2");
		v=v.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3");
		v=v.replace(/\.(\d{3})(\d)/,".$1/$2");
		v=v.replace(/(\d{4})(\d)/,"$1-$2");
		
		if (elemento.maxLength > 255)
			elemento.value = v;
		else
			elemento.value = v.substring(0, elemento.maxLength);
	}
	
	function formata_data(elemento){
		v = elemento.value;
		v = v.replace(/\D/g,"");
		v = v.replace(/(\d{2})(\d)/,"$1/$2");
		v = v.replace(/(\d{2})(\d)/,"$1/$2");
		
		if (elemento.maxLength > 255)
			elemento.value = v;
		else
			elemento.value = v.substring(0, elemento.maxLength);
	}
	
	function formata_mes_ano(elemento){
		v = elemento.value;
		v = v.replace(/\D/g,"");
		v = v.replace(/(\d{2})(\d)/,"$1/$2");
		
		if (elemento.maxLength > 255)
			elemento.value = v;
		else
			elemento.value = v.substring(0, elemento.maxLength);
	}
	
	function formata_hora(elemento){
		v = elemento.value
		v = v.replace(/\D/g,"") 
		v = v.replace(/(\d{2})(\d)/,"$1:$2")  
		
		if (elemento.maxLength > 255)
			elemento.value = v;
		else
			elemento.value = v.substring(0, elemento.maxLength);
	}
	
	//modo de usar: onkeypress="mascara_generica(this, '(99) 9999-9999', event)
	function mascara_generica(objeto, sMask, evtKeyPress) {
    	var i, nCount, sValue, fldLen, mskLen,bolMask, sCod, nTecla;
		if(document.all)// Internet Explorer
    		nTecla = evtKeyPress.keyCode;
		else if(document.layers) // Nestcape
    		nTecla = evtKeyPress.which;
		else {
    		nTecla = evtKeyPress.which;
    		if (nTecla == 8)
      	  		return true;
		}
    	sValue = objeto.value;
		// Limpa todos os caracteres de formatação que
		// já estiverem no campo.
		sValue = sValue.toString().replace( "-", "" );
		sValue = sValue.toString().replace( "-", "" );
		sValue = sValue.toString().replace( ".", "" );
		sValue = sValue.toString().replace( ".", "" );
		sValue = sValue.toString().replace( "/", "" );
		sValue = sValue.toString().replace( "/", "" );
		sValue = sValue.toString().replace( ":", "" );
		sValue = sValue.toString().replace( ":", "" );
		sValue = sValue.toString().replace( "(", "" );
		sValue = sValue.toString().replace( "(", "" );
		sValue = sValue.toString().replace( ")", "" );
		sValue = sValue.toString().replace( ")", "" );
		sValue = sValue.toString().replace( " ", "" );
		sValue = sValue.toString().replace( " ", "" );
		fldLen = sValue.length;
		mskLen = sMask.length;
		i = 0;
		nCount = 0;
		sCod = "";
		mskLen = fldLen;
    	while (i <= mskLen) {
      		bolMask = ((sMask.charAt(i) == "-") || (sMask.charAt(i) == ".") || (sMask.charAt(i) == "/") || (sMask.charAt(i) == ":"))
      		bolMask = bolMask || ((sMask.charAt(i) == "(") || (sMask.charAt(i) == ")") || (sMask.charAt(i) == " "))
      		if (bolMask) {
        		sCod += sMask.charAt(i);
        		mskLen++; }
      		else {
        		sCod += sValue.charAt(nCount);
        		nCount++;
      		}
      		i++;
    	}
    	objeto.value = sCod;
    	if (nTecla != 8) { // backspace
      		if (sMask.charAt(i-1) == "9") // apenas números...
        		return ((nTecla > 47) && (nTecla < 58));
      		else // qualquer caracter...
        		return true;
    	} else
      		return true;
  	}
