<?php

declare(strict_types=1);

trait BHKWstatePower
{
	private function WriteLog1($data)
	{
		IPS_LogMessage("BHKW statePower TestLog", $data);
	}
	
	private function statePower($data)
	{
		try
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			//echo "Alles ok!";
		}
 			catch(Exception $ex)
		{
			//print_r($ex);
			IPS_LogMessage("BHKW statePower Fehler", $data);
		}
		
		//Status des BHKW'S
		$ScriptData['STATUS'] = (string) $xmlData->state;			
		//$StatusID = $this->GetIDForIdent("KirschStatus");
		IPS_LogMessage("BHKW statePower Status", $ScriptData['STATUS']);
	}
}
