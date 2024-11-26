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

		//$Heißwasser = GetValue(13846);
		$Heißwasser = 55;
		$WarmwasserStart = GetValue(20086);
		$WarmwasserStart = $WarmwasserStart  + 3600; 
		$WarmwasserEnde = GetValue(52528);
		
		//$BHKWStatus = GetValue(21751);
		//IPS_LogMessage("zHeizung", "BHKW nicht auf Fehler! " . $BHKWStatus);
		
		$VorlaufMitteAus = $VorlaufSoll + 10;
		$VorlaufSollAn = $VorlaufSoll-6;
		
		//$ZHID = IPS_GetParent($this->ReadPropertyInteger("ZHID"));
		$ZHID = 48122;
		//SetValueBoolean(20054, GetValue(30813));
		//$RC = HM_WriteValueBoolean($ZHID, "STATE" , GetValue(30813));
		//IPS_LogMessage("zHeizung ","Heizung Schalter ID: " . $ZHID);

		$HKPumpe = GetValue($this->GetIDForIdent("R1"));
		IPS_LogMessage("zHeizung","HKPumpe: " . $HKPumpe);	
		$ZHH = false;
			
		$VarInfo = IPS_GetVariable(11816);
        //print_r ($VarInfo);
		$SchaltZeit = $VarInfo["VariableChanged"];
		//IPS_LogMessage("zHeizung", "Schaltzeit = " . $SchaltZeit);
        $Jetzt  =  time();
        //IPS_LogMessage("zHeizung", "Jetzt = " . $Jetzt);
        $Dauer = ($Jetzt - $SchaltZeit)/60;
		IPS_LogMessage("zHeizung", "Dauer: " . $Dauer);
		
		
	
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
				$ZHS = GetValueBoolean (59746);
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
}
