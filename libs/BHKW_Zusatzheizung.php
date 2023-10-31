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
		//$Heißwasser = GetValue($this->GetIDForIdent("WWTTarget"));
		$WarmwasserStart = GetValue($this->GetIDForIdent("WWStartZeit"));
		$WarmwasserEnde = GetValue($this->GetIDForIdent("WWEndeZeit"));
		
		$VorlaufMitteAus = $VorlaufSoll + 8;
		$VorlaufSollAn = $VorlaufSoll-8;
		
		$ZHID = $this->ReadPropertyInteger("CounterID");
		IPS_LogMessage("zHeizung ","Heizung Schalter ID: " . $ZHID);

		$HKPumpe = GetValue($this->GetIDForIdent("R1"));
		IPS_LogMessage("zHeizung","HKPumpe: " . $HKPumpe);	
		if($HKPumpe)
		{
			IPS_LogMessage("zHeizung", "Heizkreispumpe ist an:");
			// Heizung ist an
			//if($VorlaufIst < $VorlaufSollAn and $SPmitte < $VorlaufSoll)
			//{
				//$RC = HM_WriteValueBoolean($ZHID, "STATE" , True);
				//SetValue($this->GetIDForIdent("R4"), true);
				//IPS_LogMessage("zHeizung Heizung an:");
			//}
			//if($SPmitte > $VorlaufMitteAus)
			//{
				//$RC = HM_WriteValueBoolean($ZHID, "STATE" , false);
				//SetValue($this->GetIDForIdent("R4"), false);
				//IPS_LogMessage("zHeizung Heizung aus:");
				//IPS_LogMessage("zHeizung Heizung mitte:",GetValue($this->GetIDForIdent("T3")));
				//IPS_LogMessage("zHeizung Heizung vor+8:",$VorlaufMitteAus);
			//}
		}
		else
		{
			{
				// Heizung is aus (Warmwasser)
				// Speichertemperatur oben > 65 zusatzHeizung aus
				if (GetValue($this->GetIDForIdent("T2")) > 65)
				{
					SetValue($this->GetIDForIdent("R4"), false);
					IPS_LogMessage("zHeizung WWaus:",GetValue($this->GetIDForIdent("T2")));	
				}
				// Speichertemperatur oben < 55 zusatzHeizung an
				if (GetValue($this->GetIDForIdent("T2")) < 55)
				{
					SetValue($this->GetIDForIdent("R4"), true);
					IPS_LogMessage("zHeizung WWan:",GetValue($this->GetIDForIdent("T2")));
				}
			}
		}
	}
}
