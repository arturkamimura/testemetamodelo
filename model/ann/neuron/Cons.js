function Con(_neuron, _iWeight){
	var neuron = _neuron;
	var iWeight = _iWeight;
	
	this.getNeuronId = function(){
		return neuron.getId();
	}
	
	this.getNeuronValue = function(){
		return neuron.getValue();
	}
	
	this.getWeight = function(){
		return iWeight;
	}
}

function Cons(){
	var cons = [];
	var size = 0;

	this.add = function(con){
		cons.push(con);
		size++;
	}
	
	this.getSize = function() {
		return size;
	}
	
	this.get = function (value){
		return cons[value];
	}
}