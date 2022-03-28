function ActivationFunction(e){
	switch(e){
		case "logistic":
			return function (value){
				return 1/(1+Math.exp(-(value)));
			}
			break;
	}
	return function (value){
		return value;
	}
}