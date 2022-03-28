function Input (_id, _name, _inteiro, _min, _max, _hint, _media, _dp, _sequencia, _espacamento, _tipo, _opcoes){
	var id = _id; 
	var value=0;
	var name = _name ;
	var min = _min || 0;
	var max = _max || 0;
	var hint = _hint || "";
	var dp= parseFloat(_dp) || 0;
	var media= parseFloat(_media) || 0;
	var sequencia = _sequencia || 0;
	var espacamento = _espacamento || 0;
	var tipo = _tipo || "";
	var opcoes = _opcoes || "";


	var inteiro=false;
	if (_inteiro==1)
		inteiro=true

	this.isInteger = function(){
		return inteiro;
	}

	this.getDP = function(){
		return dp;
	}

	this.getMedia = function(){
		return media;
	}

	this.getMax = function(){
		return max;
	}

	this.getMin = function(){
		return min;
	}

	this.getHint = function(){
		return hint;
	}

	this.getValue = function(){
		return value;
	}
	this.getId = function(){
		return id;
	}
	this.getName = function(){
		return name;
	}	
	this.getSequencia = function(){
		return sequencia;
	}	
	this.getEspacamento = function(){
		return espacamento;
	}	
	this.getTipo = function(){
		return tipo;
	}	
	this.getOpcoes = function(){
		return opcoes;
	}
	
	this.setValue = function(v){
		value=v;
		if (media!=0){
			value-=media;
		}
		if (dp!=0){
			value/=dp;
		}	
	}	
}

function Hidden (_id, _bias) {
	var id=_id;
	var cons = new Cons();
	var bias=_bias;
	var value=0;
	
	this.addCon = function (_neuron, _weight){
		cons.add(new Con(_neuron, _weight));
	}
	this.getValue = function(){
		return value;
	}

	this.getBias = function(){
		return bias;
	}
	
	this.calc = function(f){
		value=0;
		for (var i=0;i<cons.getSize();i++){
			var con = cons.get(i);
			value+=con.getWeight()*con.getNeuronValue();
		}
		value = f(value+bias);
		// alert(value);
	}
	
	this.getId = function(){
		return id;
	}

	this.getConsSize = function(){
		return cons.getSize();
	}
	
	this.getCon = function (_id){
		return cons.get(_id);
	}	
}


function Output(_neuron, _name, _hint){
	var neuron = _neuron;
	var name = _name;
	var hintText = _hint || "";
	this.getName = function(){
		return name;
	}
	this.getNeuron = function(){
		return neuron;
	}
	
	this.getHint = function(){
		return hintText;
	}

	this.getNeuronId = function(){
		return neuron.getId();
	}
	
	this.getValue = function(_projeto){
		// alert(neuron.getValue());
		// alert((Math.pow(10,neuron.getValue()))*20.25);
		if (_projeto=="C"){
			console.log("aqui");
			return ((Math.pow(10,neuron.getValue())));
		}
		else{
			console.log("certo");
			return neuron.getValue();
		}
	}

}

 function Neurons(){
		var neurons = [];
		var size = 0;
		
		this.add = function(_value){
			neurons.push(_value);
			size++;
		}
		
		this.get = function(_value)	{
			return neurons[_value-1];
		}
		
		this.getSize = function(){
			return size;
		}
	}
