/**
	Lê o arquivo PMML e preenche a ANN
*/

function CSVReader(file, ann){
	$xml = "";
	// cria o leitor
	var reader = new FileReader();
	//le o arquivo
	reader.readAsText(file);
	//por padrão a função de ativação é a logística
	var defaultActivation = "logistic";
	/**
		Popula a ANN quando carregar o arquivo
	*/
	reader.onloadend = function(){
		var allTextLines = reader.result.split(/\r\n|\n/);
		var headers = allTextLines[0].split(',');
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
		var neuronsIndex=[];
		var allneurons=[];
		var firstNeuronOfLayer="";
		/*TODO
		LAYERS E OUTPUT
		*/
		var quantInputs=0;
		var inputsAnalisados=[];
		for (var i=0;i<lines.length;i++){
			neurons=lines[i][0].split("->");
			emissor=neurons[0].substring(1, neurons[0].length); //tirar aspas
			receptor=neurons[1].substring(0, neurons[1].length-1); //tirar aspas
			valor = parseFloat(lines[i][1]);

			if (emissor=='b' || (emissor.indexOf('i')>=	0 && inputsAnalisados.indexOf(emissor)==-1)){
				quantInputs+=1;
				inputsAnalisados.push(emissor);
			} else{
				break;
			}			
		}
		var hiddenIds = quantInputs-1;
		var inputsId = 1;	
		for (var i=0; i<lines.length;i++){
			neurons=lines[i][0].split("->");
			emissor=neurons[0].substring(1, neurons[0].length); //tirar aspas
			receptor=neurons[1].substring(0, neurons[1].length-1); //tirar aspas
			valor = parseFloat(lines[i][1]);
			// alert(emissor+" "+receptor+" "+valor);
			if (emissor=="b"){
				biasNeuron = valor;
			// é um con
			} else{
				var indexEmissor = neuronsIndex.indexOf(emissor);
				// input não existe
				var neuronEmissor;
				if (indexEmissor==-1){
					// alert(emissor);
					neuronEmissor = ann.addAndShowInput(inputsId, emissor);
					allneurons.push(neuronEmissor);
					neuronsIndex.push(emissor);
					inputsId++;
				} else{
					neuronEmissor = allneurons[indexEmissor];
				}

				var indexReceptor=neuronsIndex.indexOf(receptor);
				//não existe o receptor
				var neuronReceptor;
				if (indexReceptor==-1){
					// criar nova layer
					// alert(receptor);
					if (firstNeuronOfLayer==emissor || firstNeuronOfLayer==""){
						ann.addHiddenLayer();
						ann.setActivationFunction(defaultActivation);				
						firstNeuronOfLayer=receptor;				
					}
					indexReceptor = allneurons.length;
					// alert(biasNeuron);
					neuronReceptor = ann.addHiddenNeuron(hiddenIds++, biasNeuron);
					allneurons.push(neuronReceptor);
					neuronsIndex.push(receptor);
					if (receptor=="o"){
						ann.addOutput(allneurons.length, receptor);
						ann.setActivationFunction("identidade");				
					}
				} else{
					neuronReceptor = allneurons[indexReceptor];
				}
				neuronReceptor.addCon(neuronEmissor, valor);
			}
		}
	}
}