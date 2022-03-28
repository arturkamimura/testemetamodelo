/**
	Lê o arquivo PMML e preenche a ANN
*/
function XMLReader(file, ann){
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
		//	parse texto to XML
		var xmlData = reader.result,
			xmlDoc = $.parseXML( xmlData ),
			$xml = $( xmlDoc );
		
		//cria as entradas
		var neuralInput = $xml.find('NeuralInput');	
		defaultActivation = $xml.find('NeuralNetwork').attr('activationFunction');
		neuralInput.each(function () {
			var id = $(this).attr('id');
			var nomeInput = $(this).find('FieldRef').attr('field');
			ann.addInput(id, nomeInput);

		});
		
		//cria as camadas e os seus neurons
		var neuralLayers = $xml.find('NeuralLayer');		


		neuralLayers.each(function () {
			ann.addHiddenLayer();

			ann.setActivationFunction(defaultActivation);
			if ($(this).attr('activationFunction')!=undefined)
				ann.setActivationFunction($(this).attr('activationFunction'));
			var neurons = $(this).find('Neuron');	
			neurons.each(function () {
				var id = $(this).attr('id');
				var bias = parseFloat($(this).attr('bias'));
				var n = ann.addHiddenNeuron(id, bias);
				var cons = $(this).find('Con');	
				cons.each(function () {
					var from = ann.getNeuron($(this).attr('from'));
					var weight = parseFloat($(this).attr('weight'));
					n.addCon(from, weight);
				});
			});			
		});

		//cria as saídas
		var neuralOutputs = $xml.find('NeuralOutputs');	
		var outputs = neuralOutputs.find('NeuralOutput');
		outputs.each(function () {
			var name = $(this).find('FieldRef').attr('field');
			ann.addOutput($(this).attr('outputNeuron'), name);
		});
	};
}