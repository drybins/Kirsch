<?php

declare(strict_types=1);

trait BHKWFunctions
{
	public function VorlaufSoll()
	{

		//$time = date("H:i");			
		//$VorlaufSoll = GetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"));
		$AussenTemp = GetValueFloat($this->GetIDForIdent("T1"));
		
		$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
		$ID = IPS_GetInstanceListByModuleID($GUID)[0];
		//IPS_LogMessage("Dierk1 BHKW stateHeatControl ID", $ID);
		$KategorieID = @IPS_GetCategoryIDByName("Heizkreislauf 1", $ID);
		$KategorieNachtAID = @IPS_GetCategoryIDByName("Nachtabsenkung", $KategorieID);
		IPS_LogMessage("zVorlauf","Nachtabsenkung: " . $KategorieNachtAID);
		//$TMinID = IPS_GetVariableIDByName("TMin",$KategorieNachtAID);
		//IPS_LogMessage("zVorlauf","Nachtabsenkung: " . IPS_GetVariableIDByName("TMin",$KategorieNachtAID));
		IPS_LogMessage("zVorlauf","Vorlauf20 ID: " . IPS_GetVariableIDByName("Vorlauf bei 20°C", $KategorieID));
		
		$Vorlauf20 = GetValueInteger(IPS_GetVariableIDByName("Vorlauf bei 20°C", $KategorieID));
		$VorlaufM20 = GetValueInteger(IPS_GetVariableIDByName("Vorlauf bei -20°C", $KategorieID));
		$TMin = GetValue(57986);
		IPS_LogMessage("zVorlauf","TMin: " . $TMin);
		IPS_LogMessage("zVorlauf","Vorlauf 20: " . $Vorlauf20);
		IPS_LogMessage("zVorlauf","Vorlauf -20: " . $VorlaufM20);
		//IPS_LogMessage("Dierk1 BHKW stateHeatControl Kategorie", $Volauf20);
		$VorlaufTempDiff = $VorlaufM20 - $Vorlauf20;
		IPS_LogMessage("zVorlauf","VorlaufDiff: " . $VorlaufTempDiff);
		$VorlaufTempStep = $VorlaufTempDiff/40;
		IPS_LogMessage("zVorlauf","VorlaufTempStep: " . $VorlaufTempStep);
		$VorlaufSoll = ((20-$AussenTemp)* $VorlaufTempStep) + $Vorlauf20;
		IPS_LogMessage("zVorlauf","VorlaufSoll" . $VorlaufSoll);
		$NachtabsenkungStart = GetValue(46769);
		$NachtabsenkungEnde = GetValue(48107); 
		//Nachtabsenkung bei mehr als 3 Grad AußenTemperatur und zwischen 22:30 und 05:30 Uhr.
		if((time() >= $NachtabsenkungStart)or(time() <= $NachtabsenkungEnde))
		{
			if($AussenTemp > 4)
			{
				$VorlaufSoll = $VorlaufSoll - 5;
			}
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
