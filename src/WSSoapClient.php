<?php

class MyClient extends SoapClient{

    private $OASIS;
    private $username;
    private $password;
    private $passwordType;
    private $registXML;

    public function setOASIS($o){
        $this->OASIS=$o;
    }
    

    private function generatePasswordDigest()
	{
		$this->nonce = mt_rand();
		$this->timestamp = gmdate('Y-m-d\TH:i:s\Z');
		
		$packedNonce = pack('H*', $this->nonce);
		$packedTimestamp = pack('a*', $this->timestamp);
		$packedPassword = pack('a*', $this->password);
		print_r($this->password);
		print_r("\n");
		print_r($packedPassword);
		print_r("\n");
		
		$hash = sha1($packedNonce . $packedTimestamp . $packedPassword);
		$packedHash = pack('H*', $hash);
		
		return base64_encode($packedHash);
    }
    
    private function generateWSSecurityHeader()
	{
		if ($this->passwordType === 'PasswordDigest')
		{	
			
			$password = $this->generatePasswordDigest();
			$nonce = sha1($this->nonce);
		}
		elseif ($this->passwordType === 'PasswordText')
		{
			$password = $this->password;
			$nonce = sha1(mt_rand());
		}
		else
		{
			return '';
		}

		$xml = '<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="'.$this->OASIS.'/oasis-200401-wss-wssecurity-secext-1.0.xsd">
	            <wsse:UsernameToken>
	            <wsse:Username>' . $this->username . '</wsse:Username>
	            <wsse:Password Type="' . $this->OASIS . '/oasis-200401-wss-username-token-profile-1.0#' . $this->passwordType . '">' . $password . '</wsse:Password>
	            <wsse:Nonce EncodingType="' . $this->OASIS . '/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . $nonce . '</wsse:Nonce>';
		
		if ($this->passwordType === 'PasswordDigest')
		{
			$xml .= "\n\t" . '<wsu:Created xmlns:wsu="' . $this->OASIS . '/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $this->timestamp . '</wsu:Created>';
		}
		
		$xml .= '</wsse:UsernameToken></wsse:Security>';
		
		return new SoapHeader(
			$this->OASIS . '/oasis-200401-wss-wssecurity-secext-1.0.xsd',
			'Security',
			new SoapVar($xml, XSD_ANYXML),
			true);
	}

    public function __setUsernameToken($username, $password, $passwordType)
	{
		$this->username = $username;
		$this->password = $password;
		$this->passwordType = $passwordType;
    }
    
    public function saveXML($registXML){
        $this->registXML=$registXML;
    }


    public function __call($function_name, $arguments){
        //print_r($function_name);
        $this->__setSoapHeaders($this->generateWSSecurityHeader());
        $result=parent::__call($function_name, $arguments);

        if($this->registXML==true){
            $filename="xml-messages/".$function_name.".xml";
            $filename_head="xml-messages/".$function_name."_header_".".xml";
            $file=fopen($filename, "w") or die("Unable to open file!");
            $file_head=fopen($filename_head, "w") or die("Unable to open file!");
            $xml=parent::__getLastRequest();
            $head=parent::__getLastRequestHeaders();
            fwrite($file, $xml);
            fwrite($file_head,$head);
        }
        return $result;
    }
}

?>