<?php

declare(strict_types=1);

trait BHKWvaluePP
{
	private function WriteLog1($data)
	{
		IPS_LogMessage("BHKW valuePP TestLog", $data);
	}
	
	private function valuePP($data)
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
		
		//Seriennummer des BHKW'S
		$ScriptData['Ser'] = (string) $xmlData->valuePP->serial;			
		$SerID = $this->GetIDForIdent("Seriennummer");
		//IPS_LogMessage("BHKW statePower Status", $ScriptData['STATUS']);
		SetValueInteger ($SerID, $ScriptData['Ser']);
	}
}
