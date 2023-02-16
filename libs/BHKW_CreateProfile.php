<?php

declare(strict_types=1);

trait BHKWCreateProfile
{
	//private function WriteLog1($data)
	//{
	//	IPS_LogMessage("BHKW BHKWCreateProfile TestLog", $data);
	//}
	
	private function BHKWCreateProfile()
	{
		// Kirsch BHKW Profile anlegen
		$this->IPS_CreateVariableProfile("Kirsch.UpM", 1, " UpM", 0, 0, 1, 0, "");
		$this->IPS_CreateVariableProfile("Kirsch.Kw", 1, " Kw", 0, 0,1, 2, "");
		$this->IPS_CreateVariableProfile("Kirsch.Watt", 1, " Watt", 0, 0,1, 0, "");
		$this->IPS_CreateVariableProfile("Kirsch.Volt", 1, " Volt", 0, 0,1, 0, "");
		$this->IPS_CreateVariableProfile("Kirsch.GradC", 1, " °Celsius", 0, 0,1, 0, "");
		$this->IPS_CreateVariableProfile("Kirsch.Ampere", 2, " Ampere", 0, 0,1,2, ""); 
		$this->IPS_CreateVariableProfile("Kirsch.Frequenz", 2, " Hz", 0, 0,1,2, ""); 
		$this->IPS_CreateVariableProfile("Kirsch.Prozent", 1, " %", 0, 100,1, 0, "");
		$this->IPS_CreateVariableProfile("Kirsch.kWh", 2, " kWh", 0, 0,1, 0, "");
		$this->IPS_CreateVariableProfile("Kirsch.Std", 2, " Stunden", 0, 0,1, 2, "");
			
		$this->IPS_CreateVariableProfile("Kirsch.Status", 1, "", 1, 11, 1, 2, "");
		IPS_SetVariableProfileAssociation("Kirsch.Status", 1, "gestoppet", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 2, "startet", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 3, "aufwärmen", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 4, "läuft", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 5, "abkühlen", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 6, "selbsttest", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 7, "Oel nachfüllen", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 10, "Notstop", "", 0xff0000);
		IPS_SetVariableProfileAssociation("Kirsch.Status", 11, "Fehler", "", 0xff0000);
		
		$this->IPS_CreateVariableProfile("Kirsch.Heizung", 1, "", 1, 11, 1, 2, "");
		IPS_SetVariableProfileAssociation("Kirsch.Heizung", 1, "Automatik", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Heizung", 2, "Tag", "", 0x7cfc00);
		IPS_SetVariableProfileAssociation("Kirsch.Heizung", 3, "Nacht", "", 0x7cfc00);
			
		$this->IPS_CreateVariableProfile("Kirsch.Gasventil", 0, "", 1, 11, 1, 2, "");
		IPS_SetVariableProfileAssociation("Kirsch.Gasventil", true, "Geöffnet", "", -1);
		IPS_SetVariableProfileAssociation("Kirsch.Gasventil", false, "Geschlossen", "", -1);
			
		$this->IPS_CreateVariableProfile("Kirsch.OelPumpe", 1, "", 1, 11, 1, 2, "");
		IPS_SetVariableProfileAssociation("Kirsch.OelPumpe", 1, "gestoppet", "", -1);
		IPS_SetVariableProfileAssociation("Kirsch.OelPumpe", 2, "vorwärtz", "", -1);
		IPS_SetVariableProfileAssociation("Kirsch.OelPumpe", 3, "rückwärtz", "", -1);
			
		$this->IPS_CreateVariableProfile("Kirsch.AnAus", 0, "", 1, 11, 1, 2, "");
		IPS_SetVariableProfileAssociation("Kirsch.AnAus", true, "An", "", -1);
		IPS_SetVariableProfileAssociation("Kirsch.AnAus", false, "Aus", "", -1);
			
		$this->IPS_CreateVariableProfile("Kirsch.PGMStatus", 1, "", 1, 11, 1, 2, "");
		IPS_SetVariableProfileAssociation("Kirsch.PGMStatus", 1, "Aktiv", "", -1);
		IPS_SetVariableProfileAssociation("Kirsch.PGMStatus", 2, "Inaktiv", "", -1);
	}
			
	private function IPS_CreateVariableProfile($ProfileName, $ProfileType, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Icon) 
	{
	    if (!IPS_VariableProfileExists($ProfileName)) 
	    {
		       IPS_CreateVariableProfile($ProfileName, $ProfileType);
		       IPS_SetVariableProfileText($ProfileName, "", $Suffix);
		       IPS_SetVariableProfileValues($ProfileName, $MinValue, $MaxValue, $StepSize);
		       IPS_SetVariableProfileDigits($ProfileName, $Digits);
		       IPS_SetVariableProfileIcon($ProfileName, $Icon);
	    }
	}
}
