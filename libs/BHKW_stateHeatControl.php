<?php

declare(strict_types=1);

trait BHKWstateHeatControl
{
	private function WriteLog4($data)
	{
		IPS_LogMessage("BHKW stateHeatControl TestLog", $data);
	}
	
	private function stateHeatControl($data)
	{
		try
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			//echo "Alles ok!";
		}
 			catch(Exception $ex)
		{
			//print_r($ex);
			IPS_LogMessage("BHKW stateHeatControl Fehler:", $data);
		}
		
		//Status des BHKW'S
		$ScriptData['MODE'] = (string) $xmlData->mode;			
		$StatusID = $this->GetIDForIdent("HeizungsStatus");
		IPS_LogMessage("BHKW stateHeatControl Mode", $ScriptData['MODE']);
		switch ($ScriptData['MODE']) 
		{
			case "automatic":
			SetValueInteger ($StatusID, 1);
			break;
		case "day":
			SetValueInteger ($StatusID, 2);
			break;
		case "night":
			SetValueInteger ($StatusID, 3);
			break;
		default:
			//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
		}

	}
}
