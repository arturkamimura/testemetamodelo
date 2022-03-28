/*
  To-do
- mais zonas
- borda só na mesma tipologia/pavimento (recuperar ao trocar tipologia/pavimento)
- funcionar tabs
- valor de referência
*/




var indexView = new IndexView();
var annR = new ANN(indexView);

pegarIdRede();

function edToAlert(){
 console.log(edificacao);
}

function getQueryParams(qs) {
    qs = qs.replace(/\+/g, " ");
    var params = {},
        re = /[?&]?([^=]+)=([^&]*)/g,
        tokens;
 
    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }
    return params;
}


function pegarIdRede(){
  var query = getQueryParams(document.location.search);
  if (query.id!=undefined){
      loadANN(query.id, annR);
  }
}

// classe necessária por causa de algo assíncrono =(
function IndexView (){
 
  this.showOutput = function(output){
  }
  //Mostra o input
  this.showOutputToEdit = function(output){
  }
  this.setName = function(_name){
  }
  this.setProjeto = function(_name){
  } 
  this.setTipo = function(_name){
  } 
  this.setRevisao = function(_name){
  }
  this.setTraining = function(_name){
  }
  this.setEditMode = function(){
  }
  this.setId = function(_id){ }
  this.showLayer = function(layer){
  }
  this.showLayers = function (layers){
  }
  this.showInput = function(input){
    var min  = input.getMin();
    var espacamento = (input.getEspacamento()!="0" ?  "45" : "0");
    var tipow = "input";
    switch(input.getTipo()){
      case "0":
        tipow = "input";
        break;
      case "1":
        tipow= "select";
        break;
      case "2":
        tipow="especial";
        break;
    }
   fields.push({id: input.getId(),  texto:input.getHint(), tipo: tipow, index: input.getName(), opcoes: input.getOpcoes(),  copiar:true, espaco: espacamento, min: input.getMin(), max: input.getMax()});
  }
}



function retornaUH(){
  var uh = {ambientes:null, ambs:null}
  uh.ambientes= parseFloat(document.getElementById("i3").value.replace(",",".")) || "" ;
  uh.ambs = new Array();
  var ambientes = parseInt(document.getElementById("i3").value.replace(",","."));
  for (var i=0;i<ambientes;i++){
    var amb = new Array();
    indexAnn = i+1;
    for (var j=0;j<fields.length;j++){
      var valor = 1;
      var ide = fields[j].id;
      if (fields[j].tipo=="especial"){
        valor = document.getElementById(fields[j].index).value;
      } else {
        valor = parseFloat(document.getElementById("a"+indexAnn+"_"+ide).value.replace(",","."));
      }
      amb.push({seq: j, id: ide, name: fields[j].index, value:valor});
    }
    var valor =  parseFloat(document.getElementById("sResultadoResf"+indexAnn).innerHTML) || null;
    amb.push({resultado: "r",  value: valor}) ;
    uh.ambs.push(amb);
  }
  return uh;
}


function zeraUH(){
  document.getElementById("i3").selectedIndex = -1;
  roomChange();
  for (var i=0;i<6;i++){
    indexAnn = i+1;
    for (var j=0;j<fields.length;j++){
      if (fields[j].tipo=="select"){
        document.getElementById("a"+indexAnn+"_"+fields[j].id).selectedIndex =  -1 ;
      } else if (fields[j].tipo=="input"){
        document.getElementById("a"+indexAnn+"_"+fields[j].id).value =  "" ;

        if (fields[j].index=="ParExtN" ||fields[j].index=="ParExtS" ||fields[j].index=="ParExtL" ||fields[j].index=="ParExtO" ||fields[j].index=="AVaoN"||fields[j].index=="AVaoS"||fields[j].index=="AVaoL"||fields[j].index=="AVaoO")
          document.getElementById("a"+indexAnn+"_"+fields[j].id).value =  "0" ;
      }
    }
  }
}


function acharItem(_sel, _valor){
    var sel = document.getElementById(_sel);
    for (var i=0; i<sel.options.length;i++ ){
      sel.selectedIndex=i;
      if (sel.value==_valor){
        return i;
      }
    }
    sel.selectedIndex=-1;
}


