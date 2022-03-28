/**
	TODO: Carregar resto
	
	Lê o arquivo PMML e preenche a ANN
*/
function TratamentoLoader(response, tratamento){

var resultArray = JSON.parse(response);
 		for (x in response){
 			if (resultArray[x]!=undefined){		
 				if (resultArray[x]['type']=="tratamento"){
 					tratamento.setName(resultArray[x]['nome']);
 					tratamento.setId(resultArray[x]['id']);
 				}
			}
		}
}