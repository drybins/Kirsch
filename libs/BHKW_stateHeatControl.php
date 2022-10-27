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
		//IPS_LogMessage("BHKW stateHeatControl Kategorie1", $KategorieID1);
		$ScriptData['HKStatus'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]['state'];
		$NASID = IPS_GetVariableIDByName("Status", $KategorieID1);
		//IPS_LogMessage("BHKW stateHeatControl nasid", $NASID);
		
		switch($ScriptData['HKStatus'])
		{
			case "enabled":
				SetValueInteger($NASID , 1);
			break;
			case "disabled":
				SetValueInteger($NASID , 2);
			break;
			default:
		}
		
		//IPS_LogMessage("BHKW stateHeatControl HKStatus", $ScriptData['HKStatus']);
		//IPS_LogMessage("BHKW stateHeatControl HKStatus", $NASID);


		$ScriptData['HKSZ'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]->time[0];
		SetValueInteger(IPS_GetVariableIDByName("Start Zeit", $KategorieID1), strtotime($ScriptData['HKSZ']));
		//IPS_LogMessage("BHKW stateHeatControl HKSZ", $ScriptData['HKSZ']);

		$ScriptData['HKEZ'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]->time[1];
		SetValueInteger(IPS_GetVariableIDByName("Ende Zeit", $KategorieID1), strtotime($ScriptData['HKEZ']));
		//IPS_LogMessage("BHKW stateHeatControl HKEZ", $ScriptData['HKEZ']);

		$ScriptData['HKTFall'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]->temperature[0];
		SetValueInteger(IPS_GetVariableIDByName("Absenken um °Celsius", $KategorieID1), $ScriptData['HKTFall']);

		$ScriptData['HKTmin'] = (string) $xmlData->heatCircuits->heatCircuit->program[0]->temperature[1];
		SetValueInteger(IPS_GetVariableIDByName("nicht absenken bei unter °Celsius", $KategorieID1), $ScriptData['HKTmin']);

		$KategorieID1 = @IPS_GetCategoryIDByName("Heißwasser", $KategorieID);

		$WWSID = IPS_GetVariableIDByName("Status", $KategorieID1);
		$ScriptData['HKWWS'] = (string) $xmlData->programs->program[2]['state'];
		IPS_LogMessage("BHKW stateHeatControl HKWWS", $ScriptData['HKWWS']);
		
		switch($ScriptData['HKWWS'])
		{
			case "enabled":
				SetValueInteger($WWSID , 1);
			break;
			case "disabled":
				SetValueInteger($WWSID , 2);
			break;
			default:
		}
		//SetValueInteger(IPS_GetVariableIDByName("Status", $KategorieID1), $ScriptData['HKWWS']);

		$ScriptData['HKWWST'] = (string) $xmlData->programs->program[2]->time[0];
		SetValueInteger(IPS_GetVariableIDByName("Start Zeit", $KategorieID1), strtotime($ScriptData['HKWWST']));

		$ScriptData['HKWWET'] = (string) $xmlData->programs->program[2]->time[1];
		SetValueInteger(IPS_GetVariableIDByName("Ende Zeit", $KategorieID1), strtotime($ScriptData['HKWWET']));

		$ScriptData['HKWWT'] = (string) $xmlData->programs->program[2]->temperature;
		SetValueInteger(IPS_GetVariableIDByName("warm Wasser Temperatur", $KategorieID1), $ScriptData['HKWWT']);

		$KategorieID1 = @IPS_GetCategoryIDByName("Sommer", $KategorieID);

		$SSID = IPS_GetVariableIDByName("Status", $KategorieID1);
		//IPS_LogMessage("BHKW stateHeatControl SSID", $SSID);
		$ScriptData['HKSS'] = (string) $xmlData->programs->program[1]['state'];
		//IPS_LogMessage("BHKW stateHeatControl HKSS", $ScriptData['HKSS']);
		switch($ScriptData['HKSS'])
		{
			case "enabled":
				SetValueInteger($SSID , 1);
			break;
			case "disabled":
				SetValueInteger($SSID , 2);
			break;
			default:
		}

		$ScriptData['HKST'] = (string) $xmlData->programs->program[1]->temperature;
		SetValueInteger(IPS_GetVariableIDByName("Abschalten bei Außentemperatur über", $KategorieID1), $ScriptData['HKST']);

		$KategorieID1 = @IPS_GetCategoryIDByName("Urlaubsmodus", $KategorieID);
		
		$UID = IPS_GetVariableIDByName("Status", $KategorieID1);
		IPS_LogMessage("BHKW stateHeatControl SSID", $UID);
		$ScriptData['HKU'] = (string) $xmlData->programs->program[0]['state'];
		IPS_LogMessage("BHKW stateHeatControl HKU", $ScriptData['HKU']);
		switch($ScriptData['HKU'])
		{
			case "enabled":
				SetValueInteger($UID , 1);
			break;
			case "disabled":
				SetValueInteger($UID , 2);
			break;
			default:

		$ScriptData['HKAST'] = (string) $xmlData->programs->program[1]->date[0];
		SetValueInteger(IPS_GetVariableIDByName("Start Zeit", $KategorieID1), strtotime($ScriptData['HKAST']));
		IPS_LogMessage("BHKW stateHeatControl HKAST", $ScriptData['HKAST']);
		}
	}
}