function carregaUH(uh){

  // if ()
  if (uh.ambientes=="")
    document.getElementById("i3").selectedIndex = -1;
  else
    document.getElementById("i3").selectedIndex = uh.ambientes-1;

  roomChange();

  var quant=0;
  if (uh.ambientes!=""){
    quant = uh.ambientes;
  }

  for (var i=0;i<quant;i++){
    var amb = uh.ambs[i];
    indexAnn = i+1;

    for (var j=0;j<amb.length;j++){
      if (amb[j].resultado==undefined){
        if (fields[amb[j].seq].tipo=="select"){
          acharItem("a"+indexAnn+"_"+amb[j].id, amb[j].value);
          // document.getElementById("a"+indexAnn+"_"+amb[j].id).selectedIndex =  amb[j].value-1 ;
        } else if (fields[amb[j].seq].tipo=="input"){
          if (amb[j].value.toString()=="NaN"){
            document.getElementById("a"+indexAnn+"_"+amb[j].id).value = "";            
          } else{
            document.getElementById("a"+indexAnn+"_"+amb[j].id).value = amb[j].value.toString().replace(".",",") ;
          }
        }
      } else{
        document.getElementById("sResultadoResf"+indexAnn).innerHTML = amb.resf || "";
      }
    }

  }
}



function escondeMostraDadosUH(){
  if (document.getElementById("dUH").style.display=="none"){
    mostraDadosUH();
  } else {
    escondeDadosUH();
  }
}

function escondeDadosUH(){
  document.getElementById("dUH").style.display="none";
  document.getElementById("sEscondeDadosUH").innerHTML="+";
}

function mostraDadosUH(){
  document.getElementById("dUH").style.display="block";
  if (document.getElementById("i3").selectedIndex!=-1)
  {
    document.getElementById("sEscondeDadosUH").innerHTML="-";
  } else{
    document.getElementById("sEscondeDadosUH").innerHTML="";
  }
}

function escondeMostraEdi(){
  if (document.getElementById("dEdificacao").style.display=="none"){
    mostraEdi();
  } else {
    escondeEdi();

  }
}
function escondeEdi(){
  document.getElementById("dEdificacao").style.display="none";
  document.getElementById("sEscondeEdif").innerHTML="+";
}

function mostraEdi(){
  document.getElementById("dEdificacao").style.display="block";
  if (document.getElementById("p1").value!="" && document.getElementById("p2").value!="")
  {
    document.getElementById("sEscondeEdif").innerHTML="-";
  } else{
    document.getElementById("sEscondeEdif").innerHTML="";
  }
}

function mostraResultado(){
  escondeIlustracao();
  document.getElementById("trResultadosResfriamento").style.display="table-row";
  document.getElementById("dResultado").style.display="block";
}

function escondeResultado(){
  document.getElementById("dResultado").style.display="none";
  document.getElementById("trResultadosResfriamento").style.display="none";
}


 
function escondeIlustracao(){
  // document.getElementById("dIlustracao").style.display="none";
}

function mostraIlustracao(){
  // document.getElementById("dIlustracao").style.display="block";
  // escondeResultado();
}

function setTextoExplicativo(nomeCampo, texto){
 document.getElementById("textoExplicativo").innerHTML="<strong>"+nomeCampo+": </strong>"+texto;
}

