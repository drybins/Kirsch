<?php

declare(strict_types=1);

trait CommonFunctions
{
	public function VS()
	{

		//$time = date("H:i");			
		//$VorlaufSoll = GetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"));
		//$AussenTemp = GetValueFloat($this->GetIDForIdent("T1"));
		//$VorlaufTempDiff = 70 - 45;
		//$VorlaufTempStep = $VorlaufTempDiff/40;
		//$VorlaufSoll = ((20-$AussenTemp)* $VorlaufTempStep) + 45;
		//Nachtabsenkung bei mehr als 3 Grad AußenTemperatur und zwischen 22:30 und 05:00 Uhr.
		//if(($time >= "22:30")or($time <= "05:00"))
		//{
		//	$VorlaufSoll = $VorlaufSoll - 5;
		//}
		//If($AußenTemperatur > 3)
		//{
    		//}
		IPS_LogMessage("VS:"," running");
		//IPS_LogMessage("AußentemperaturT",$VorlaufSoll);
		//IPS_LogMessage("Außentemperatur", $this->GetIDForIdent("T1"));
		//SetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"), $VorlaufSoll);
	}
}