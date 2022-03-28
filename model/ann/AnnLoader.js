/**
	TODO: Carregar resto
	
	Lê o arquivo PMML e preenche a ANN
*/
function AnnLoader(response, ann){

var resultArray = JSON.parse(response);
 		for (x in response){
 			if (resultArray[x]!=undefined){		
 				if (resultArray[x]['type']=="ann"){
 					ann.setName(resultArray[x]['pmml']);
 					ann.setId(resultArray[x]['id']);
 					ann.setProjeto(resultArray[x]['projeto']); 
 					ann.setTipo(resultArray[x]['tipo']); 
 					ann.setRevisao(resultArray[x]['revisao']);
					ann.setTraining(resultArray[x]['training']);
 				}
 				if (resultArray[x]['type']=="input"){
 					ann.addInput(resultArray[x]['id'], resultArray[x]['name'], resultArray[x]['inteiro'], resultArray[x]['min'], resultArray[x]['max'],  resultArray[x]['txt'], resultArray[x]['media'],resultArray[x]['dp'],resultArray[x]['sequencia'],resultArray[x]['espacamento'],resultArray[x]['tipo'],resultArray[x]['opcoes']); 
 					// console.log("ei "+resultArray[x]['id']+" "+resultArray[x]['name']);
 				}
							
				if (resultArray[x]['type']=="layer"){
				 	ann.addHiddenLayer();
					ann.setActivationFunction(resultArray[x]['activation_function']);
				}
				
				if (resultArray[x]['type']=="neuron"){
					ann.addHiddenNeuron(resultArray[x]['id'], resultArray[x]['bias']);
				}
				
				if (resultArray[x]['type']=="con"){
					var neuron = ann.getNeuron(resultArray[x]['id_neuron']);
					neuron.addCon(ann.getNeuron(resultArray[x]['id_neuron_con']), parseFloat(resultArray[x]['weight']));
				}

				if (resultArray[x]['type']=="output"){
 					ann.addOutput(resultArray[x]['id'], resultArray[x]['name'], resultArray[x]['txt']);
 				}
			}
		}
}