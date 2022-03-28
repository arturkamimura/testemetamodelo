<?PHP

//	TODO: Carregar resto

$host = "mysql.pbeedifica.com.br";
$banco = "pbeedifica01";
$senha = "gkHCWKG2020";

$function = $_POST["func"];
// mysql_real_escape_string
//Conexão à base de dados
//$dbhandle = mysql_connect($host, $banco, $senha)
$mysqli = new mysqli($host, $banco, $senha, $banco);
if (mysqli_connect_errno()) trigger_error(mysqli_connect_error());

if ($function == "listUFs"){
	$sql ='SELECT estado FROM  `pmml_climas` GROUP BY estado';
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"estado"=>$r["estado"]
		);
	}
	echo json_encode($rows);

}else if ($function == "getCity"){
	$sql ="SELECT latitude, altitude,vvento,radiacao, tma, dpt, ama, dpa FROM  `pmml_climas` where id='". $mysqli->real_escape_string($_POST['cidade'])."'";
	// $sql ="SELECT latitude, altitude, vvento, radiacao, ghr, gha FROM  `pmml_climas` where cidade='Florianópolis'";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(

			"latitude" => utf8_encode($r["latitude"]),
			"altitude" => utf8_encode($r["altitude"]),
			"vvento" => utf8_encode($r["vvento"]),
			"radiacao" => utf8_encode($r["radiacao"]),
			"tma" => utf8_encode($r["tma"]),
			"dpt" => utf8_encode($r["dpt"]),
			"ama" => utf8_encode($r["ama"]),
			"dpa" => utf8_encode($r["dpa"])

		);
	}
	// echo json_encode("OK");
	echo json_encode($rows);



}else if ($function == "listCities"){
	$sql ="SELECT id, cidade FROM  `pmml_climas` where estado='". $mysqli->real_escape_string($_POST['uf'])."'";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"],
			"cidade" => utf8_encode($r["cidade"])


		);
	}
	echo json_encode($rows);



}else if ($function == "delete"){
	$id = intval($_POST["campo"]) or die("campo não é numérico");
	
	if ($id >0 ){
		$sql = "UPDATE pmml_ann SET deleted = 1 WHERE id = ".$id;
		$result = $mysqli->query($sql);

		if (!$result) {
    		die('Invalid query: ' . mysqli_error());
		}
		$response = array("success" => true);
		echo json_encode($response);
	}
} else if ($function == "deleteTratamento"){
	$id = intval($_POST["campo"]) or die("campo não é numérico");
	
	if ($id >0 ){
		$sql = "UPDATE pmml_training SET deleted = 1 WHERE id = ".$id;
		$result = $mysqli->query($sql);

		if (!$result) {
    		die('Invalid query: ' . mysqli_error());
		}
		$response = array("success" => true);
		echo json_encode($response);
	}
} else if ($function == "listComercialANNs"){
	$sql = "SELECT id, pmml FROM pmml_ann where projeto='C' and deleted=0";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"]
		);
	}
	echo json_encode($rows);
} else if ($function == "listResidencialANNs"){
	$sql = "SELECT id, pmml FROM pmml_ann where projeto='R' and deleted=0";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"]
		);
	}
	echo json_encode($rows);
} else if ($function == "listANNs"){
	$sql = "SELECT id, pmml FROM pmml_ann where deleted=0";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"]
		);
	}
	echo json_encode($rows);
} else if ($function == "listDuplicator"){
	$sql = "SELECT id, pmml FROM pmml_ann where deleted=0 and idTraining=".intval($_POST["id"]);
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"]
		);
	}
	echo json_encode($rows);
} else if ($function == "listIdentifiers"){
	$sql = "SELECT id, nome FROM pmml_fields where idTraining=".intval($_POST["id"]);
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"nome"=>$r["nome"]
		);
	}
	echo json_encode($rows);
} else if ($function == "listTratamentos"){
	$sql = "SELECT id, nome FROM pmml_training where deleted=0";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"nome"=>$r["nome"]
		);
	}
	echo json_encode($rows);
} else if (($function == "addTratamento")||($function == "editTratamento")){
	$id=-1;
	$sql = "INSERT INTO pmml_training (nome) VALUES ('".$mysqli->real_escape_string($_POST["nome"])."');";
	if ($function=="editTratamento"){
		$id  = intval($_POST['idann']);
		$sql = "update pmml_training set nome ='".$mysqli->real_escape_string($_POST["nome"])."'  WHERE id=".$id;
	}
	$r = $mysqli->query($sql);
	if ($id==-1)
		$id = $mysqli->insert_id;
	if ($function!='editTratamento'){
		$i=1;
		while (isset($_POST["input".$i])){
			// print $i." ";
			$inputName = $mysqli->real_escape_string($_POST["input".$i]);
			$inputMedia = floatval($_POST["inputMedia".$i]);
			$inputDesvPad = floatval($_POST["inputDesvPad".$i]);
			$sql = "INSERT INTO pmml_fields (nome, idTraining, media, desvpad) values ('".$inputName."', '".$id."','".$inputMedia."','".$inputDesvPad."')";
			$r = $mysqli->query($sql);
			if (!$r){
				die('Invalid query: ' . mysqli_error());
			}
			$i++;
		}
	}

} else if (($function == "addANN")||($function == "editANN")){
	$id=-1;
	$sql = "INSERT INTO pmml_ann (pmml, projeto) VALUES ('".$mysqli->real_escape_string($_POST["nome"])."', '".$mysqli->real_escape_string($_POST["projeto"])."')";
	if ($function=="editANN"){
		$id  = intval($_POST['idann']);
		$sql = "update pmml_ann set pmml ='".$mysqli->real_escape_string($_POST["nome"])."', projeto = '".$mysqli->real_escape_string($_POST["projeto"])."'' WHERE id=".$id;
	}
	$r = $mysqli->query($sql);
	if ($id==-1)
		$id = $mysqli->insert_id;
	$i=1;
	while (isset($_POST["input".$i])){
	//	$inputId = intval($_POST["inputId".$i]);
		$inputName = $mysqli->real_escape_string($_POST["input".$i]);
		$inputHint = $mysqli->real_escape_string($_POST["hint".$i]);
		$inputInteiro = intval($_POST["inteiro".$i]);
		$inputMin = floatval($_POST["min".$i]);
		$inputMax = floatval($_POST["max".$i]);
		$inputMedia = floatval($_POST["media".$i]);
		$inputDP = floatval($_POST["dp".$i]);
		$sql = "INSERT INTO pmml_inputs (id, id_ann, inteiro,  name, min, max, txt, media, dp) values ('".$i."', '".$id."','".$inputInteiro."','".$inputName."','".$inputMin."','".$inputMax."', '".$inputHint."', '".$inputMedia."', '".$inputDP."')";
		if ($function=="editANN"){
			$sql = "UPDATE pmml_inputs set inteiro='".$inputInteiro."', name='".$inputName."', min='".$inputMin."', max='".$inputMax."', txt='".$inputHint."', media= '".$inputMedia."', dp= '".$inputDP."' where id='".$i."' and id_ann='".$id."'";
		}
		$r = $mysqli->query($sql);
		if (!$r){
			die('Invalid query: ' . mysqli_error());
		}
		$i++;
	}
	if ($function!="editANN"){
		$j=0; 
		while (isset($_POST["layer".$j])){
			$layer = explode("|",$_POST["layer".$j]);
			$layerSize = $layer[0];
			$sql = "INSERT INTO pmml_layers (idann, seq, activation_function) VALUES ('".$id."','".$j."','".$layer[1]."')";
			$r = $mysqli->query($sql);
			$idLayer = $mysqli->insert_id;
			if (!$r){
				die('Invalid query: ' . mysqli_error());
			}
			$k=0;
			while ($k<$layerSize){
				$neuronTxt = explode(";", $_POST["hidden".$i]);
				$neuronInfo = explode("=", $neuronTxt[0]);
				$idNeuron = $neuronInfo[0];
				$sql = "INSERT INTO pmml_neurons (id, id_layer, id_ann, bias) VALUES ('".$idNeuron."','".$idLayer."','".$id."','".$neuronInfo[1]."')";
				$r = $mysqli->query($sql);
				if (!$r){
					die('Invalid query: ' . mysqli_error());
				}
				$neuronCons = explode("|",$neuronTxt[1]);
				for ($ii=0;$ii<sizeof($neuronCons);$ii++){
					$con = explode("=", $neuronCons[$ii]);
					if (intval($con[0])!=0){

						$sql = "INSERT INTO pmml_cons (id_layer, id_neuron, id_neuron_con, weight) VALUES ('".$idLayer."','".$idNeuron."','".$con[0]."','".$con[1]."')";
						$r = $mysqli->query($sql);
						if (!$r){
							print $sql;
							die('Invalid query: ' . mysqli_error());
						}
					}
			
				}
				$i++;
				$k++;
			}
			$j++;
		}
	}
	$outputsIds = explode(",", $_POST["outputsList"]);
	foreach ($outputsIds as &$outputId){
		if ($outputId!=''){
			$sql = "SELECT id, id_layer FROM pmml_neurons where id = '".$outputId."' and id_ann= '".$id."'";
			$result = $mysqli->query($sql);
			if (!$result) {
				die('Invalid query: ' . mysqli_error());
			}
			while ($r = mysqli_fetch_array($result)) {	
				$sql = "INSERT INTO pmml_outputs (id_neuron, id_layer, id_ann, name, txt) VALUES ('".$r["id"]."','".$r["id_layer"]."','".$id."','".$_POST["outputName".$outputId]."','".$_POST["outputHint".$outputId]."')";
				if ($function=="editANN"){
					$sql = "update pmml_outputs set name='".$_POST["outputName".$outputId]."', txt='".$_POST["outputHint".$outputId]."' where id_neuron='".$r["id"]."' and id_layer='".$r["id_layer"]."'";				
				}
				// print $sql;			
				$r = $mysqli->query($sql);
				if (!$r){
					die('Invalid query: ' . mysqli_error());
				}
			}
		}
	}
	/*while (isset($_POST["hidden".$i])){
		$i++;
	}*/
	
	$response = array("success" => true);
	echo json_encode($response);	
	/*$sql = "SELECT id, pmml FROM pmml_ann where deleted=0";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"]
		);
	}
	echo json_encode($rows);*/



} else if (($function == "addANN2")||($function == "editANN2")){
	$id=-1;
	// print "aqui";
	$sql = "INSERT INTO pmml_ann (pmml, projeto, tipo, idTraining, revisao) VALUES ('".$mysqli->real_escape_string($_POST["nome"])."', '".$mysqli->real_escape_string($_POST["projeto"])."', '".$mysqli->real_escape_string($_POST["tipo"])."', '".$mysqli->real_escape_string($_POST["cbTratamentos"])."', '".$mysqli->real_escape_string($_POST["revisao"])."')";
	if ($function=="editANN2"){
		$id  = intval($_POST['idann']);
		$sql = "UPDATE pmml_ann SET pmml ='".$mysqli->real_escape_string($_POST["nome"])."', projeto = '".$mysqli->real_escape_string($_POST["projeto"])."', tipo='".$mysqli->real_escape_string($_POST["tipo"])."', idTraining='".$mysqli->real_escape_string($_POST["cbTratamentos"])."', revisao='".$mysqli->real_escape_string($_POST["revisao"])."' WHERE id=".$id;
	}
	$r = $mysqli->query($sql);
	if ($id==-1)
		$id = $mysqli->insert_id;
	$i=1;

	$sequencia = explode(",", $_POST["p"]);
	echo $_POST["p"];

	 while (isset($_POST["input".$i])){
	// //	$inputId = intval($_POST["inputId".$i]);
		$inputName = $mysqli->real_escape_string($_POST["input".$i]);
		$inputHint = $mysqli->real_escape_string($_POST["hint".$i]);
		$inputEspacamento = isset($_POST['espacamento'.$i]);
		$inputTipo = intval($_POST["type".$i]);
		$inputOpcoes = $mysqli->real_escape_string($_POST["opcoes".$i]);
		$inputMin = floatval($mysqli->real_escape_string($_POST["min".$i]));
		$inputMax = floatval($mysqli->real_escape_string($_POST["max".$i]));
		$inputSequencia = 1;
		for ($q=0;$q<sizeof($sequencia);$q++){
			if (intval($sequencia[$q])==$i){
				$inputSequencia=$q+1;
				break;
			}
		}	
		// intval($sequencia[$i-1]);
//		$inputMedia = floatval($_POST["media".$i]);
	//	$inputDP = floatval($_POST["dp".$i]);
		$sql = "Select media, desvpad from pmml_fields where nome='".$inputName."' and idTraining=".$mysqli->real_escape_string($_POST["cbTratamentos"]);
		$result = $mysqli->query($sql);
		$r = mysqli_fetch_array($result);
		print $r['media'];

		$sql = "INSERT INTO pmml_inputs (id, id_ann, name, txt, tipo, opcoes, sequencia, espacamento, media, dp, min, max) values ('".$i."', '".$id."','".$inputName."','".$inputHint."','".$inputTipo."', '".$inputOpcoes."','".$inputSequencia."', '".$inputEspacamento."', '".$r['media']."', '".$r['desvpad']."','".$inputMin."','".$inputMax."')";
		if ($function=="editANN2"){
			$sql = "UPDATE pmml_inputs set media='".$r['media']."', dp='".$r['desvpad']."', name='".$inputName."', txt='".$inputHint."', tipo='".$inputTipo."', opcoes='".$inputOpcoes."', sequencia='".$inputSequencia."', min='".$inputMin."', max='".$inputMax."', espacamento='".$inputEspacamento."'  where id='".$i."' and id_ann='".$id."'";
	 	}
	 	$r = $mysqli->query($sql);
	 	if (!$r){
	 		die('Invalid query: ' . mysqli_error());
	 	}
	 	$i++;
	 }
	if ($function!="editANN2"){
		$j=0;
		while (isset($_POST["layer".$j])){
			print "aqui";
			$layer = explode("|",$_POST["layer".$j]);
			$layerSize = $layer[0];
			$sql = "INSERT INTO pmml_layers (idann, seq, activation_function) VALUES ('".$id."','".$j."','".$layer[1]."')";
			$r = $mysqli->query($sql);
			$idLayer = $mysqli->insert_id;
			if (!$r){
				die('Invalid query: ' . mysqli_error());
			}
			$k=0;
			while ($k<$layerSize){
				$neuronTxt = explode(";", $_POST["hidden".$i]);
				$neuronInfo = explode("=", $neuronTxt[0]);
				$idNeuron = $neuronInfo[0];
				$sql = "INSERT INTO pmml_neurons (id, id_layer, id_ann, bias) VALUES ('".$idNeuron."','".$idLayer."','".$id."','".$neuronInfo[1]."')";
				$r = $mysqli->query($sql);
				if (!$r){
					die('Invalid query: ' . mysqli_error());
				}
				$neuronCons = explode("|",$neuronTxt[1]);
				for ($ii=0;$ii<sizeof($neuronCons);$ii++){
					$con = explode("=", $neuronCons[$ii]);
					if (intval($con[0])!=0){

						$sql = "INSERT INTO pmml_cons (id_layer, id_neuron, id_neuron_con, weight) VALUES ('".$idLayer."','".$idNeuron."','".$con[0]."','".$con[1]."')";
						$r = $mysqli->query($sql);
						if (!$r){
							print $sql;
							die('Invalid query: ' . mysqli_error());
						}
					}
			
				}
				$i++;
				$k++;
			}
			$j++;
		}
	}
	if ($function!="editANN2"){

		$outputsIds = explode(",", $_POST["outputsList"]);
		
		foreach ($outputsIds as &$outputId){
			if ($outputId!=''){
				echo "output id e ann id ".$outputId. " ".$id;
				$sql = "SELECT id, id_layer FROM pmml_neurons where id = '".$outputId."' and id_ann= '".$id."'";
				$result = $mysqli->query($sql);
				if (!$result) {
					die('Invalid query: ' . mysqli_error());
				}
				while ($r = mysqli_fetch_array($result)) {	
					echo "feito";

					// $sql = "INSERT INTO pmml_outputs (id_neuron, id_layer, id_ann, name, txt) VALUES ('".$r["id"]."','".$r["id_layer"]."','".$id."','".$_POST["outputName".$outputId]."','".$_POST["outputHint".$outputId]."')";
					$sql = "INSERT INTO pmml_outputs (id_neuron, id_layer, id_ann, name, txt) VALUES ('".$r["id"]."','".$r["id_layer"]."','".$id."','nome','hint')";
						// $sql = "update pmml_outputs set name='".$_POST["outputName".$outputId]."', txt='".$_POST["outputHint".$outputId]."' where id_neuron='".$r["id"]."' and id_layer='".$r["id_layer"]."'";				
						// $sql = "update pmml_outputs set name='nome', txt='hint' where id_neuron='".$r["id"]."' and id_layer='".$r["id_layer"]."'";				
									// print $sql;			
					$r = $mysqli->query($sql);
					if (!$r){
						die('Invalid query: ' . mysqli_error());
					}
				}
			}

		}
	}

	$response = array("success" => true);
	echo json_encode($response);	
} else if($function == "loadTratamento"){
	$idTraining = intval($_POST["campo"]);
	$sql = "SELECT id, nome FROM pmml_training where id = '".$idTraining."'";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"tratamento",
			"id"=>$r["id"], 
			"nome"=>$r["nome"]
		);
	}
	echo json_encode($rows);


} else if($function == "load"){
	$idAnn = intval($_POST["campo"]);

	$sql = "SELECT id, pmml, projeto, tipo, revisao, idTraining FROM pmml_ann where id = '".$idAnn."' order by id";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	
	//Load ANN
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"ann",
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"],
			"projeto"=>$r["projeto"],
			"tipo"=>$r["tipo"],
			"revisao"=>$r["revisao"],
			"training"=>$r["idTraining"]
		);
	}
	
	//LOAD Inputs
	$sql = "SELECT id, inteiro, name, min, max, txt, media, dp, sequencia, espacamento, tipo, opcoes FROM pmml_inputs where id_ann = '".$idAnn."'";
	// $sql = "SELECT id, inteiro, name, min, max, txt, media, dp FROM pmml_inputs where id_ann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"input",
			"id"=>$r["id"], 
			"inteiro"=>$r["inteiro"],
			"name"=>$r["name"],
			"min"=>$r["min"],
			"max"=>$r["max"],
			"txt"=>$r["txt"],
			"media"=>$r["media"],
			"dp"=>$r["dp"],
			"sequencia"=>$r["sequencia"],
			"espacamento"=>$r["espacamento"],
			"tipo"=>$r["tipo"],
			"opcoes"=>$r["opcoes"]
		);
	}
	
	//Load Layers
	$sql = "SELECT id, seq, activation_function FROM pmml_layers where idann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	$layers = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$layers[] = array(
			"type"=>"layer",
			"id"=>$r["id"], 
			"seq"=>$r["seq"],
			"activation_function"=>$r["activation_function"]
		);
	}
	
	foreach ($layers as &$l){
		$rows[] = $l;
		$sql = "SELECT pmml_neurons.id, bias FROM pmml_neurons join pmml_layers on pmml_neurons.id_layer=pmml_layers.id where idann = '".$idAnn."' and pmml_neurons.id_layer='".$l["id"]."'";
		$result = $mysqli->query($sql);
		if (!$result) {
			die('Invalid query: ' . mysqli_error());
		}
		while ($r = mysqli_fetch_array($result)) {
			$rows[]= array(
				"type"=>"neuron",
				"id"=>$r["id"],
				"bias"=>$r["bias"]			
			);
		}
	}
	$sql = "SELECT pmml_cons.id_neuron, pmml_cons.id_neuron_con, weight FROM pmml_cons join pmml_layers on (pmml_cons.id_layer = pmml_layers.id) where pmml_layers.idann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[]= array(
			"type"=>"con",
			"id_neuron"=>$r["id_neuron"],
			"id_neuron_con"=>$r["id_neuron_con"],			
			"weight"=>$r["weight"]	
		);
	}
	


