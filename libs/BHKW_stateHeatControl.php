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
		for ($x = 0; $x <= 3; $x+=1) 
		{
			$ScriptData['T'] = (string) $xmlData->temperatures->temperature[$x];
			//IPS_LogMessage("BHKW stateHeatControl time", $x . " : " . $ScriptData['T']);
			switch ($x) 
			{
				case "0":
					SetValueFloat($this->GetIDForIdent("T1") , $ScriptData['T']);
					//IPS_LogMessage("BHKW stateHeatControl T1", $x . " : " . $ScriptData['T']);
				break;
				case "1":
					SetValueFloat($this->GetIDForIdent("T4") , $ScriptData['T']);
					//IPS_LogMessage("BHKW stateHeatControl T4", $x . " : " . $ScriptData['T']);
				break;
				case "2":
					SetValueFloat($this->GetIDForIdent("T3") , $ScriptData['T']);
					//IPS_LogMessage("BHKW stateHeatControl T3", $x . " : " . $ScriptData['T']);
				break;
				case "3":
					SetValueFloat($this->GetIDForIdent("T2") , $ScriptData['T']);
					//IPS_LogMessage("BHKW stateHeatControl T2", $x . " : " . $ScriptData['T']);
				break;
			default:
				//;
			} 
		}
		
		$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
		$ID = IPS_GetInstanceListByModuleID($GUID)[0];
		//IPS_LogMessage("BHKW stateHeatControl ID", $ID);
		
		$KategorieID = @IPS_GetCategoryIDByName("Heizkreislauf 1", $ID);
		$KategorieID1 = @IPS_GetCategoryIDByName("Nachtabsenkung", $KategorieID);
		IPS_LogMessage("BHKW stateHeatControl Kategorie1", $KategorieID1);
		$ScriptData['HKStatus'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]['state'];
		//$NASID = IPS_GetVariableIDByName("Status", $KategorieID1);
		//IPS_LogMessage("BHKW stateHeatControl nasid", $NASID);
		SetValueFloat(IPS_GetVariableIDByName("Status",$KategorieID1) , $ScriptData['HKStatus']);
		IPS_LogMessage("BHKW stateHeatControl HKStatus", $ScriptData['HKStatus']);
		//IPS_LogMessage("BHKW stateHeatControl HKStatus", $NASID);


		$ScriptData['HKSZ'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]->time[0];
		//SetValueFloat($this->GetIDForIdent("StartZeit") , $ScriptData['HKSZ']);
		IPS_LogMessage("BHKW stateHeatControl HKSZ", $ScriptData['HKSZ']);

		$ScriptData['HKEZ'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]->time[1];
		//SetValueFloat($this->GetIDForIdent("EndeZeit") , $ScriptData['HKEZ']);
		IPS_LogMessage("BHKW stateHeatControl HKEZ", $ScriptData['HKEZ']);

	}
}
