<?php
/*****************************************************************************/
/* erweitert das Kirsch BHKW Modul um dei möglichkeit eine Zusatheizung      */
/* zu schalten.                                                              */
/* Autohr:   D.Rybinski, KAT-CS                                              */
/* Version:  1.0                                                             */
/* Erstellt: 16.05.2021                                                      */
/*                                                                           */
/*                                                                           */
/*                                                                           */
/*****************************************************************************/

declare(strict_types=1);

trait BHKWZusatzHeizung
{	
	private function ZusatzHeizung()
	{
		$VorlaufSoll = GetValue($this->GetIDForIdent("VorlaufTemperaturSoll"));
		$VorlaufIst = GetValue($this->GetIDForIdent("T5"));
		$SPOben = GetValue($this->GetIDForIdent("T2"));
		$SPMitte = GetValue($this->GetIDForIdent("T3"));

		$KategorieID = @IPS_GetCategoryIDByName("19", 0);
		$KategorieNacht1ID = @IPS_GetCategoryIDByName("Keller", $KategorieID);
		$KategorieNacht2ID = @IPS_GetCategoryIDByName("Heizungsraum", $KategorieNacht1ID);
		$KategorieNacht3ID = @IPS_GetCategoryIDByName("Krupp Kessel", $KategorieNacht2ID);
		$GeraeteID = IPS_GetObjectIDByName ("DS 18B20 Temperature Sensor", $KategorieNacht3ID);
		$SchellyID = IPS_GetObjectIDByName ("shellyplus2pm-a0dd6c28b4f4", $KategorieNacht3ID);
		
		$HO = GetValue(19296);   // Holz/Oel
		if($HO)			// Ist Holz
		{	
			$this->Holz($SchellyID);
		}
		
		//$Heißwasser = GetValue(13846);
		$KategorieBHKWID = @IPS_GetCategoryIDByName("BHKW", 0);
		$GeraeteBHKWID = IPS_GetObjectIDByName ("Kirsch BHKW Nano 4.12", $KategorieBHKWID);
		$KategorieHK1ID = @IPS_GetCategoryIDByName("Heizkreislauf 1", $GeraeteBHKWID);
		$KategorieHWID = @IPS_GetCategoryIDByName("Heißwasser", $KategorieHK1ID);
		IPS_LogMessage("zHeizungH","KategorieHWID ID: " . $KategorieHWID);
		
		$GeraeteHWID = IPS_GetObjectIDByIdent("WWTTarget", $KategorieHWID);
		$Heißwasser = GetValue($GeraeteHWID);
		$GeraeteWWSID = IPS_GetObjectIDByIdent("WWStartZeit", $KategorieHWID);
		$GeraeteWWEID = IPS_GetObjectIDByIdent("WWEndeZeit", $KategorieHWID);
		//IPS_LogMessage("zHeizungH","GeraeteWWEID ID: " . $GeraeteWWEID);	
		$WarmwasserStart = GetValue($GeraeteWWSID);
		$WarmwasserStart = $WarmwasserStart  + 3600; 
		$WarmwasserEnde = GetValue($GeraeteWWEID);
		
		//$BHKWStatus = GetValue(21751);
		//IPS_LogMessage("zHeizung", "BHKW nicht auf Fehler! " . $BHKWStatus);
		$VorlaufMitteAus = $VorlaufSoll + 10;
		$VorlaufSollAn = $VorlaufSoll-6;
		
		$HKPumpe = GetValue($this->GetIDForIdent("R1"));
		//IPS_LogMessage("zHeizung","HKPumpe: " . $HKPumpe);	
		$ZHH = false;
			
		$VarInfo = IPS_GetVariable(11816);
        //print_r ($VarInfo);
		$SchaltZeit = $VarInfo["VariableChanged"];
		//IPS_LogMessage("zHeizung", "Schaltzeit = " . $SchaltZeit);
        $Jetzt  =  time();
        //IPS_LogMessage("zHeizung", "Jetzt = " . $Jetzt);
        $Dauer = ($Jetzt - $SchaltZeit)/60;
		//IPS_LogMessage("zHeizung", "Dauer: " . $Dauer);
		
		
	
		if($Dauer > 30)
        {
			IPS_LogMessage("zHeizung", "Schalten");

			if($HKPumpe)
			{
				$ZHH = GetValueBoolean (11816);
				IPS_LogMessage("zHeizung", "Heizkreispumpe ist an:");
				if($VorlaufIst < $VorlaufSollAn)
				{
					IPS_LogMessage("zHeizung", "VI:" . $VorlaufIst . " : " . "VSAN" . " : " . $VorlaufSollAn);	
					IPS_LogMessage("zHeizung", "ZH VorlaufIst ist kleiner Vorlauf soll An.");
				}

				//if($VorlaufIst < $VorlaufSollAn Or $SPMitte < $VorlaufMitteAus)
				if($VorlaufIst < $VorlaufSollAn)
				{
					$ZHH = True;
					//SetValue($this->GetIDForIdent("R4"), true);
					IPS_LogMessage("zHeizung","Heizung an:");
				}
				//if($SPmitte > $VorlaufMitteAus)
				if($SPMitte > $VorlaufMitteAus)
				{
					IPS_LogMessage("zHeizung", "SPMitte:" . $SPMitte . " : " . "VMAUS" . " : " . $VorlaufMitteAus);	
					IPS_LogMessage("zHeizung", "ZH Speicher mitte ist größer Vorlauf mitte Aus.");

					$ZHH = false;
					IPS_LogMessage("zHeizung","Heizung aus:");
				}
			}
			$ZHW= false;
			if (time() > $WarmwasserStart and time() < $WarmwasserEnde)
			{
			
				IPS_LogMessage("zHeizung", "WW:" . $SPOben . " : Heißwasser: " . $Heißwasser . " : Heißwasser aus:" . ($Heißwasser +5) . " : Heißwasser an:" . ($Heißwasser -10 ));	
				//if ($SPOben > ($Heißwasser + 5) and !$ZHH)
				if ($SPOben > ($Heißwasser + 5))
				{
					$ZHW = false;
					IPS_LogMessage("zHeizung", "WWaus:" . $SPOben);	
				}
				else
				// Speichertemperatur oben < 55 zusatzHeizung an
				//if ($SPOben < ($Heißwasser - 10) or $SPOben < ($Heißwasser + 5) )
				if ($SPOben < ($Heißwasser - 10)) 
				{
					$ZHW = True;
					//SetValue($this->GetIDForIdent("R4"), true);
					IPS_LogMessage("zHeizung", "WWan:" . $SPOben);
				}
			}
			if(!$ZHH)
			{
				IPS_LogMessage("zHeizung","ZHH ist false");
			}
			else
			{
				IPS_LogMessage("zHeizung","ZHH ist True");
			}
			if(!$ZHW)
			{
				IPS_LogMessage("zHeizung","ZHW ist false");
			}
			else
			{
				IPS_LogMessage("zHeizung","ZHW ist True");
			}
			$Holz = GetValueBoolean (19296);
			//$ZHS = HM_RequestStatus($ZHID, "STATE");
			if(!$Holz)
			{
				IPS_LogMessage("zHeizung","Oel Brenner");
				$ZHS = GetValueBoolean (59746);    // Krupp Heizungs Pumpe An/Aus
				if($ZHW or $ZHH)
				{
					//$RC = @HM_WriteValueBoolean($ZHID, "STATE" , True);
					IPS_LogMessage("zHeizung", "Heizung an1 : " . $ZHS . " ZHW: " . $ZHW . " ZHH: " . $ZHH);
					if(!$ZHS)
					{
						//SetValueBoolean(20054, true);
						IPS_LogMessage("zHeizung", "Heizung eingeschaltet!");
						//$RC = @HM_WriteValueBoolean($ZHID, "STATE" , True);
						SetValue(59746, True);
						RequestAction(59746, True);
						SetValue(11816, True);
						RequestAction(11816, True);
					}
					else
					{
						IPS_LogMessage("zHeizung", "Heizung war an!");
					}
				}
				else
				{
					if($ZHS)
					{
						//SetValueBoolean(20054, false);
						//$RC = HM_WriteValueBoolean($ZHID, "STATE" , False);
						SetValue(59746, False);
						RequestAction(59746, False);
						SetValue(11816, False);
						RequestAction(11816, False);
						IPS_LogMessage("zHeizung", "Heizung ausgeschaltet!");
					}
					else
					{
						IPS_LogMessage("zHeizung", "Heizung war aus!");
					}
				}
			}
			else
			{
				IPS_LogMessage("zHeizung", "Holzist an: " . $Holz );
			}
		}
	}
	
