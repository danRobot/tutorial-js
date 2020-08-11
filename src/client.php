<?php

require("WSSoapClient.php");


$url='https://correo-certificado.technokey.co/webService.php?WSDL';
try{
    $cliente=new MyClient($url, [ "trace" => 1 ] );
}catch(SoapFault $e){
    echo $e->getMessage();
}

$username='usuario.prueba@gmail.com';
$password='Sigee0Sigee*';

$OASIS='https://docs.oasis-open.org/wss/v1.1/';

$cliente->setOASIS($OASIS);

$cliente->__setUsernameToken($username,$password,"PasswordDigest");
$m=[ "idUsuario" => 0,
    "Asunto"=>"Asunto",
    "Texto"=>"Texto",
    "NombreDestinatario"=>"destino",
    "CorreoDestinatario"=>"corr",
    "Adjunto"=>"",
    "NombreArchivo"=>"",
    "Alertas"=>True,0];

$cliente->saveXML(true);
$arr=["idUsuario"=>$username];
$result=$cliente->RegistrarMensaje($m);
print_r($result);
//$result=$cliente->ObtenerToken(["idUsuario" => $username,"idMensaje"=>2]);


//$result=$cliente->RegistrarMensaje($m);
print_r($result);
//print_r(($cliente->__getFunctions()))

?>