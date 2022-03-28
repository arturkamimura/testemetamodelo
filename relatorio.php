

<html>
<head>
   <!-- Desenvolvido por Karran Besen em 2015 no Laboratório de eficiência energética em edificações -->
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Parse PMML ANN To JavaScript</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- <link rel="stylesheet" href="style.css"> -->
	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- Latest compiled JavaScript -->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>	

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>

function createInputChart(id_name, value){
	var formRel =   document.getElementById("formRel");
	var inp = document.createElement('input');
	inp.name=id_name;
	inp.id=id_name;
	inp.value=value;
	inp.type="hidden";
	formRel.appendChild(inp);
}

function createDivChart(id_name){
	var chartsDiv =   document.getElementById("futureCharts");
	var newDiv = document.createElement('div');
	newDiv.id = id_name;
	chartsDiv.appendChild(newDiv);
}


function drawPieChart(cont, input, values){
	// createDivChart(cont);
	// createInputChart(input, "");
	google.charts.load("current", {packages:["corechart"]});
	google.charts.setOnLoadCallback(drawChart);
	function drawChart() {
		console.log(values);
		var data = google.visualization.arrayToDataTable(values);
		// var data =  google.visualization.arrayToDataTable([["amb","%"],["0", 3281.07]]);
		var s = new Array()
		for (var i=0;i<values.length-1;i++){
			if (i==0){
				s.push({color: '#606163',offset:.05});            
		  	} else{
				s.push({color: '#96989B', offset:.05});           
		  	}
		}
    	var options = {
			pieHole: 0.4,
			pieSliceText: 'label',
			legend: {position:'none'},
			'width': 350,
			'height': 400,
			'chartArea': {'width': '100%', 'height': '80%'},
			// enableInteractivity:false,
			pieSliceTextStyle: {fontSize:16,   bold: true,},  
			backgroundColor: { fill:'transparent' },
			slices: s,
		};
		var chart = new google.visualization.PieChart(document.getElementById(cont));

		google.visualization.events.addListener(chart, 'ready', function () {
  			document.getElementById(input).value=chart.getImageURI();
  	});     
    chart.draw(data, options);
  }
}



function makeCharts(){
	var pavs = Number(document.getElementById("inPavs").value);
	for (var i=1;i<=pavs;i++){
		var uhs = Number(document.getElementById("inPav"+i).value);
		for (var j=1;j<=uhs;j++){
			var ambs =Number(document.getElementById("inPav"+i+"UH"+j).value);
			var resultadosResf = [['Ambiente', '%']];
			var contEstar=1;
			var contDorms=1;
			for (var k=0;k<ambs;k++){
				var invalores = document.getElementById("inPav"+i+"UH"+j+"Amb"+k).value;
				var valores = invalores.split("|");
				var v = [];
				for (var n=0;n<valores.length;n++){
					var valor = valores[n].split("=");
					if (valor[0]=="ER" ){
						v.push("E"+contEstar++)
						v.push(Number(valor[1]));
					} else if (valor[0]=="DR" ){
						v.push("D"+contDorms++)
						v.push(Number(valor[1]));
					}
				}	
				resultadosResf.push(v);			
			}
			// console.log(resultadosResf);
			drawPieChart("resfPav"+i+"UH"+j,"inresfPav"+i+"UH"+j, resultadosResf);
			drawPieChart("aquePav"+i+"UH"+j,"inaquePav"+i+"UH"+j, resultadosResf);
			// drawPieChart("confPav"+i+"UH"+j,"inconfPav"+i+"UH"+j, resultadosResf);
		}
	}
}


</script>

	

</head>

<body >



	<form id="formRel" name="formRel" method="post" action="relatorio2.php">
	</form>
	<div id="futureCharts">
	</div>


