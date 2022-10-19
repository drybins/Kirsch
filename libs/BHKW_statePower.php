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
		$StatusID = $this->GetIDForIdent("KirschStatus");
		IPS_LogMessage("BHKW statePower Status", $ScriptData['STATUS']);
				
		switch ($ScriptData['STATUS']) 
		{
			case "stop":
			SetValueInteger ($StatusID, 1);
			break;
		case "start":
			SetValueInteger ($StatusID, 2);
			break;
		case "warmup":
			SetValueInteger ($StatusID, 3);
			break;
		case "running":
			SetValueInteger ($StatusID, 4);
			break;
		case "cooldown":
			SetValueInteger ($StatusID, 5);
			break;
		case "selftest":
			SetValueInteger ($StatusID, 6);
			break;
		case "emergencystop":
			SetValueInteger ($StatusID, 10);
			break;  
		case "error":
			SetValueInteger ($StatusID, 11);
			break;         
		default:
			//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
		}
	}
}