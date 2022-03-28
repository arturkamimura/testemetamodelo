function Tratamento(_index) {
		var fields = new Array();
		var name = "";
		var id=-1;
		var indexView = _index;

		this.setId = function (_id){
			id=_id;
			indexView.setId(_id);
		}

		this.getId = function (){
			return id;
		}

		this.setName = function(_name){
			name = _name;
			indexView.setName(_name);
		}

		this.getName = function(){
			return name;
		}

		this.pushFieldAndShow = function (_field){
			fields.push(_field);
			indexView.showField(_field);
		}

		this.showFields = function (){
			for (var i=0;i<fields.length;i++)
				indexView.showField(fields[i]);
		}

		this.pushField = function (_field){
			fields.push(_field);
		}

		this.getFieldsSize = function(){
			return fields.length;
		}

		this.getField = function(i){
			return fields[i];
		}

}

function Field(_nome){
	var nome = _nome;
	var values = new Array();

	this.getNome = function(){
		return nome;
	}

	this.pushValue = function (value){
		values.push(value);
	}

	this.getMedia = function(){
		return math.mean(values);
		// var sum = 0;
		// for( var i = 0; i < values.length; i++ ){
		//     sum += parseFloat( values[i] ); //don't forget to add the base
		// }
		// return sum/values.length;
	}

	this.getDesvPad = function(){
		return math.std(values);
	}
}