<?php
	echo "<script>createInputChart('inPavs', '". $_POST["inPavs"]."');</script>";
	echo "<script>createInputChart('inTipo', '".$_POST["inTipo"]."');</script>";
	// $html.="<input type='hidden' id='inPavs' name='inPavs' value />"
	for ($nPavs = 1  ; $nPavs<= intval($_POST["inPavs"]);$nPavs++){
		//Para cada UH do pavimento

		if ($_POST['inTipo']=="Multifamiliar"){
			if ($nPavs==1){
				$html.='<h1>Térreo</h1><hr/>';
			} else if ($npavs==intval($_POST["inPavs"])){
				$html.='<h1>Cobertura</h1><hr/>';
			} else{
				if ($_POST['inRepete']=="1"){
					$html.='<h1>Pavimento Intermediário</h1><hr/>';
				} else{
					$html.='<h1>Pavimento '.$nPavs.'</h1><hr/>';					
				}
			}
		}
		echo "<script>createInputChart('inPav".$nPavs."', '".$_POST["inPav".$nPavs]."');</script>";
		for ($nUHs = 1 ; $nUHs<=intval($_POST["inPav".$nPavs]); $nUHs++){

			echo "<script>createInputChart('inresfPav".$nPavs."UH".$nUHs."', '');</script>";
			echo "<script>createInputChart('inaquePav".$nPavs."UH".$nUHs."', '');</script>";
			echo "<script>createInputChart('inconfPav".$nPavs."UH".$nUHs."', '');</script>";
			echo "<script>createInputChart('inPav".$nPavs."UH".$nUHs."AreaUtil', '".$_POST["inPav".$nPavs."UH".$nUHs."AreaUtil"]."');</script>";
			echo "<script>createInputChart('inPav".$nPavs."UH".$nUHs."VentCruzada', '".$_POST["inPav".$nPavs."UH".$nUHs."VentCruzada"]."');</script>";

			$ambs = array();
			$areaUtilTotal=0;
			$areaUtilTotalUH=floatval($_POST["inPav".$nPavs."UH".$nUHs."AreaUtil"]);
			$contE = 1;
			$contD = 1;
			$ventilacaoCruzadaUH="Ausência";
			if (intval($_POST["inPav".$nPavs."UH".$nUHs."VentCruzada"])==1){
				$ventilacaoCruzadaUH="Presença";
			}
			echo "<script>createInputChart('inPav".$nPavs."UH".$nUHs."', '".$_POST["inPav".$nPavs."UH".$nUHs]."');</script>";

			for ($nAmbs = 0; $nAmbs<intval($_POST["inPav".$nPavs."UH".$nUHs]);$nAmbs++){
			// echo "<script>createInputChart('inPav".$nPavs."UH".$nUHs."Amb"."', '".$_POST["inPav".$nPavs."UH".$nUHs."VentCruzada"]."');</script>";
			echo "<script>createInputChart('inPav".$nPavs."UH".$nUHs."Amb".$nAmbs."', '".$_POST["inPav".$nPavs."UH".$nUHs."Amb".$nAmbs]."');</script>";


				$ambTxt = $_POST["inPav".$nPavs."UH".$nUHs."Amb".$nAmbs];
				$amb = explode("|", $ambTxt);
				$nomeamb="";
					$somb="";
					$AbsCob="";
					$CtCob="";
					$AbsPar="";
					$UParExt="";
					$CtParExt="";
					$Uvid="";
					$FsVid="";
					$FatVen="";
					$Resf="";
					$Aque="";
					$Conf="";
					$ucob="";
				for ($campoN=0;$campoN<sizeof($amb);$campoN++){
					$campo = explode("=",$amb[$campoN]);
					if ($campo[0]=="Somb"){
						$somb = $campo[1];
					} else if ($campo[0]=="ER"){
						$Resf = $campo[1];
						$nomeamb = "Estar ".$contE++;
					} else if ($campo[0]=="EC"){
						$Conf = $campo[1];
					} else if ($campo[0]=="EA"){
						$Aque = $campo[1];
					} else if ($campo[0]=="DA"){
						$Aque = $campo[1];
					} else if ($campo[0]=="DR"){
						$Resf = $campo[1];
					} else if ($campo[0]=="DC"){
						$nomeamb = "Dormitório ".$contD++;
						$Conf = $campo[1];
					} else if ($campo[0]=="AbsCob"){
						$AbsCob = $campo[1];
					}else if ($campo[0]=="CtCob"){
						$CtCob = $campo[1];
					}else if ($campo[0]=="AbsPar"){
						$AbsPar = $campo[1];
					}else if ($campo[0]=="UParExt"){
						$UParExt = $campo[1];
					}else if ($campo[0]=="CtParExt"){
						$CtParExt = $campo[1];					
					}else if ($campo[0]=="Uvid"){
						$Uvid = $campo[1];					
					}else if ($campo[0]=="FsVid"){
						$FsVid = $campo[1];					
					}else if ($campo[0]=="FatVen"){
						$FatVen = $campo[1];					
					}else if ($campo[0]=="UCob"){
						$ucob = $campo[1];					
					}else if ($campo[0]=="AreaUtil"){
						$areaUtilTotal += floatval($campo[1]);					
					}
				}
				$ambs[]=array(
					"nomeamb"=>$nomeamb,
					"somb"=>$somb,
					"UCob"=>$ucob,
					"AbsCob"=>$AbsCob,
					"CtCob"=>$CtCob,
					"AbsPar"=>$AbsPar,
					"UParExt"=>$UParExt,
					"CtParExt"=>$CtParExt,
					"Uvid"=>$Uvid,
					"FsVid"=>$FsVid,
					"FatVen"=>$FatVen,
					"Resf"=>$Resf,
					"Aque"=>$Aque,
					"Conf"=>$Conf
					);
			}

	$html .= '
	<div style="width:100%;background-color:darkgray;">
	<table>
		<tr>
			<td>';
			if ($_POST['inTipo']=="Unifamiliar"){
				$html.='<img src="imgs/Casa.png" height="150px" />';
			} else {
				$html.='<img src="imgs/multi_rel.png" height="150px" />';				
			}
			$html .= '
			</td>
			<td>
				<h2><b style="color:white">Habitação ' . $_POST['inTipo'] . '</b></h2>
				<b>Para esta simulação foi escolhida a cidade de <b style="color:white;">Florianópolis</b>. A UH possui <b style="color:white;">'.$areaUtilTotalUH.' m²</b> de área útil,
				sendo que os seus <b style="color:white;">'.($contD+$contE-2).'</b> ambientes de permanência prolongada possuem ao total <b style="color:white;">'.$areaUtilTotal.' m²</b> de área.
				Há a <b style="color:white;">'.$ventilacaoCruzadaUH.'</b> de ventilação cruzada e ao total a UH possui <b style="color:white;">'.($contD-1).'</b> dormitórios.</b>
			</td>
		</tr>
	</table>
	</div>
	<br/>
	<table width="100%">
		<tr align="center">
	 
			<td >
				<div id="resfPav'.$nPavs.'UH'.$nUHs.'" style="background: transparent url(\'imgs/resfriamento.png\') no-repeat  center;"> </div>
			</td>
			<td>
				<div id="aquePav'.$nPavs.'UH'.$nUHs.'" style="background: transparent url(\'imgs/aquecimento.png\') no-repeat  center;"> </div>

			</td>
			<td>
				<div>
				<img src="imgs/conforto.png"/> <br/><br/>
				<img src="imgs/medidor_conforto.png"/> </div>
			</td>
		</tr>
		<tr>
	 
			<td>
<center><span  style="font-size:22px;font-weight:bold;">Consumo Total: ' . $_POST['inResfTotalPav'.$nPavs.'UH'.$nUHs] . ' kWh/m² <br/>';
for ($i=0;$i<sizeof($ambs);$i++){
	$html.=$ambs[$i]["nomeamb"].": ".$ambs[$i]["Resf"]."<br/>";
}


$html.='
</span>
</center>
			</td>
			<td>
<center><span  style="font-size:22px;font-weight:bold;">Consumo Total: ' . $_POST['inAqueTotalPav'.$nPavs.'UH'.$nUHs] . ' kWh/m²<br/>';
for ($i=0;$i<sizeof($ambs);$i++){
	$html.=$ambs[$i]["nomeamb"].": ".$ambs[$i]["Aque"]."<br/>";
}

$html.='</span>
</center>

			</td>
			<td>
<center><span  style="font-size:22px;font-weight:bold;">Total: ' . $_POST['inConfTotalPav'.$nPavs.'UH'.$nUHs] . '%<br/>';
for ($i=0;$i<sizeof($ambs);$i++){
	$html.=$ambs[$i]["nomeamb"].": ".$ambs[$i]["Conf"]."<br/>";
}

$html.='</span>
</center>
			</td>
		</tr>

	</table>
	<br/>
	<br/>
	<table width="100%" align="center" style="text-align:center;border: 0mm solid black;border-collapse: collapse;font-size:16px;">
		<tr style="background-color:lightgray;">
			<td style="height:40px;">
				<b>Amb </b>
			</td>			
			<td style="height:40px;">
				<b>AbsCob </b>
			</td>
			<td>
				<b>Ucob </b>
			</td>
			<td>
				<b>CtCob</b>
			</td>
			<td>
				<b>AbsPar </b>
			</td>
			<td>
				<b>UParExt </b>
			</td>
			<td>
				<b>CtPartExt</b>
			</td>
			<td>
				<b>uVid </b>
			</td>
			<td>
				<b>FsVid </b>
			</td>
			<td>
				<b>Fvent</b> 
			</td>
			<td>
				<b>Somb</b>
			</td>
		</tr>';

 for ($i=0;$i<sizeof($ambs);$i++){
 	$html.="<tr>";
	$html.='<td style="height:40px;">'.$ambs[$i]["nomeamb"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["AbsCob"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["UCob"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["CtCob"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["AbsPar"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["UParExt"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["CtParExt"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["Uvid"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["FsVid"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["FatVen"].'</td>';			
	$html.='<td style="height:40px;">'.$ambs[$i]["somb"].'</td>';			

 	$html.="</tr>";
}


 $html.='<tr style="background-color:lightgray;">
			<td style="height:40px;">
			</td>			
			<td style="height:40px;">
			</td>
			<td>
				W/m²K			
			</td>
			<td>
				KJ/m²K
			</td>
			<td>
			</td>
			<td>
				W/m²K			
			</td>
			<td>
				KJ/m²K
			</td>
			<td>
				W/m²K
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
		</tr>

	</table>';
		if ($nUHs!=intval($_POST["inPav".$nPavs])||$nPavs!=intval($_POST["inPavs"]))
			$html.='<pagebreak />';
        

		}
	}
	echo $html;
	echo "<script>makeCharts();</script>";
	echo "<br/><center>	<button id='rel' class='btn btn-lg btn-primary' onclick='document.getElementById(\"formRel\").submit();'>Gerar PDF</button></center>";
?>


</body>
</html>
