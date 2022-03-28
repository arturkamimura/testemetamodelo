/**
	Lê o arquivo PMML e preenche a ANN
*/

function TratamentoReader(file, tratamento){
	$xml = "";
	// cria o leitor
	var reader = new FileReader();
	//le o arquivo
	reader.readAsText(file);
	//por padrão a função de ativação é a logística
	/**
		Popula a ANN quando carregar o arquivo
	*/
	reader.onloadend = function(){
		var allTextLines = reader.result.split(/\r\n|\n/);
		var headers = allTextLines[0].split(',');
		for (var i=0;i<headers.length;i++){
			tratamento.pushField(new Field(headers[i]));
		}
		var lines = [];
		for (var i=1; i<allTextLines.length; i++) {
		    var data = allTextLines[i].split(',');
		    if (data.length == headers.length) {
		        var tarr = [];
		        for (var j=0; j<headers.length; j++) {
		            tarr.push(data[j]);
		        }
		        lines.push(tarr);
		    }
		}
	
		for (var i=0;i<lines.length;i++){
			for (var j=0;j<lines[i].length;j++){
				tratamento.getField(j).pushValue(parseFloat(lines[i][j]));
			}
		}
		tratamento.showFields();

	}
}