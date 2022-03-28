function ANN(_index) {
		var inputs = new InputLayer();
		var layers = new Layers();
		var name = "";
		var id=-1;
		var neurons = new Neurons();
		var outputs = new OutputLayer();
		var indexView = _index;
		var layer = -1;
		var projeto = "C";
		var tipo = "R";
		var revisao = "";
		var training = -1;

		this.setId = function (_id){
			id=_id;
			indexView.setId(_id);
		}

		this.setName = function(_name){
			name = _name;
			indexView.setName(_name);
		}


		this.setProjeto = function (_proj){
			projeto = _proj;
			indexView.setProjeto(_proj);
		}

		this.setTipo = function (_tipo){
			tipo = _tipo;
			indexView.setTipo(_tipo);

		}

		this.setRevisao = function (_revisao){
			revisao = _revisao;
			indexView.setRevisao(_revisao);

		}

		this.setTraining = function (_training){
			training = _training;
			indexView.setTraining(_training);
		}


		this.getProjeto = function (){
			return projeto;
		}

		this.getId = function (){
			return id;
		}

		this.getName = function(){
			return name;
		}

		this.getTipo = function(){
			return tipo;
		}
		this.getRevisao = function(){
			return revisao;
		}
		this.getTraining = function(){
			return training;
		}

		this.getLayers = function(){
			return layers;
		}
						
		this.getNeuron= function (_value){
			return neurons.get(_value);
		}

		this.getLayer = function (_id){
			return layers.getLayer(_id);
		}

		this.getLayersSize= function (){
			return layers.getSize();
		}
				
		this.calcLayers = function(){
			for (var i=0;i<layer+1;i++)		
				layers.get(i).calc();	
			return outputs;
			// for (var i=0; i<outputs.getSize();i++){
			// 	indexView.showOutput(outputs.get(i));
			// }					
		}
		
		this.setActivationFunction = function (aFunc) {
			layers.get(layer).setActivationFunction(aFunc);
		}
		
		this.addOutput = function(_id, _name, _hint){
			_hint  = _hint || "";
			var o = new Output(neurons.get(_id), _name, _hint);
			indexView.showOutputToEdit(o);
			outputs.add(o);
		}

		this.showInputs = function(){
			for (var i=1; i<=inputs.getSize();i++){
				indexView.showInput(inputs.get(i));
			}
		}

		this.getResidencialValue = function(){
			return outputs.get(0).getValue(projeto);		
		}
		
		this.addInput = function(_id, _value, _inteiro, _positivo, _min, _max, _def, _hint, _sequencia, _espacamento, _tipo, _opcoes)	{
			var sequencia = _sequencia || 0;
			var espacamento = _espacamento || 0;
			var tipo = _tipo || "";
			var opcoes = _opcoes || "";
				var newInput = new Input(_id, _value, _inteiro, _positivo, _min, _max, _def, _hint, sequencia, espacamento, tipo, opcoes);
			neurons.add(newInput);
			inputs.add(newInput);
			// indexView.showInput(newInput);
			return newInput;
		}

		this.addAndShowInput = function(_id, _value, _inteiro, _positivo, _min, _max, _def, _hint, _sequencia, _espacamento, _tipo, _opcoes)	{
				var sequencia = _sequencia || 0;
			var espacamento = _espacamento || 0;
			var tipo = _tipo || "";
			var opcoes = _opcoes || "";
			var newInput = new Input(_id, _value, _inteiro, _positivo, _min, _max, _def, _hint, sequencia, espacamento, tipo, opcoes);
			neurons.add(newInput);
			inputs.add(newInput);
			indexView.showInput(newInput);
			return newInput;
		}
		
		this.getInputsSize = function (){
			return inputs.getSize();
		}
		
		this.addHiddenLayer = function(){
			var l =new Layer(++layer);
			layers.add(l);
			return l;
		}
		
		this.addHiddenNeuron = function(_id, _bias)	{
			var neuron = new Hidden(_id, parseFloat(_bias));
			neurons.add(neuron);
			layers.get(layer).add(neuron);
			return neuron;
		}

		this.toString = function(){
			var text="Inputs:";
			var i;
			for (i=1;i<=inputs.getSize();i++){
				text+=inputs.get(i).getName()+"; ";
			}
			text+="\nNeurons:";
			i++;
			for (i;i<neurons.getSize();i++){
				var n = neurons.get(i);
				text+=n.getId();
				// alert(n.getId());
				for (var j=0;j<n.getConsSize();j++){
					text+="<="+n.getCon(j).getNeuronId();
				}
				text+="; ";
			}
			return text;
		}		
}