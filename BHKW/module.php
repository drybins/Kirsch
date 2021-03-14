<?php
	class BHKW extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
				// Kirsch BHKW Profile anlegen
			    	$this->IPS_CreateVariableProfile("Kirsch.UpM", 1, " UpM", 0, 0, 1, 0, "");
				$this->IPS_CreateVariableProfile("Kirsch.Kw", 1, " Kw", 0, 0,1, 2, "");
				$this->IPS_CreateVariableProfile("Kirsch.Watt", 1, " Watt", 0, 0,1, 0, "");
				$this->IPS_CreateVariableProfile("Kirsch.Prozent", 1, " %", 0, 100,1, 0, "");
				$this->IPS_CreateVariableProfile("Kirsch.Status", 1, "", 1, 11, 1, 2, "");
				
				
				IPS_SetVariableProfileAssociation("Kirsch.Status", 1, "gestoppet", "", 0x7cfc00);
				IPS_SetVariableProfileAssociation("Kirsch.Status", 2, "startet", "", 0x7cfc00);
				IPS_SetVariableProfileAssociation("Kirsch.Status", 3, "aufwärmen", "", 0x7cfc00);
				IPS_SetVariableProfileAssociation("Kirsch.Status", 4, "läuft", "", 0x7cfc00);
				IPS_SetVariableProfileAssociation("Kirsch.Status", 5, "abkühlen", "", 0x7cfc00);
				IPS_SetVariableProfileAssociation("Kirsch.Status", 10, "Notstop", "", 0xff0000);
				IPS_SetVariableProfileAssociation("Kirsch.Status", 11, "Fehler", "", 0xff0000);	
				//$BHKWID = IPS_GetInstance($this->InstanceID)["InstanceID"];
				//Echo "BHKWID=" . $BHKWID;
				//if(IPS_CategoryExists(IPS_GetObjectIDByIdent("statePP",$BHKWID)))
				//{
					//$CatID = IPS_CreateCategory();       // Kategorie anlegen
					//IPS_SetName($CatID, "statePP"); // Kategorie benennen
					//IPS_SetParent($CatID, $BHKWID);
				//}
				//else
				//{
				//	$CatID = IPS_GetObjectIDByIdent("statePP",$BHKWID);
				//}
				//IPS_LogMessage("BHKW ID", $BHKWID);
				//$Parent = IPS_GetParent();
				//BHKW statePP Variablen anlegen
				$this->RegisterVariableInteger("KirschStatus", "Status", "Kirsch.Status", 10);
				//$StatusID =IPS_GetObjectIDByIdent("KirschStatus",$BHKWID);
				//IPS_SetParent($StatusID, $CatID);
				$this->RegisterVariableInteger("Zielleistung", "Zielleistung", "Kirsch.Kw", 15);
				$this->RegisterVariableInteger("Referenzleistung", "Referenz Leistung", "Kirsch.Watt", 20);
				$this->RegisterVariableInteger("Wirkleistung", "Wirkleistung", "Kirsch.Watt", 25);
				
				$this->RegisterVariableFloat("Oeltemperatur", "Öltemperatur", "~Temperature", 30);
				$this->RegisterVariableFloat("Heizwasser", "Heizwasser", "~Temperature", 35);
				$this->RegisterVariableFloat("Abgasteperatur", "Abgasteperatur", "~Temperature", 40);
				$this->RegisterVariableFloat("Gehaeusetemperatur", "Gehäusetemperatur", "~Temperature", 45);
				$this->RegisterVariableInteger("Motordrehzahl", "Motordrehzahl", "Kirsch.UpM", 50);
				$this->RegisterVariableInteger("Speicherladepumpe", "Speicherladepumpe", "Kirsch.Prozent", 55);
				$this->RegisterVariableInteger("Drosselklapenstellung", "Drosselklapenstellung", "Kirsch.Prozent", 60);
				
				$this->RegisterVariableFloat("Speicheroben", "Speichertemperatur oben", "~Temperature", 70);
				$this->RegisterVariableFloat("Speichermitte", "Speichertemperatur mitte", "~Temperature", 75);
				$this->RegisterVariableFloat("Speicherunten", "Speichertemperatur unten", "~Temperature", 80);
				
				$this->RegisterVariableFloat("Aussentemperatur", "Außentemperatur", "~Temperature", 85);
				$this->RegisterVariableFloat("VorlaufTemperatur", "Vorlauf Temperatur", "~Temperature", 90);
				$this->RegisterVariableFloat("RuecklaufTemperatur", "Rücklauf Temperatur", "~Temperature", 95);
				
				$this->RegisterVariableInteger("WLPG", "Wirkleistung gesamt", "Kirsch.Watt", 100);
				$this->RegisterVariableInteger("WLP1", "Wirkleistung Phase1", "Kirsch.Watt", 105);
				$this->RegisterVariableInteger("WLP2", "Wirkleistung Phase2", "Kirsch.Watt", 110);
				$this->RegisterVariableInteger("WLP3", "Wirkleistung Phase3", "Kirsch.Watt", 115);
				
				$this->RegisterVariableFloat("SP1", "Spannung Phase1", "~Volt.230", 120);
				$this->RegisterVariableFloat("SP2", "Spannung Phase2", "~Volt.230", 125);
				$this->RegisterVariableFloat("SP3", "Spannung Phase3", "~Volt.230", 130);
				
				//statePower Variablen anlegen
				
				$this->ConnectParent("{33B9B2D7-6BC5-1CF6-A86F-E76622A7FFB7}");
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}

		public function Send()
		{
			$this->SendDataToParent(json_encode(Array("DataID" => "{EB1230DE-C6EE-1739-07A1-3742EE4FB43F}")));
		}

		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			$data = utf8_decode($data->Buffer);
			//IPS_LogMessage("Device RECV", $cmd);
			//IPS_LogMessage("Device RECV", utf8_decode($data->Buffer));
			
			$start = strpos($data,"<",5);
			$end = strpos($data,">",$start);
			$cmd = substr($data, $start+1, $end-$start-1);
			IPS_LogMessage("Splitter CMD", $cmd);

			switch ($cmd)
			{
				case "statePP":
					$this->statePP($data);
					break;
				case "statePower":
					//$this->statePP($data);
					break;
				default:
					break;
			}
		}
		
		public function VorlaufSoll()
		{
			IPS_LogMessage("AußentemperaturT");
			IPS_LogMessage("Außentemperatur", $this->GetIDForIdent("Aussentemperatur"));
		}
				       
		private function statePP($data)
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			
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
			//Wirkleistung
			$ScriptData['E7'] = (integer) $xmlData->electric[0]->E7;
			SetValue ($this->GetIDForIdent("Wirkleistung"), $ScriptData['E7']);
			
			$ScriptData['OelT'] = (float) $xmlData->sensors[0]->TI4;
			SetValueFloat ($this->GetIDForIdent("Oeltemperatur"), $ScriptData['OelT']);
			$ScriptData['HW'] =  (float) $xmlData->sensors[0]->TI3;
			SetValueFloat($this->GetIDForIdent("Heizwasser") , $ScriptData['HW']);
			$ScriptData['AT'] =  (float) $xmlData->sensors[0]->TI5;
			SetValueFloat ($this->GetIDForIdent("Abgasteperatur") , $ScriptData['AT']);
			$ScriptData['GT'] =  (float) $xmlData->sensors[0]->TI6;
			SetValueFloat ($this->GetIDForIdent("Gehaeusetemperatur") , $ScriptData['GT']);
			
			$ScriptData['S1'] =  (Float) $xmlData->sensors[0]->S1;
			SetValue ($this->GetIDForIdent("Motordrehzahl")  , $ScriptData['S1']);
			$ScriptData['P1'] =  (Float) $xmlData->actors[0]->P1;
			SetValue ($this->GetIDForIdent("Speicherladepumpe")  , $ScriptData['P1']);
			$ScriptData['V2'] =  (Float) $xmlData->actors[0]->V2;
			SetValue ($this->GetIDForIdent("Drosselklapenstellung")  , $ScriptData['V2']);
			
			/*[Eickeloh\Heizung\BHKW\Heizung\Speichertemperatur oben]*/
			$ScriptData['SO'] =  (Float) $xmlData->sensors[0]->T2;
			SetValueFloat ($this->GetIDForIdent("Speicheroben") , $ScriptData['SO']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Speichertemperatur mitte]*/
			$ScriptData['SM'] =  (Float) $xmlData->sensors[0]->T3;
			SetValueFloat ($this->GetIDForIdent("Speichermitte") , $ScriptData['SM']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Speichertemperatur unten]*/
			$ScriptData['SU'] =  (Float) $xmlData->sensors[0]->T4;
			SetValueFloat($this->GetIDForIdent("Speicherunten") , $ScriptData['SU']);
			
			/*[Eickeloh\Heizung\BHKW\Heizung\Außentemperatur]*/
			$ScriptData['T1'] =  (Float) $xmlData->sensors[0]->T1;
			SetValue ($this->GetIDForIdent("Aussentemperatur") , $ScriptData['T1']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Vorlauftemperatur ist]*/
			$ScriptData['T5'] =  (Float) $xmlData->sensors[0]->T5;
			SetValue ($this->GetIDForIdent("VorlaufTemperatur") , $ScriptData['T5']);
			/*[Eickeloh\Heizung\BHKW\Heizung\Rücklauftemperatur]*/
			$ScriptData['T6'] =  (Float) $xmlData->sensors[0]->T6;
			SetValue ($this->GetIDForIdent("RuecklaufTemperatur") , $ScriptData['T6']);

			/*[Eickeloh\Heizung\BHKW\Wirkleistung Gesamt]*/
			$ScriptData['E7'] = (Float) $xmlData->electric[0]->E7;
			SetValue ($this->GetIDForIdent("WLPG") , $ScriptData['E7']);
			
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Phase1]*/
			$ScriptData['E71'] = (integer) $xmlData->electric[0]->E71;
			SetValue ($this->GetIDForIdent("WLP1") , $ScriptData['E71']);
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Phase2]*/
			$ScriptData['E72'] = (integer) $xmlData->electric[0]->E72;
			SetValue ($this->GetIDForIdent("WLP2") , $ScriptData['E72']);
			/*[Eickeloh\Heizung\BHKW\Wirkleistung Phase3]*/
			$ScriptData['E73'] = (integer) $xmlData->electric[0]->E73;
			SetValue ($this->GetIDForIdent("WLP3") , $ScriptData['E73']);
			
			/*[Eickeloh\Heizung\BHKW\Spannung Phase 1]*/
			$ScriptData['E1'] = (Float) $xmlData->electric[0]->E1;
			SetValue ($this->GetIDForIdent("SP1") , $ScriptData['E1']);
			/*[Eickeloh\Heizung\BHKW\Spannung Phase 2]*/
			$ScriptData['E2'] = (Float) $xmlData->electric[0]->E2;
			SetValue ($this->GetIDForIdent("SP2") , $ScriptData['E2']);
			/*[Eickeloh\Heizung\BHKW\Spannung Phase 3]*/
			$ScriptData['E3'] = (Float) $xmlData->electric[0]->E3;	
			SetValue ($this->GetIDForIdent("SP3") , $ScriptData['E3']);
			
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
