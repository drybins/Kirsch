<?php

declare(strict_types=1);

trait BHKWstateExternal
{
	private function WriteLog10($data)
	{
		IPS_LogMessage("BHKW stateExternal TestLog", $data);
	}
	
	private function stateExternal($data)
	{
		try
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			//echo "Alles ok!";
		}
 			catch(Exception $ex)
		{
			//print_r($ex);
			IPS_LogMessage("BHKW stateExternal Fehler", $data);
		}
		
		$ScriptData['R1'] = (string) $xmlData->R1;
		switch ($ScriptData['R1']) 
		{
		case "on":
			SetValueBoolean($this->GetIDForIdent("R1"), true);
			break;
		case "off":
			SetValueBoolean ($this->GetIDForIdent("R1"), false);
			break;
		default:
			//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
		};

	}
}
