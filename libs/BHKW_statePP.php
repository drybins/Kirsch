<?php
		private function statePP($data)
		{
			try
 			{
				$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
				//echo "Alles ok!";
  			}
  			catch(Exception $ex)
  			{
 				//print_r($ex);
  				IPS_LogMessage("BHKW statePP Fehler", $data);
			}
			
			//Status des BHKW'S
			$ScriptData['STATUS'] = (string) $xmlData->common[0]->state;			
			$StatusID = $this->GetIDForIdent("KirschStatus");
			IPS_LogMessage("BHKW statePP StatusID", $StatusID);
			
			switch ($ScriptData['STATUS']) 
			{
				case "stop":
				SetValueInteger ($StatusID, 1);
				break;
			case "start":
				SetValueInteger ($StatusID, 2);
				break;
			case "warmup":
				SetValueInteger ($StatusID, 3);
				break;
			case "running":
				SetValueInteger ($StatusID, 4);
				break;
			case "cooldown":
				SetValueInteger ($StatusID, 5);
				break;
			case "selftest":
				SetValueInteger ($StatusID, 6);
				break;
			case "emergencystop":
				SetValueInteger ($StatusID, 10);
				break;  
			case "error":
				SetValueInteger ($StatusID, 11);
				break;         
			default:
				//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
			}
			
			// Target Power
			$ScriptData['TP'] = (integer) $xmlData->common[0]->targetPower;
			SetValueInteger ($this->GetIDForIdent("Zielleistung"), $ScriptData['TP']);
			//Referenzleistung
			$ScriptData['RL'] = (float) $xmlData->common[0]->referencePower*1000;
			SetValueInteger ($this->GetIDForIdent("Referenzleistung"), $ScriptData['RL']);
			
			/*[Eickeloh\Heizung\BHKW\Spannung*/
			$ScriptData['E1'] = (Float) $xmlData->electric[0]->E1;
			SetValue ($this->GetIDForIdent("E1") , $ScriptData['E1']);
			$ScriptData['E2'] = (Float) $xmlData->electric[0]->E2;
			SetValue ($this->GetIDForIdent("E2") , $ScriptData['E2']);
			$ScriptData['E3'] = (Float) $xmlData->electric[0]->E3;
			SetValue ($this->GetIDForIdent("E3") , $ScriptData['E3']);
			
			/*[Eickeloh\Heizung\BHKW\Strom*/
			$ScriptData['E4'] = (Float) $xmlData->electric[0]->E4;
			SetValue ($this->GetIDForIdent("E4") , $ScriptData['E4']);
			$ScriptData['E5'] = (Float) $xmlData->electric[0]->E5;
			SetValue ($this->GetIDForIdent("E5") , $ScriptData['E5']);
			$ScriptData['E6'] = (Float) $xmlData->electric[0]->E6;
			SetValue ($this->GetIDForIdent("E6") , $ScriptData['E6']);
			
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Gesamt]*/
			$ScriptData['E7'] = (Float) $xmlData->electric[0]->E7;
			SetValue ($this->GetIDForIdent("E7") , $ScriptData['E7']);
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Phase1]*/
			$ScriptData['E71'] = (integer) $xmlData->electric[0]->E71;
			SetValue ($this->GetIDForIdent("E71") , $ScriptData['E71']);
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Phase2]*/
			$ScriptData['E72'] = (integer) $xmlData->electric[0]->E72;
			SetValue ($this->GetIDForIdent("E72") , $ScriptData['E72']);
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Phase3]*/
			$ScriptData['E73'] = (integer) $xmlData->electric[0]->E73;
			SetValue ($this->GetIDForIdent("E73") , $ScriptData['E73']);
			
			/*[Eickeloh\Heizung\BHKW\Scheinleistung*/
			$ScriptData['E81'] = (Float) $xmlData->electric[0]->E81;
			SetValue ($this->GetIDForIdent("E81") , $ScriptData['E81']);
			$ScriptData['E82'] = (Float) $xmlData->electric[0]->E82;
			SetValue ($this->GetIDForIdent("E82") , $ScriptData['E82']);
			$ScriptData['E83'] = (Float) $xmlData->electric[0]->E83;
			SetValue ($this->GetIDForIdent("E83") , $ScriptData['E83']);
			
			/*[Eickeloh\Heizung\BHKW\Frequenz*/
			$ScriptData['E101'] = (Float) $xmlData->electric[0]->E101;
			SetValue ($this->GetIDForIdent("E101") , $ScriptData['E101']);
			$ScriptData['E102'] = (Float) $xmlData->electric[0]->E102;
			SetValue ($this->GetIDForIdent("E102") , $ScriptData['E102']);
			$ScriptData['E103'] = (Float) $xmlData->electric[0]->E103;
			SetValue ($this->GetIDForIdent("E103") , $ScriptData['E103']);
			
			$ScriptData['TI1'] =  (float) $xmlData->sensors[0]->TI1;
			SetValueFloat($this->GetIDForIdent("TI3") , $ScriptData['TI1']);						
			$ScriptData['TI2'] =  (float) $xmlData->sensors[0]->TI2;
			SetValueFloat($this->GetIDForIdent("TI2") , $ScriptData['TI2']);
			$ScriptData['TI4'] = (float) $xmlData->sensors[0]->TI4;
			SetValueFloat ($this->GetIDForIdent("TI4"), $ScriptData['TI4']);
			$ScriptData['TI5'] =  (float) $xmlData->sensors[0]->TI5;
			SetValueFloat ($this->GetIDForIdent("TI5") , $ScriptData['TI5']);
			$ScriptData['TI6'] =  (float) $xmlData->sensors[0]->TI6;
			SetValueFloat ($this->GetIDForIdent("TI6") , $ScriptData['TI6']);
			
			$ScriptData['TS3'] =  (string) $xmlData->sensors[0]->TS3;
			SetValueString ($this->GetIDForIdent("TS3"), $ScriptData['TS3']);
			$ScriptData['TS5'] =  (string) $xmlData->sensors[0]->TS5;
			SetValueString ($this->GetIDForIdent("TS5") , $ScriptData['TS5']);
			$ScriptData['TS6'] =  (string) $xmlData->sensors[0]->TS6;
			SetValueString ($this->GetIDForIdent("TS6") , $ScriptData['TS6']);
			
			$ScriptData['S1'] =  (Float) $xmlData->sensors[0]->S1;
			SetValue ($this->GetIDForIdent("S1")  , $ScriptData['S1']);
			
			$ScriptData['S4'] =  (string) $xmlData->sensors[0]->S4;
			SetValueString ($this->GetIDForIdent("S4") , $ScriptData['S4']);
			$ScriptData['S7'] =  (string) $xmlData->sensors[0]->S7;
			SetValueString ($this->GetIDForIdent("S7") , $ScriptData['S7']);
			$ScriptData['S9'] =  (string) $xmlData->sensors[0]->S9;
			SetValueString ($this->GetIDForIdent("S9") , $ScriptData['S9']);
			$ScriptData['S12'] =  (string) $xmlData->sensors[0]->S12;
			SetValueString ($this->GetIDForIdent("S12") , $ScriptData['S12']);
			$ScriptData['S13'] =  (string) $xmlData->sensors[0]->S13;
			SetValueString ($this->GetIDForIdent("S13") , $ScriptData['S13']);
			$ScriptData['C1'] =  (string) $xmlData->actors[0]->C1;
			SetValueString ($this->GetIDForIdent("C1") , $ScriptData['C1']);
			$ScriptData['C2'] =  (string) $xmlData->actors[0]->C2;
			SetValueString ($this->GetIDForIdent("C2") , $ScriptData['C2']);
			$ScriptData['SS'] =  (string) $xmlData->actors[0]->SS;
			SetValueString ($this->GetIDForIdent("SS") , $ScriptData['SS']);

			$ScriptData['V1'] =  (string) $xmlData->actors[0]->V1;
			switch ($ScriptData['V1']) 
			{
			case "on":
				SetValueBoolean($this->GetIDForIdent("V1"), true);
				break;
			case "off":
				SetValueBoolean ($this->GetIDForIdent("V1"), false);
				break;
			default:
				//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
			}
			$ScriptData['V2'] =  (Float) $xmlData->actors[0]->V2;
			SetValue ($this->GetIDForIdent("V2")  , $ScriptData['V2']);
			$ScriptData['V3'] =  (string) $xmlData->actors[0]->V3;
			SetValueString ($this->GetIDForIdent("V3") , $ScriptData['V3']);
			
			$ScriptData['P1'] =  (Float) $xmlData->actors[0]->P1;
			SetValue ($this->GetIDForIdent("P1")  , $ScriptData['P1']);
			$ScriptData['P2'] =  (string) $xmlData->actors[0]->P2;
			//SetValueString ($this->GetIDForIdent("P2") , $ScriptData['P2']);
			switch ($ScriptData['P2']) 
			{
			case "off":
				SetValueInteger($this->GetIDForIdent("P2"), 1);
				break;
			case "forward":
				SetValueInteger ($this->GetIDForIdent("P2"), 2);
				break;
			case "reverse":
				SetValueInteger ($this->GetIDForIdent("P2"), 3);
				break;
			default:
				//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
			}
			
			/*[Eickeloh\Heizung\BHKW\Heizung\Außentemperatur]*/
			$ScriptData['T1'] =  (Float) $xmlData->sensors[0]->T1;
			SetValue ($this->GetIDForIdent("T1") , $ScriptData['T1']);
			$this->VorlaufSoll();
			/*[Eickeloh\Heizung\BHKW\Heizung\Speichertemperatur oben]*/
			$ScriptData['T2'] =  (Float) $xmlData->sensors[0]->T2;
			SetValueFloat ($this->GetIDForIdent("T2") , $ScriptData['T2']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Speichertemperatur mitte]*/
			$ScriptData['T3'] =  (Float) $xmlData->sensors[0]->T3;
			SetValueFloat ($this->GetIDForIdent("T3") , $ScriptData['T3']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Speichertemperatur unten]*/
			$ScriptData['T4'] =  (Float) $xmlData->sensors[0]->T4;
			SetValueFloat($this->GetIDForIdent("T4") , $ScriptData['T4']);
						
			$ScriptData['R1'] =  (string) $xmlData->actors[0]->R1;
			switch ($ScriptData['R1']) 
			{
			case "on":
				SetValueBoolean($this->GetIDForIdent("R1"), true);
				break;
			case "off":
				SetValueBoolean ($this->GetIDForIdent("R1"), false);
				break;
			default:
				//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
			}
			
			/*[Eickeloh\Heizung\BHKW\Heizung\Rücklauftemperatur]*/
			$ScriptData['T5'] =  (Float) $xmlData->sensors[0]->T5;
			SetValue ($this->GetIDForIdent("T5") , $ScriptData['T5']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Rücklauftemperatur]*/
			$ScriptData['T6'] =  (Float) $xmlData->sensors[0]->T6;
			SetValue ($this->GetIDForIdent("T6") , $ScriptData['T6']);
			/*
			$ScriptData['mixer1'] =  (string) $xmlData->actors[0]->mixer1;
			switch ($ScriptData['mixer1']) 
			{
			case "on":
				SetValueBoolean($this->GetIDForIdent("mixer1"), true);
				break;
			case "off":
				SetValueBoolean ($this->GetIDForIdent("mixer1"), false);
				break;
			default:
				//SetValueString (14320 , "Status nicht gefunden:" . $ScriptData['STATUS']);
			}
			*/
			$ScriptData['totalTime'] =  (Float) $xmlData->operatingData[0]->totalTime/3600;
			SetValue ($this->GetIDForIdent("totalTime") , $ScriptData['totalTime']);
			$ScriptData['oilTime'] =  (Float) $xmlData->operatingData[0]->oilTime/3600;
			SetValue ($this->GetIDForIdent("oilTime") , $ScriptData['oilTime']);
			$ScriptData['electricity'] =  (Float) $xmlData->operatingData[0]->electricity;
			SetValue ($this->GetIDForIdent("electricity") , $ScriptData['electricity']);			
			$ScriptData['heat'] =  (Float) $xmlData->operatingData[0]->heat;
			SetValue ($this->GetIDForIdent("heat") , $ScriptData['heat']);		
		}
