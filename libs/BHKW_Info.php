<?php

declare(strict_types=1);

trait BHKWInfo
{
	private function WriteLog11($data)
	{
		IPS_LogMessage("BHKW Info TestLog", $data);
	}
	
	private function Info($data)
	{
		try
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			//echo "Alles ok!";
		}
 			catch(Exception $ex)
		{
			//print_r($ex);
			IPS_LogMessage("BHKW Info Fehler", $data);
		}
		
		$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
		$ID = IPS_GetInstanceListByModuleID($GUID)[0];
		$KategorieID = @IPS_GetCategoryIDByName("Hardware", $ID);
		
		$ScriptData['IP'] = (string) $xmlData->ip;
		SetValueString(IPS_GetVariableIDByName("IP ADDR", $KategorieID), $ScriptData['IP']);

		$ScriptData['firmware'] = (string) $xmlData->firmware;
		SetValueString(IPS_GetVariableIDByName("Geräte Firmware", $KategorieID), $ScriptData['firmware']);
		

		$BedinID = @IPS_GetCategoryIDByName("Bedineinheit", $KategorieID);
		
		$ScriptData['Cserial'] = (string) $xmlData->control->serial;		
		SetValueInteger(IPS_GetVariableIDByName("Seriennummer Bedieneinheit", $BedinID), $ScriptData['Cserial']);

		$ScriptData['CHardware'] = (string) $xmlData->control->hardware;		
		SetValueString(IPS_GetVariableIDByName("Hardware Version Bedieneinheit", $BedinID), $ScriptData['CHardware']);

		$ScriptData['Csoftware'] = (string) $xmlData->control->software;		
		SetValueString(IPS_GetVariableIDByName("Software Version Bedieneinheit", $BedinID), $ScriptData['Csoftware']);

		$SteuerID = @IPS_GetCategoryIDByName("Steuereinheit", $KategorieID);
		
		$ScriptData['Sserial'] = (string) $xmlData->userInterface->serial;		
		SetValueInteger(IPS_GetVariableIDByName("Seriennummer Steuereinheit", $SteuerID), $ScriptData['Sserial']);

		$ScriptData['SHardware'] = (string) $xmlData->userInterface->hardware;		
		SetValueString(IPS_GetVariableIDByName("Hardware Version Steuereinheit", $SteuerID), $ScriptData['SHardware']);

		$ScriptData['Sdaemon'] = (string) $xmlData->userInterface->daemon;		
		SetValueString(IPS_GetVariableIDByName("Daemon Steuereinheit", $SteuerID), $ScriptData['Sdaemon']);

		$ScriptData['Sgui'] = (string) $xmlData->userInterface->gui;		
		SetValueString(IPS_GetVariableIDByName("Software Steuereinheit", $SteuerID), $ScriptData['Sgui']);
	}
}
