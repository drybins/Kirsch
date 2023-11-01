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

		$Heißwasser = GetValue(13846);
		$WarmwasserStart = GetValue(20086);
		$WarmwasserEnde = GetValue(52528);
		
		$VorlaufMitteAus = $VorlaufSoll + 8;
		$VorlaufSollAn = $VorlaufSoll-8;
		
		$ZHID = $this->ReadPropertyInteger("CounterID");
		IPS_LogMessage("zHeizung ","Heizung Schalter ID: " . $ZHID);

		$HKPumpe = GetValue($this->GetIDForIdent("R1"));
		IPS_LogMessage("zHeizung","HKPumpe: " . $HKPumpe);	
		if($HKPumpe)
		{
			IPS_LogMessage("zHeizung", "Heizkreispumpe ist an:");
			if($VorlaufIst < $VorlaufSollAn Or $SPMitte  < $VorlaufMitteAus)
			{
				$ZH = True;
				//SetValue($this->GetIDForIdent("R4"), true);
				IPS_LogMessage("zHeizung","Heizung an:");
			}
			//if($SPmitte > $VorlaufMitteAus)
			else
			{
				$ZH = false;
				IPS_LogMessage("zHeizung","Heizung aus:");
				//$RC = HM_WriteValueBoolean($ZHID, "STATE" , false);
				//SetValue($this->GetIDForIdent("R4"), false);
				//IPS_LogMessage("zHeizung Heizung aus:");
				//IPS_LogMessage("zHeizung Heizung mitte:",GetValue($this->GetIDForIdent("T3")));
				//IPS_LogMessage("zHeizung Heizung vor+8:",$VorlaufMitteAus);
			}
		}
		if (time() > $WarmwasserStart and time() < $WarmwasserEnde)
		{
			// Heizung is aus (Warmwasser)
			// Speichertemperatur oben > 65 zusatzHeizung aus
			if ($SPOben > ($Heißwasser + 2))
			{
				IPS_LogMessage("zHeizung", "WWaus:" . $SPOben);	
			}
			// Speichertemperatur oben < 55 zusatzHeizung an
			if ($SPOben < ($Heißwasser - 10))
			{
				$ZH = True;
				//SetValue($this->GetIDForIdent("R4"), true);
				IPS_LogMessage("zHeizung", "WWan:" . $SPOben);
			}
		}
		
		if($ZH)
		{

			$RC = HM_WriteValueBoolean(48122, "STATE" , True);
			echo "Heizung an";
		}
		else
		{

			$RC = HM_WriteValueBoolean(48122, "STATE" , false);
			echo "Heizung au";
		}
	}
}
