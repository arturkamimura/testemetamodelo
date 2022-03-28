<html>
<head>
   <!-- Desenvolvido por Karran Besen em 2015 no Laboratório de eficiência energética em edificações -->
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Parse PMML ANN To JavaScript</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- Latest compiled JavaScript -->
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>	
	<!-- Modelo -->
	<script src="model/ann/Ann.js" type="text/javascript"></script>
	<script src="model/ann/ActivationFunction.js" type="text/javascript"></script>
	<script src="model/ann/neuron/Cons.js" type="text/javascript"></script>
	<script src="model/ann/neuron/Neuron.js" type="text/javascript"></script>
	<script src="model/ann/layer/Layer.js" type="text/javascript"></script>	
	<script src="model/util/XMLReader.js" type="text/javascript"></script>	
	<script src="model/util/CSVReader.js" type="text/javascript"></script>	
	<script src="model/ann/AnnLoader.js" type="text/javascript"></script>	
	<!-- Libs -->
	<script src="libs/jquery.csv.js" type="text/javascript"></script>	
	<!-- Visão -->
	<script src="view/EditAnn.js" type="text/javascript"></script>	

</head>
  <style>
  body {
    padding-top: 50px;
  } 
  .starter-template {
    padding: 40px 15px;
    text-align: center;
  }
  </style>


<body onload="">
<!-- Visão/Header para avisar que está em desenvolvimento -->
<div id="header">
	<div class="alert alert-warning" role="alert"><strong><p>Aviso:</p></strong>
	Esse sistema está em desenvolvimento, qualquer erro notifique suporte.labeee@gmail.com .
	</div>
</div>


<!---->

<?PHP

//TODO MUDAR PRA LOGIN VIA REST


//set the working directory to your Drupal root
define("DRUPAL_ROOT",     "/home/pbeedifica/www");

//require the bootstrap include
require_once '../includes/bootstrap.inc';

//Load Drupal
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL); 
//(loads everything, but doesn't render anything)

$name = $_POST['user'];
$password = $_POST['pass'];

//use drupal user_authenticate function


$user = user_authenticate($name, $password);