// fazer loops
function constroiTabela(){

  $('#tableInputs > tbody:last-child').append('\
              <tr>\
                  <th class="col-md-1 text-center"></th>\
                  <th class="col-md-1 text-center" ><span id="amb1" style="font-weight:500;font-size:16px;">Zona 1</span></th>\
                  <th class="col-md-1 text-center"></th>\
                  <th class="col-md-1 text-center"><span id="amb2" style="font-weight:500;font-size:16px;">Zona 2</span></th>\
                  <th class="col-md-1 text-center"><span id="amb3" style="font-weight:500;font-size:16px;">Zona 3</span></th>\
                  <th class="col-md-1 text-center"><span id="amb4" style="font-weight:500;font-size:16px;">Zona 4</span></th>\
                  <th class="col-md-1 text-center"><span id="amb5" style="font-weight:500;font-size:16px;">Zona 5</span></th>\
                  <th class="col-md-1 text-center"><span id="amb6" style="font-weight:500;font-size:16px;">Zona 6</span></th>\
              </tr>'
);
  for (var i=0;i<fields.length;i++){
    if (i==1){
      $('#tableInputs > tbody:last-child').append('\
        <tr>\
          <td class="col-md-2" >\
          </td>\
          <td class="col-md-1 text-center">\
          </td>\
          <td class="col-md-1 text-center">\
            <span style="font-size:12px">Copiar ?</span>\
          </td>\
          <td class="col-md-1 text-center">\
          </td>\
          <td class="col-md-1 text-center">\
          </td>\
          <td class="col-md-1 text-center">\
          </td>\
          <td class="col-md-1 text-center">\
          </td>\
          <td class="col-md-1 text-center">\
          </td>\
        </tr>'
       );      
    }
    var field = fields[i];
    var valorescampo = field.texto.split("(");
    valornome=valorescampo[0];
    valorpeso=""
    if (valorescampo.length>1){
      valorpeso="("+valorescampo[1];
    }
    var min=-9999999.9999;
    var max=9999999.9999;
    if (field.min!=field.max){
      min = field.min;
      max = field.max;
    }
    if (field.tipo=="input"){
      $('#tableInputs > tbody:last-child').append('\
        <tr>\
          <td class="col-md-2" style="padding-top:'+field.espaco+'">\
            '+valornome+'<br/><i><span style="font-size:10px">'+valorpeso+'</span></i>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="text" class="auto" data-a-sep="" data-a-dec="," data-v-min="-9999999.9999" data-v-max="9999999.9999" onchange="itemChange('+field.id+', '+min+', '+max+')" name="a1_'+field.id+'" id="a1_'+field.id+'" style="width:100px;">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
          <input type="checkbox" onchange="dupRow(this, '+field.id+')" name="c'+field.id+'" id="c'+field.id+'">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="text" class="auto" data-a-sep="" data-a-dec="," data-v-min="-9999999.9999" data-v-max="9999999.9999" onchange="itemChange('+field.id+')" name="a2_'+field.id+'" id="a2_'+field.id+'" style="width:100px;">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="text" class="auto" data-a-sep="" data-a-dec="," data-v-min="-9999999.9999" data-v-max="9999999.9999" onchange="itemChange('+field.id+')" name="a3_'+field.id+'" id="a3_'+field.id+'" style="width:100px;">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="text" class="auto" data-a-sep="" data-a-dec="," data-v-min="-9999999.9999" data-v-max="9999999.9999" onchange="itemChange('+field.id+')" name="a4_'+field.id+'" id="a4_'+field.id+'" style="width:100px;">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="text" class="auto" data-a-sep="" data-a-dec="," data-v-min="-9999999.9999" data-v-max="9999999.9999" onchange="itemChange('+field.id+')" name="a5_'+field.id+'" id="a5_'+field.id+'" style="width:100px;">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="text" class="auto" data-a-sep="" data-a-dec="," data-v-min="-9999999.9999" data-v-max="9999999.9999" onchange="itemChange('+field.id+')" name="a6_'+field.id+'" id="a6_'+field.id+'" style="width:100px;">\
          </td>\
        </tr>'
      );
    } else if (field.tipo=="select"){
      var o1 = field.opcoes.split("|");
      var optionStrings = "";
      for (var j=0;j<o1.length;j++){
        var o11 = o1[j].split("=");
        optionStrings+='<option value="'+o11[1]+'">'+o11[0]+'</option>';
      }
      $('#tableInputs > tbody:last-child').append('\
        <tr>\
          <td class="col-md-2" style="padding-top:'+field.espaco+'">\
            '+valornome+'<br/><i><span style="font-size:10px">'+valorpeso+'</span></i>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <center><select style="width:100px;"" class="form-control" onchange="itemChange('+field.id+')"  name="'+"a1_"+field.id+'" id="'+"a1_"+field.id+'"">\
            '+optionStrings+'\
            </select></center>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <input type="checkbox" onchange="dupRow2(this, '+field.id+')" name="c'+field.id+'" id="c'+field.id+'">\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <center><select style="width:100px;" class="form-control" onchange="itemChange('+field.id+')"  name="'+"a2_"+field.id+'" id="'+"a2_"+field.id+'"">\
            '+optionStrings+'\
            </select></center>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <center><select style="width:100px;" class="form-control" onchange="itemChange('+field.id+')"  name="'+"a3_"+field.id+'" id="'+"a3_"+field.id+'"">\
            '+optionStrings+'\
            </select></center>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <center><select style="width:100px;" class="form-control" onchange="itemChange('+field.id+')"  name="'+"a4_"+field.id+'" id="'+"a4_"+field.id+'"">\
            '+optionStrings+'\
            </select></center>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <center><select style="width:100px;" class="form-control" onchange="itemChange('+field.id+')"  name="'+"a5_"+field.id+'" id="'+"a5_"+field.id+'"">\
            '+optionStrings+'</center>\
            </select>\
          </td>\
          <td class="col-md-1 text-center" style="padding-top:'+field.espaco+'">\
            <center><select style="width:100px;" class="form-control" onchange="itemChange('+field.id+')"  name="'+"a6_"+field.id+'" id="'+"a6_"+field.id+'"">\
            '+optionStrings+'\
            </select></center>\
          </td>\
        </tr>'
      );
    }

    if (!field.hasOwnProperty('copiar')) {
      document.getElementById('c'+field.id).style.display="none";
    }
    if (field.hasOwnProperty('padrao')) {
      document.getElementById('a1_'+field.id).value=field.padrao;
      document.getElementById('a2_'+field.id).value=field.padrao;
      document.getElementById('a3_'+field.id).value=field.padrao;
      document.getElementById('a4_'+field.id).value=field.padrao;
      document.getElementById('a5_'+field.id).value=field.padrao;
      document.getElementById('a6_'+field.id).value=field.padrao;
    }        
  }
  $('#tableInputs > tbody:last-child').append('\
    <tr id="trResultadosResfriamento" style="display:none">\
      <td class="col-md-2">\
        <img style="width:30px;height:30px" src="../imgs/resfriamento.png">\
        <span style="font-size:12px;font-weight:bold">Resfriamento: </span>\
      </td>\
      <td class="col-md-1 text-center">\
        <span id="sResultadoResf1" style="font-weight:bold"></span>\
      </td>\
      <td class="col-md-1 text-center">\
      </td>\
      <td class="col-md-1 text-center">\
        <span id="sResultadoResf2" style="font-weight:bold"></span>\
      </td>\
      <td class="col-md-1 text-center"\
        <span id="sResultadoResf3" style="font-weight:bold"></span>\
      </td>\
      <td class="col-md-1 text-center">\
        <span id="sResultadoResf4" style="font-weight:bold"></span>\
      </td>\
      <td class="col-md-1 text-center">\
        <span id="sResultadoResf5" style="font-weight:bold"style="font-weight:bold"></span>\
      </td>\
      <td class="col-md-1 text-center">\
        <span id="sResultadoResf6" style="font-weight:bold"></span>\
      </td>\
    </tr>'
  );


for (var i=0;i<6;i++){
    indexAnn = i+1;
    for (var j=0;j<fields.length;j++){
      if (fields[j].tipo=="select"){
        document.getElementById("a"+indexAnn+"_"+fields[j].id).selectedIndex =  -1 ;
      } else if (fields[j].tipo=="input"){
        if (fields[j].index=="ParExtN" ||fields[j].index=="ParExtS" ||fields[j].index=="ParExtL" ||fields[j].index=="ParExtO" ||fields[j].index=="AVaoN"||fields[j].index=="AVaoS"||fields[j].index=="AVaoL"||fields[j].index=="AVaoO")
        document.getElementById("a"+indexAnn+"_"+fields[j].id).value =  "0" ;
      }
    }
  }

          $('.auto').autoNumeric('init');
          // ativaHints();
}