	private function WarmWasser()
	{
	}
	
	private function Holz(int $SchellyID)
	{
		IPS_LogMessage("zHeizungH","Schelly ID: " . $SchellyID);
		$IdentKruppStatus = IPS_GetObjectIDByIdent("KruppStatus",$KategorieNacht3ID);
		$IdentVorlaufKrupp = IPS_GetObjectIDByIdent("Temperatur",$GeraeteID);
		IPS_LogMessage("zHeizungH","IdentKruppStatus: " . $IdentKruppStatus);
		$AID = IPS_GetObjectIDByName ("Archive", 0);
		$newDate = date('Y-m-d H:i:s', strtotime(' -5 minutes'));
		$last_value = AC_GetLoggedValues($AID, $IdentVorlaufKrupp,  0, strtotime($newDate), 1);
		$Vorlauf_Krupp = GetValue($IdentVorlaufKrupp);
		$strtest = $last_value[0]["Value"];
		IPS_LogMessage("zHeizungH","VorlaufKrupp: " . $strtest);
		$difTemp =  $Vorlauf_Krupp - $strtest;
		IPS_LogMessage("zHeizungH","DifTemp: " . $difTemp);
		SetValueFloat (59571, $difTemp);
	
		$Status_Krupp = GetValue($IdentKruppStatus);
		IPS_LogMessage("zHeizung","Status_Krupp: " . $Status_Krupp);
		if($Status_Krupp === 0 and $difTemp >0)
		{
			SetValueInteger ($IdentKruppStatus, 1);
		}	
		if(($Status_Krupp === 1 or $Status_Krupp === 3)  and $Vorlauf_Krupp >80)
		{
			SetValueInteger ($IdentKruppStatus, 2);
			//Pumpe an
		}
		if($Status_Krupp === 2 and $Vorlauf_Krupp <60)
		{
			SetValueInteger ($IdentKruppStatus, 3);
			//Pumpe aus
		}
		$HolzNachlegen = GetValue(34665);
		if(!$HolzNachlegen)
		{
			SetValueInteger ($IdentKruppStatus, 4);
			if($difTemp < 0 and $Vorlauf_Krupp < 100)
			{
				IPS_LogMessage("zHeizungH","Pumpe abschalten.");
			}
		}
	}
}