if (!$user){
	echo "Login Errado";
} else {



?>
	<!-- MENU -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">PMML_To_JS</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li id="gerenciarRedes" ><a href="#" onclick="getANNs();">Gerenciar Redes</a></li>
            <li><a href="#about" onclick="getDefaults();">Gerenciar Padrões</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav><!-- MENU -->

	
	
    <div class="container">
	
		<div id="DManageANNs" style="display:block" class="starter-template">
		</div>
		
		<div id="pmmlLoader" align="left">
			<strong>Adicionar rede:</strong>
			<form enctype="multipart/form-data" method="post">
				<input type="file" name="odfxml" id="odfxml" class="btn btn-default" />
			</form>	
			</br>
		</div>

		<div id="defaultLoader" align="left" style="display:none">
			<div class="panel-body" id="dNetworks">
						Redes: <SELECT class="form-control" style="margin-left:10px;margin-top:10px;width:200px" id="cbANNs" onchange="newDefault(this)"><option>Selecione uma rede</option> </SELECT>
			</div>
		</div>

		<div id="DEditANN" style="display:none" class="starter-template">
		</div>
		
    </div><!-- /.container -->



<script>
	var ann;
	

	function enviarANN()	{
			indexView.showLayers(ann.getLayers());			
			var dados = jQuery( "#searchForm ").serialize();

			jQuery.ajax({
				type: "POST",
				url: "callback.php",
				async: true,
				data: dados,
				success: function( data ){
					getANNs();
				}
			});	}
	
	function excluir(_id){
		var r = confirm("Deseja realmente excluir esse campo?");
		if (r == true) {
		    $.ajax({
				type: 'POST',
				url: 'callback.php',
				async: true,
				data: {func: "delete", campo: _id},
				success: function(response) {
					getANNs();
				}             
			}); 
		}
	}
	function getDefaults(){
		document.getElementById("DManageANNs").style.display="block";
		document.getElementById("pmmlLoader").style.display="none";	
		document.getElementById("defaultLoader").style.display="block";	
		document.getElementById("DEditANN").style.display="none";
		$.ajax({
			type: 'POST',
			url: 'callback.php',
			async: true,
			data: {func: "listDefaults"},
			success: function(response) {
				listDefaults(response);
			}             
		}); 

	}
	function getANNs(){
		document.getElementById("DManageANNs").style.display="block";
		document.getElementById("pmmlLoader").style.display="block";	
		document.getElementById("defaultLoader").style.display="none";	
		document.getElementById("DEditANN").style.display="none";
		$.ajax({
			type: 'POST',
			url: 'callback.php',
			async: true,
			data: {func: "listANNs"},
			success: function(response) {
				listANNs(response);
			}             
		}); 
	}

	function loadANN(_id, ann){
		$.ajax({
			type: 'POST',
			url: 'callback.php',
			async: true,
			data: {func: "load", campo: _id},
			success: function(response) {
				AnnLoader(response, ann);
				ann.showInputs();
			}             
		}); 
	}
	
	function listANNs(list){
		teste= document.getElementById("DManageANNs");
		teste.innerHTML = '<h1>Gerenciar Redes</h1>\
		    <table id="tAnns" class="table table-striped">\
		    	<thead>\
		          <tr>\
		            <th>#</th>\
		            <th>Nome da Rede</th>\
		            <th colspan="2">Ações</th>\
		          </tr>\
		        </thead>\
		        <tbody>\
		        </tbody>\
		    </table>\
';
		var resultArray = JSON.parse(list);
 		for (x in list){
 			if (resultArray[x]!=undefined){				
				$('#tAnns > tbody:last-child').append('<tr><td>'+resultArray[x]['id']+'	</td><td>'+resultArray[x]['pmml']+'</td><td><img style="cursor:pointer;" src="icos/b_edit.png" onclick="editar('+resultArray[x]['id']+')" /></td><td><img style="cursor:pointer;" src="icos/b_drop.png" onclick="excluir('+resultArray[x]['id']+')" /></td>');
			}
		}
	}

	function listDefaults(list){
		teste= document.getElementById("DManageANNs");
		teste.innerHTML = '<h1>Gerenciar Padrões</h1>\
		    <table id="tAnns" class="table table-striped">\
		    	<thead>\
		          <tr>\
		            <th>#</th>\
		            <th>Nome do Padrão</th>\
		            <th colspan="2">Ações</th>\
		          </tr>\
		        </thead>\
		        <tbody>\
		        </tbody>\
		    </table>\
';
		var resultArray = JSON.parse(list);
 		for (x in list){
 			if (resultArray[x]!=undefined){				
				$('#tAnns > tbody:last-child').append('<tr><td>'+resultArray[x]['id']+'	</td><td>'+resultArray[x]['pmml']+'</td><td><img style="cursor:pointer;" src="icos/b_edit.png" onclick="editar('+resultArray[x]['id']+')" /></td><td><img style="cursor:pointer;" src="icos/b_drop.png" onclick="excluir('+resultArray[x]['id']+')" /></td>');
			}
		}
	}


	var indexView = new EditAnn("DEditANN");

	function editar(id){
		indexView = new EditAnn("DEditANN");
		document.getElementById("DManageANNs").style.display="none";
		document.getElementById("pmmlLoader").style.display="none";
		document.getElementById("DEditANN").style.display="block";
		ann = new ANN(indexView);
		
		if (id=="new"){
			var reader = new CSVReader(document.getElementById("odfxml").files[0], ann);
		}else{
			var loader  = loadANN(id, ann);
			indexView.setEditMode();
		}	
	}

    
	//Limpa os Inputs e cria uma nova ANN quando carrega um novo PMML
	$("#odfxml").change(function(){
		editar("new");
    });

	//TODO Função mostrar e esconder
	document.getElementById("DEditANN").style.display="none";
	document.getElementById("DManageANNs").style.display="block";
	document.getElementById("pmmlLoader").style.display="block";	
	getANNs();
	
	// function testaHash(){	
	// 	if(window.location.hash) {
	// 		var hash = window.location.hash.substring(1); 
	// 		DManageANNs.style.display="none";
	// 	} else{
	// 		alert("eita");	
	// 	} 
	// }
	/*
	SALVAR BANCO DE DADOS
	PEGAR DO BANCO DE DADOS
	MESMA COISA PROS PADRÕES

	 */

	 $("cbANNs").click(
		$.ajax({
			type: 'POST',
			url: 'callback.php',
			async: true,
			data: {func: "listANNs"},
			success: function(response) {
				var sel = $("#cbANNs");
				var resultArray = JSON.parse(response);
				for (x in response){
					if (resultArray[x]!=undefined){		
						sel.append("<option value='"+resultArray[x]['id']+"'>"+resultArray[x]['pmml']+"</option>");
					}
				}

			}             
		})
	);
</script>
<?php

}

?>

  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>  
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
</body>
</html>