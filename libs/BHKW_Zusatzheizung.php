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
		
		$VorlaufMitteAus = $VorlaufSoll + 10;
		$VorlaufSollAn = $VorlaufSoll-15;
		
		//$ZHID = IPS_GetParent($this->ReadPropertyInteger("ZHID"));
		$ZHID = 48122;
		//IPS_LogMessage("zHeizung ","Heizung Schalter ID: " . $ZHID);

		$HKPumpe = GetValue($this->GetIDForIdent("R1"));
		IPS_LogMessage("zHeizung","HKPumpe: " . $HKPumpe);	
		$ZHH = false;
		if($HKPumpe)
		{
			IPS_LogMessage("zHeizung", "Heizkreispumpe ist an:");
			IPS_LogMessage("zHeizung", "VI:" . $VorlaufIst . " : " . "VSAN" . " : " . $VorlaufSollAn);	
			IPS_LogMessage("zHeizung", "SPMitte:" . $SPMitte . " : " . "VMAUS" . " : " . $VorlaufMitteAus);	
			if($VorlaufIst < $VorlaufSollAn Or $SPMitte < $VorlaufMitteAus)
			{
				$ZHH = True;
				//SetValue($this->GetIDForIdent("R4"), true);
				IPS_LogMessage("zHeizung","Heizung an:");
			}
			//if($SPmitte > $VorlaufMitteAus)
			if($SPMitte > $VorlaufMitteAus)
			{
				$ZHH = false;
				IPS_LogMessage("zHeizung","Heizung aus:");
				//$RC = HM_WriteValueBoolean($ZHID, "STATE" , false);
				//SetValue($this->GetIDForIdent("R4"), false);
				//IPS_LogMessage("zHeizung Heizung aus:");
				//IPS_LogMessage("zHeizung Heizung mitte:",GetValue($this->GetIDForIdent("T3")));
				//IPS_LogMessage("zHeizung Heizung vor+8:",$VorlaufMitteAus);
			}
		}
		$ZHW= $ZHH;
		if (time() > $WarmwasserStart and time() < $WarmwasserEnde)
		{
			
			IPS_LogMessage("zHeizung", "WW:" . $SPOben . " : Heißwasser: " . $Heißwasser . " : Heißwasser aus:" . ($Heißwasser +5) . " : Heißwasser an:" . ($Heißwasser -10 ));	
			// Heizung is aus (Warmwasser)
			// Speichertemperatur oben > 65 zusatzHeizung aus
			//if ($SPOben > ($Heißwasser + 5) and !$ZHH)
			if ($SPOben > ($Heißwasser + 5))
			{
				$ZHW = false;
				IPS_LogMessage("zHeizung", "WWaus:" . $SPOben);	
			}
			else
			// Speichertemperatur oben < 55 zusatzHeizung an
			if ($SPOben < ($Heißwasser - 10) or $SPOben < ($Heißwasser + 5) )
			{
				$ZHW = True;
				//SetValue($this->GetIDForIdent("R4"), true);
				IPS_LogMessage("zHeizung", "WWan:" . $SPOben);
			}
		}
		
		if($ZHW)
		{
			//SetValueBoolean($ZHID, true);
			$RC = @HM_WriteValueBoolean($ZHID, "STATE" , True);
			echo "Heizung an";
		}
		else
		{
			//$RC = HM_RequestStatus ($ZHID)
			//if($RC)
			//{
				$RC = @HM_WriteValueBoolean($ZHID, "STATE" , false);
				echo "Heizung auß";
			//}
		}
	}
}
