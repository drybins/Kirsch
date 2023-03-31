<?php

declare(strict_types=1);

trait BHKWFunctions
{
	public function VorlaufSoll()
	{

		$time = date("H:i");			
		//$VorlaufSoll = GetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"));
		$AussenTemp = GetValueFloat($this->GetIDForIdent("T1"));
		
				$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
		$ID = IPS_GetInstanceListByModuleID($GUID)[0];
		IPS_LogMessage("Dierk1 BHKW stateHeatControl ID", $ID);
		$KategorieID = @IPS_GetCategoryIDByName("Heizkreislauf 1", $ID);
		
		$Volauf20 = GetValueInteger(IPS_GetVariableIDByName("Vorlauf bei 20°C", $KategorieID));
		IPS_LogMessage("Dierk1 BHKW stateHeatControl Kategorie", $Volauf20);
		$VorlaufTempDiff = 70 - 40;
		$VorlaufTempStep = $VorlaufTempDiff/40;
		$VorlaufSoll = ((20-$AussenTemp)* $VorlaufTempStep) + 40;
		//Nachtabsenkung bei mehr als 3 Grad AußenTemperatur und zwischen 22:30 und 05:30 Uhr.
		if(($time >= "22:30")or($time <= "05:30"))
		{
			$VorlaufSoll = $VorlaufSoll - 5;
		}
		//If($AußenTemperatur > 3)
		//{
    		//}
		//IPS_LogMessage("$VorlaufTempStep",$VorlaufTempStep);
		//IPS_LogMessage("AußentemperaturT",$VorlaufSoll);
		//IPS_LogMessage("Außentemperatur", $this->GetIDForIdent("T1"));
		SetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"), $VorlaufSoll);
	}
}
