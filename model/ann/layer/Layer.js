
function Layer(_id){
	var id=_id;
	var neurons = new Neurons();
	var bias;
	var activationFunction;
	var functionName;
	var size=0;
	
	this.add = function(_n){
		neurons.add(_n);
		size++;
	} 

	this.get = function(_id){
		return neurons.get(_id);
	}
	
	this.getId = function(){
		return id;
	}
	
	this.setActivationFunction = function(f){
		activationFunction = new ActivationFunction(f);
		functionName = f;
	}

	this.getActivationFunction = function(){
		return functionName;
	}
	
	this.getSize = function(){
		return size;
	}
	
	this.calc = function (){
		for (var i=1;i<=neurons.getSize();i++)
			neurons.get(i).calc(activationFunction);	
	}
}



function OutputLayer(){
	var neurons = new Neurons();
	var size=0;
	
	this.add = function(_n){
		neurons.add(_n);
		size++;
	} 

	this.get = function(_id){
		return neurons.get(_id+1);
	}
	

	this.getSize = function(){
		return size;
	}
}
	

	
function InputLayer(){
	var neurons = new Neurons();
	var size=0;
	this.add = function(neuron){
		neurons.add(neuron);
		size++;
	}
	
	this.getSize = function(){
		return size;
	}
	
	this.get = function(_id){
		return neurons.get(_id);
	}
}
	
function Layers() {
	var layers = [];
	var size=0;
	
	this.add = function (layer)	{
		layers.push(layer);
		size++;
	}
	
	this.get = function(value) {
		return layers[value];
	}

	this.getSize = function(){
		return size;
	}
	

}