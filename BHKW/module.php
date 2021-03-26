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
			$this->IPS_CreateVariableProfile("Kirsch.Volt", 1, " Volt", 0, 0,1, 0, "");
			$this->IPS_CreateVariableProfile("Kirsch.Ampere", 2, " Ampere", 0, 0,1,2, ""); 
			$this->IPS_CreateVariableProfile("Kirsch.Frequenz", 2, " Hz", 0, 0,1,2, ""); 
			$this->IPS_CreateVariableProfile("Kirsch.Prozent", 1, " %", 0, 100,1, 0, "");
			$this->IPS_CreateVariableProfile("Kirsch.Status", 1, "", 1, 11, 1, 2, "");
				
			IPS_SetVariableProfileAssociation("Kirsch.Status", 1, "gestoppet", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 2, "startet", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 3, "aufwärmen", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 4, "läuft", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 5, "abkühlen", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 10, "Notstop", "", 0xff0000);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 11, "Fehler", "", 0xff0000);	
			$Parent = $this->GetParentId();
			IPS_LogMessage("BHKW ID", $Parent);
			//BHKW statePP Variablen anlegen
			$this->RegisterVariableInteger("KirschStatus", "Status", "Kirsch.Status", 100);
			//$StatusID =IPS_GetObjectIDByIdent("KirschStatus",$BHKWID);
			//IPS_SetParent($StatusID, $CatID);
			$this->RegisterVariableInteger("Zielleistung", "Zielleistung", "Kirsch.Kw", 110);
			$this->RegisterVariableInteger("Referenzleistung", "Referenz Leistung", "Kirsch.Watt", 120);
			//E1 Spannung Phase 1
			//E2 Spannung Phase 2
			//E3 Spannung Phase 3
			$this->RegisterVariableInteger("E1", "Spannung Phase1", "Kirsch.Volt", 130);	
			$this->RegisterVariableInteger("E2", "Spannung Phase2", "Kirsch.Volt", 140);
			$this->RegisterVariableInteger("E3", "Spannung Phase3", "Kirsch.Volt", 150);
			//E4 Strom Phase 1
			//E5 Strom Phase 2
			//E6 Strom Phase 3
			$this->RegisterVariableFloat("E4", "Strom Phase1", "Kirsch.Ampere", 160);	
			$this->RegisterVariableFloat("E5", "Strom Phase2", "Kirsch.Ampere", 170);
			$this->RegisterVariableFloat("E6", "Strom Phase3", "Kirsch.Ampere", 180);
			//E7 Wirkleistung Gesamt
			//E71 Wirkleistung Phase 1
			//E72 Wirkleistung Phase 2
			//E73 Wirkleistung Phase 3
			$this->RegisterVariableInteger("E7", "Wirkleistung Gesamt", "Kirsch.Watt", 190);
			$this->RegisterVariableInteger("E71", "Wirkleistung Phase1", "Kirsch.Watt", 200);	
			$this->RegisterVariableInteger("E72", "Wirkleistung Phase2", "Kirsch.Watt", 210);
			$this->RegisterVariableInteger("E73", "Wirkleistung Phase3", "Kirsch.Watt", 220);
			//E81 Scheinleistung Phase 1
			//E82 Scheinleistung Phase 2
			//E83 Scheinleistung Phase 3
			$this->RegisterVariableInteger("E81", "Scheinleistung Phase1", "Kirsch.Watt", 230);	
			$this->RegisterVariableInteger("E82", "Scheinleistung Phase2", "Kirsch.Watt", 240);
			$this->RegisterVariableInteger("E83", "Scheinleistung Phase3", "Kirsch.Watt", 250);
			//E101 Frequenz Phase 1
			//E102 Frequenz Phase 2
			//E103 Frequenz Phase 3
			$this->RegisterVariableFloat("E101", "Frequenz Phase1", "Kirsch.Frequenz", 260);	
			$this->RegisterVariableFloat("E102", "Frequenz Phase2", "Kirsch.Frequenz", 270);
			$this->RegisterVariableFloat("E103", "Frequenz Phase3", "Kirsch.Frequenz", 280);
			//
			$this->RegisterVariableFloat("TI3", "Heizwasser", "~Temperature", 290);
			$this->RegisterVariableFloat("TI4", "Öltemperatur", "~Temperature", 300);
			$this->RegisterVariableFloat("TI5", "Abgasteperatur", "~Temperature", 310);
			$this->RegisterVariableFloat("TI6", "Gehäusetemperatur", "~Temperature", 320);
			

			$this->RegisterVariableInteger("Motordrehzahl", "Motordrehzahl", "Kirsch.UpM", 500);
			$this->RegisterVariableInteger("Speicherladepumpe", "Speicherladepumpe", "Kirsch.Prozent", 550);
			$this->RegisterVariableInteger("Drosselklapenstellung", "Drosselklapenstellung", "Kirsch.Prozent", 600);
			
			$this->RegisterVariableFloat("Speicheroben", "Speichertemperatur oben", "~Temperature", 700);
			$this->RegisterVariableFloat("Speichermitte", "Speichertemperatur mitte", "~Temperature", 750);
			$this->RegisterVariableFloat("Speicherunten", "Speichertemperatur unten", "~Temperature", 800);
				
			$this->RegisterVariableFloat("Aussentemperatur", "Außentemperatur", "~Temperature", 850);
			$this->RegisterVariableFloat("T3", "Vorlauf Temperatur ist", "~Temperature", 910)
			$this->RegisterVariableFloat("VorlaufTemperaturSoll", "Vorlauf Temperatur soll", "~Temperature", 900);
			$this->RegisterVariableFloat("RuecklaufTemperatur", "Rücklauf Temperatur", "~Temperature", 950);
				
				//statePower Variablen anlegen
			//$eventID = IPS_CreateEvent(0);
			//IPS_SetParent($eventID, $this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventCondition($eventID,$this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventTrigger($eventID,0,$this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventScript($eventID, "$this->VorlaufSoll()");
				

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
			//const TempDiff =40;
			//const VorlaufSoll20 = 45;
			//const VorlaufSollminus20 = 70;
			
			//$VorlaufSoll = GetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"));
			$AussenTemp = GetValueFloat($this->GetIDForIdent("Aussentemperatur"));
			$VorlaufTempDiff = 70 - 45;
			$VorlaufTempStep = $VorlaufTempDiff/40;
			$VorlaufSoll = ((20-$AussenTemp)* $VorlaufTempStep) + 45;
			IPS_LogMessage("AußentemperaturT",$VorlaufSoll);
			IPS_LogMessage("Außentemperatur", $this->GetIDForIdent("Aussentemperatur"));
			SetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"), $VorlaufSoll);
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
			
			//$ScriptData['TI3'] =  (float) $xmlData->sensors[0]->TI3;
			//tValueFloat($this->GetIDForIdent("TI3") , $ScriptData['TI3']);						
			//criptData['OelT'] = (float) $xmlData->sensors[0]->TI4;
			//tValueFloat ($this->GetIDForIdent("TI4"), $ScriptData['OelT']);
			//criptData['AT'] =  (float) $xmlData->sensors[0]->TI5;
			//tValueFloat ($this->GetIDForIdent("TI5") , $ScriptData['AT']);
			//criptData['GT'] =  (float) $xmlData->sensors[0]->TI6;
			//tValueFloat ($this->GetIDForIdent("TI6") , $ScriptData['GT']);
			
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

			/*[Eickeloh\Heizung\BHKW\Heizung\Rücklauftemperatur]*/
			$ScriptData['T6'] =  (Float) $xmlData->sensors[0]->T6;
			SetValue ($this->GetIDForIdent("RuecklaufTemperatur") , $ScriptData['T6']);


			
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
    		protected function GetParentId()
    		{
        		$instance = @IPS_GetInstance($this->InstanceID);
        		return $instance['ConnectionID'];
    		}
	}