function itemChange(row){
  document.getElementById('c'+row).checked=false;
}

function itemChange(row, min, max){
  for (var i=1;i<7;i++){
    var val = Number(document.getElementById("a"+i+"_"+row).value.replace(",","."));
    if ((val<min || val>max)&&(val!="")){
      document.getElementById("a"+i+"_"+row).style.border = "1px solid red";
      document.getElementById("a"+i+"_"+row).value = "";

    } else{
      document.getElementById("a"+i+"_"+row).style.border = '';
    }
  }
  document.getElementById('c'+row).checked=false;

}


for (var i=0;i<9;i++){
  $('#tableInputs td:nth-child('+(i)+'),th:nth-child('+(i)+')').hide();
}

function roomChange(){
  var zonas = parseInt(document.getElementById("i3").value) || 0;
  if (zonas==0)
  {
    for (i=9;i>0;i--){
    $('#tableInputs td:nth-child('+(i)+'),th:nth-child('+(i)+')').hide();
  }
    return 0;
  }
  sTXTAmbientes.style.display="block";
  for (i;i<9;i++){
    $('#tableInputs td:nth-child('+(i)+'),th:nth-child('+(i)+')').show();

  }
  for (i=6;i>zonas;i--){
    $('#tableInputs td:nth-child('+(i+2)+'),th:nth-child('+(i+2)+')').hide();
  }

  if (zonas==1){
    $('#tableInputs td:nth-child('+(i+2)+'),th:nth-child('+(i+2)+')').hide();    
  }
}