/*
	/*$sql = "SELECT pmml_cons.id_layer, id_neuron, id_neuron_con, weight FROM pmml_cons join pmml_layers on pmml_cons.id_layer=pmml_layers.idate(format) where idann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"con",
			"id_neuron"=>$r["id_neuron"], 
			"id_layer"=>$r["id_layer"],
			"id_neuron_con"=>$r["id_neuron_con"],
			"weight"=>$r["weight"]
		);
	}*/
	
	//Load Outputs
	$sql = "SELECT id_neuron, name, txt FROM pmml_outputs where id_ann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"output",
			"id"=>$r["id_neuron"], 
			"name"=>$r["name"],
			"txt"=>$r["txt"]
		);
	}


	echo json_encode($rows);


} else if($function == "load2"){
	$idAnn = intval($_POST["campo"]);

	$sql = "SELECT id, pmml, projeto, tipo, revisao, idTraining FROM pmml_ann where id = '".$idAnn."'";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	
	//Load ANN
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"ann",
			"id"=>$r["id"], 
			"pmml"=>$r["pmml"],
			"projeto"=>$r["projeto"],
			"tipo"=>$r["tipo"],
			"revisao"=>$r["revisao"],
			"training"=>$r["idTraining"]
		);
	}
	
	//LOAD Inputs
	$sql = "SELECT id, inteiro, name, min, max, txt, media, dp, sequencia, espacamento, tipo, opcoes FROM pmml_inputs where id_ann = '".$idAnn."' order by sequencia";
	 // $sql = "SELECT id, inteiro, name, min, max, txt, media, dp FROM pmml_inputs where id_ann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"input",
			"id"=>$r["id"], 
			"inteiro"=>$r["inteiro"],
			"name"=>$r["name"],
			"min"=>$r["min"],
			"max"=>$r["max"],
			"txt"=>$r["txt"],
			"media"=>$r["media"],
			"dp"=>$r["dp"],
			"sequencia"=>$r["sequencia"],
			"espacamento"=>$r["espacamento"],
			"tipo"=>$r["tipo"],
			"opcoes"=>$r["opcoes"]
		);
	}
	
	//Load Layers
	$sql = "SELECT id, seq, activation_function FROM pmml_layers where idann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	$layers = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$layers[] = array(
			"type"=>"layer",
			"id"=>$r["id"], 
			"seq"=>$r["seq"],
			"activation_function"=>$r["activation_function"]
		);
	}
	
	foreach ($layers as &$l){
		$rows[] = $l;
		$sql = "SELECT pmml_neurons.id, bias FROM pmml_neurons join pmml_layers on pmml_neurons.id_layer=pmml_layers.id where idann = '".$idAnn."' and pmml_neurons.id_layer='".$l["id"]."'";
		$result = $mysqli->query($sql);
		if (!$result) {
			die('Invalid query: ' . mysqli_error());
		}
		while ($r = mysqli_fetch_array($result)) {
			$rows[]= array(
				"type"=>"neuron",
				"id"=>$r["id"],
				"bias"=>$r["bias"]			
			);
		}
	}
	$sql = "SELECT pmml_cons.id_neuron, pmml_cons.id_neuron_con, weight FROM pmml_cons join pmml_layers on (pmml_cons.id_layer = pmml_layers.id) where pmml_layers.idann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[]= array(
			"type"=>"con",
			"id_neuron"=>$r["id_neuron"],
			"id_neuron_con"=>$r["id_neuron_con"],			
			"weight"=>$r["weight"]	
		);
	}
	


/*
	/*$sql = "SELECT pmml_cons.id_layer, id_neuron, id_neuron_con, weight FROM pmml_cons join pmml_layers on pmml_cons.id_layer=pmml_layers.idate(format) where idann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	$rows = array();
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"con",
			"id_neuron"=>$r["id_neuron"], 
			"id_layer"=>$r["id_layer"],
			"id_neuron_con"=>$r["id_neuron_con"],
			"weight"=>$r["weight"]
		);
	}*/
	
	//Load Outputs
	$sql = "SELECT id_neuron, name, txt FROM pmml_outputs where id_ann = '".$idAnn."'";
	$result = $mysqli->query($sql);
	if (!$result) {
		die('Invalid query: ' . mysqli_error());
	}
	while ($r = mysqli_fetch_array($result)) {
		$rows[] = array(
			"type"=>"output",
			"id"=>$r["id_neuron"], 
			"name"=>$r["name"],
			"txt"=>$r["txt"]
		);
	}


	echo json_encode($rows);


}


?>