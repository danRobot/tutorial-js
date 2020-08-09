<?php

class MyClient extends SoapClient{
    public function __call($function_name, $arguments){
        $filename="xml-messages/".$function_name.".xml";
        //print_r($function_name);
        $result=parent::__call($function_name, $arguments);
        $file=fopen($filename, "w") or die("Unable to open file!");
        $xml=parent::__getLastRequest();
        fwrite($file, $xml);
        return $result;
    }
}

$url='https://correo-certificado.technokey.co/webService.php?WSDL';
try{
    $cliente=new MyClient($url, [ "trace" => 1 ] );
}catch(SoapFault $e){
    echo $e->getMessage();
}

$username='usuario.prueba@gmail.com';
$password='Sigee0Sigee*';

$m=[ "idUsuario" => $username,
    "Asunto"=>"Asunto",
    "Texto"=>"Texto",
    "NombreDestinatario"=>"destino",
    "CorreoDestinatario"=>"corr",
    "Adjunto"=>"",
    "NombreArchivo"=>"",
    "Alertas"=>True,0];

$result=$cliente->RegistrarMensaje($m);
print_r($result);


?>