function dupRow2(cbox, r){
  if (cbox.checked){
    for (var i=2;i<7;i++){ 
      document.getElementById('a'+i+'_'+r).selectedIndex= document.getElementById('a'+1+'_'+r).selectedIndex;      
    }
  } else{
   for (var i=2;i<7;i++){ 
      document.getElementById('a'+i+'_'+r).selectedIndex= 0;      
    }
  }
}  

function dupRow(cbox, r){
  if (cbox.checked){
    for (var i=2;i<7;i++){     
      document.getElementById('a'+i+'_'+r).value = document.getElementById('a'+1+'_'+r).value;
    }
  } else{
    for (var i=2;i<7;i++){     
      document.getElementById('a'+i+'_'+r).value = "";
    }
  }
}
    
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});

function loadANN(_id, ann){
  $.ajax({
    type: 'POST',
    url: '../callback.php',
    async: true,
    data: {func: "load2", campo: _id},
    success: function(response) {
      AnnLoader(response, ann);
      ann.showInputs();
      constroiTabela();
      roomChange();
      loadANN2(_id, ann)
    }             
  }); 
} 

function loadANN2(_id, ann){
  $.ajax({
    type: 'POST',
    url: '../callback.php',
    async: true,
    data: {func: "load", campo: _id},
    success: function(response) {
      annR = new ANN(indexView);
      AnnLoader(response, annR);
    }             
  }); 
} 


function pavFiller(){
  var pav = document.getElementById('p1');
  var pavList = document.getElementById('p3');
  var nPav = pav.value || false;
  while (pavList.options.length) {
    pavList.remove(0);
  }
  if (nPav) {
    while(nPav>edificacao.length){
      edificacao.push({uhs:new Array(), pilotis:0});
    }
    var interIguais = false;
    if (document.getElementById("p2-5").value==1){
      interIguais = true;
    }
    var jafoi = false;
    for (var i=1; i<=nPav;i++){
      if (i==1){
        var option = new Option("Térreo", i);
        pavList.options.add(option);   
      } else if (i==nPav){
        var option = new Option("Cobertura", i);
        pavList.options.add(option); 
      } else{
        if (interIguais){
          if(!jafoi){
            pavList.options.add(new Option ("Intermediários",2));
            jafoi=true;
          }
        } else {
          var option = new Option("Pav "+i, i);
          pavList.options.add(option); 
        }
      }
    }
  }
  pavAtual = 1;
  pavChange();
}

function setUH(num, _primeiro){
  var primeiro = _primeiro || false;
  var uhsAtual = edificacao[pavAtual-1].uhs;
  while(uhsAtual.length<num){
    uhsAtual.push(null);
  }
  if (!primeiro)
    uhsAtual[uhAtual-1]=retornaUH();
  if (uhsAtual[num-1]==null){
    zeraUH();
    uhsAtual[num-1]=retornaUH();
  }
  carregaUH(uhsAtual[num-1]);
  roomChange();
  uhAtual = num;
  mostraDadosUH();
}

function pavChange(){
    var pav = document.getElementById("p3");
    var vPav = pav.value || false;
    if (vPav){
      if (pavAtual==1){
        edificacao[pavAtual-1].pilotis=document.getElementById("p4").value;
      }
      edificacao[pavAtual-1].uhs[uhAtual-1] = retornaUH();
      pavAtual=vPav
      if (vPav==1){
        document.getElementById("trPavPilotis").style.display="table-row";
      } else {
        document.getElementById("trPavPilotis").style.display="none";        
      }
      if (edificacao[pavAtual-1].uhs.length==0){
        setUH(uhAtual, true);
      }
      acharItem("p4", edificacao[pavAtual-1].pilotis);
      carregaUH(edificacao[pavAtual-1].uhs[uhAtual-1]);
      mostraDadosUH();
    } else {
      document.getElementById("dUH").style.display="none";              
    }
}

function uhChange(){
  while ($('#tabs li').size()!=0){
      $('#tabs li').first().remove();
  }
  while ($('#tabs li').size()!=document.getElementById("p2").value){
    var nextTab = $('#tabs li').size()+1;
    // create the tab
    if ($('#tabs li').size()==0){
      $('<li class="active"><a href="#tab'+nextTab+'" onclick="setUH(1)" data-toggle="tab">UH'+nextTab+'</a></li>').appendTo('#tabs');
       setUH(1, true);
    } else {
      $('<li><a href="#tab'+nextTab+'" data-toggle="tab" onclick="setUH('+($('#tabs li').size()+1)+')">UH'+nextTab+'</a></li>').appendTo('#tabs');
    }
  }
}


// ARRUMAR ESTE TROÇO
function calcMulti(){
  // console.log("Pavimento atual: "+pavAtual);
  // console.log("Tipologia atual: "+uhAtual);
  edificacao[pavAtual-1].uhs[uhAtual-1] = retornaUH();
  var quantidadeTotalPavimentos = document.getElementById("p1").value;

  var quantidadeTotalUHS = 1;

  var repete = document.getElementById("p2-5").value==1;
  var totalResfriamento = 0;

  for (var i=0;i<quantidadeTotalPavimentos;i++){
    var l = i;
    if (i!=0 && i!=quantidadeTotalPavimentos-1){
      if (repete){
        l=1;
      }  
    } else if (i==quantidadeTotalPavimentos-1 && repete){
    }

    for (var j=0;j<=quantidadeTotalUHS-1;j++){
      // console.log(edificacao[l].uhs[j]);
      // console.log(edificacao[l].uhs[j].ambientes);
      if (edificacao[l].uhs[j]==null){
        if ((repete) && (i!= quantidadeTotalPavimentos-1)){
        } else{
          return 0;
        }
      } else {
        if (!repete){
          if (edificacao[l].uhs[j].ambientes==""){
            alert("Existem ambientes não preenchidos");
            return 0;
          }
        }
        for (var k=0;k<edificacao[l].uhs[j].ambientes;k++){
        	var idResfriamento=-1;
    		  for (var m=0;m<edificacao[l].uhs[j].ambs[k].length;m++){
		    	 if (edificacao[l].uhs[j].ambs[k][m].resultado==undefined){
            if (edificacao[l].uhs[j].ambs[k][m].value==-1 || edificacao[l].uhs[j].ambs[k][m].value.toString()=="NaN"){
                console.log("ERRO: "+"a"+k+"_"+m);
                document.getElementById("a"+(k+1)+"_"+(edificacao[l].uhs[j].ambs[k][m].id)).style.border="1px solid red";
                alert("Existe um campo: "+edificacao[l].uhs[j].ambs[k][m].name+", não preenchido!");
                return 0;
            } else {
              var neuronValue = parseFloat(edificacao[l].uhs[j].ambs[k][m].value); 
              if (edificacao[l].uhs[j].ambs[k][m].name=="RoofOutdoors"){
                neuronValue=0;
                if (i==quantidadeTotalPavimentos-1){
                  neuronValue=1;
                }
              } else if (edificacao[l].uhs[j].ambs[k][m].name=="FloorOutdoors"){
                neuronValue=0;
              } else if (edificacao[l].uhs[j].ambs[k][m].name=="FloorGround"){
                neuronValue=0;
                if (i==0){
                  if (edificacao[l].pilotis!=1){
                    neuronValue=1;                  }
                }
              }
              console.log(edificacao[l].uhs[j].ambs[k][m].name+" "+neuronValue);              
		       	 	annR.getNeuron(parseInt(edificacao[l].uhs[j].ambs[k][m].id)).setValue(parseFloat(neuronValue));
            }
      	  }
  				  else 
        			idResfriamento=m;
			    }
        	var valor = annR.calcLayers().get(0).getValue("R");
          valor = Math.pow(10, valor)*20.25;
          console.log(valor);
 
          totalResfriamento+=valor;
    			valor = Number(valor.toFixed(2));
    			edificacao[l].uhs[j].ambs[k][idResfriamento] = valor;
			    if ((uhAtual-1==j) && (pavAtual-1==l))
          	document.getElementById("sResultadoResf"+(k+1)).innerHTML=Number(valor.toFixed(2));
        }         
      }
    }
  }
  mostraResultado();
  document.getElementById("sResfriamentoTotal").innerHTML="<b>Resfriamento Total: </b>" + Number(totalResfriamento.toFixed(2))+" kWh";
}