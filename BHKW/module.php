<?php
//const TempDiff =40;
//const VorlaufSoll20 = 45;
//const VorlaufSollminus20 = 70;
if (!defined('TempDiff')) {
    define('TempDiff', '40');
}
if (!defined('VorlaufSoll20')) {
    define('VorlaufSoll20', '45');
}
if (!defined('VorlaufSollminus20')) {
    define('VorlaufSollminus20', '70');
}

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
			$this->IPS_CreateVariableProfile("Kirsch.kWh", 2, " kWh", 0, 0,1, 0, "");
			$this->IPS_CreateVariableProfile("Kirsch.Std", 2, " Stunden", 0, 0,1, 2, "");
			
			$this->IPS_CreateVariableProfile("Kirsch.Status", 1, "", 1, 11, 1, 2, "");
			IPS_SetVariableProfileAssociation("Kirsch.Status", 1, "gestoppet", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 2, "startet", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 3, "aufwärmen", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 4, "läuft", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 5, "abkühlen", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 6, "selbsttest", "", 0x7cfc00);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 10, "Notstop", "", 0xff0000);
			IPS_SetVariableProfileAssociation("Kirsch.Status", 11, "Fehler", "", 0xff0000);
			
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
			
			//$Parent = $this->GetParentId();
			//IPS_LogMessage("BHKW ID", $Parent);
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
			//

			$this->RegisterVariableString("TS3", "Vorlauf-STB", "", 330);
			$this->RegisterVariableString("TS5", "Abgas-STB", "", 340);
			$this->RegisterVariableString("TS6", "Gehäuse-STB", "", 350);
			
			$this->RegisterVariableInteger("S1", "Motordrehzahl", "Kirsch.UpM", 360);
			$this->RegisterVariableString("S4", "Öldrucksensor", "", 370);
			$this->RegisterVariableString("S7", "Leckagesensor", "", 380);
			$this->RegisterVariableString("S9", "Gaswarnsensor", "", 390);
			$this->RegisterVariableString("S12", "Gasüberdruckwächter", "", 400);
			$this->RegisterVariableString("S13", "Kondensatniveau", "", 410); 
			$this->RegisterVariableString("C1", "Hauptschütz", "", 420);
			$this->RegisterVariableString("C2", "Kondensatorschütz", "", 430);
			$this->RegisterVariableString("SS", "Sanftanlauf", "", 440);
			
			$this->RegisterVariableBoolean("V1", "Gasventil", "Kirsch.Gasventil", 450);
			$this->RegisterVariableInteger("V2", "Drosselklapenstellung", "Kirsch.Prozent", 460);
			$this->RegisterVariableString("V3", "Status Lambdaregelung", "", 470);
	
			$this->RegisterVariableInteger("P1", "Leistung der Speicherladepumpe", "Kirsch.Prozent", 480);
			$this->RegisterVariableInteger("P2", "Drehrichtung Ölpumpe", "Kirsch.OelPumpe", 490);
			
			$this->RegisterVariableFloat("T1", "Außentemperatur", "~Temperature", 500);
			$this->RegisterVariableFloat("T2", "Speichertemperatur oben", "~Temperature", 510);
			$this->RegisterVariableFloat("T3", "Speichertemperatur mitte", "~Temperature", 520);
			$this->RegisterVariableFloat("T4", "Speichertemperatur unten", "~Temperature", 530);

			$this->RegisterVariableBoolean("R1", "Heizkreispumpe", "Kirsch.AnAus", 532);
			
			$this->RegisterVariableFloat("VorlaufTemperaturSoll", "Vorlauf Temperatur soll", "~Temperature", 535);
			$this->RegisterVariableFloat("T5", "Vorlauf Heizkreis 1", "~Temperature", 540);
			$this->RegisterVariableFloat("T6", "Rücklauf Heizkreis 1", "~Temperature", 540);
			
			//$this->RegisterVariableBoolean("mixer1", "Mischer Heizkreis 1", "", 550);
			$this->RegisterVariableFloat("totalTime", "Gesamtbetriebszeit", "Kirsch.Std", 560);
			$this->RegisterVariableFloat("oilTime", "Betriebszeit nach Ölnachfüllung", "Kirsch.Std", 570);
			$this->RegisterVariableFloat("electricity", "Elektrische Energiemenge", "Kirsch.kWh", 580);
			$this->RegisterVariableFloat("heat", "Thermische Energiemenge", "Kirsch.kWh", 590);			
				
				//statePower Variablen anlegen
			//$eventID = IPS_CreateEvent(0);
			//IPS_SetParent($eventID, $this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventCondition($eventID,$this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventTrigger($eventID,0,$this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventScript($eventID, "$this->VorlaufSoll()");
			/*
			$CatID = IPS_CreateCategory();       // Kategorie anlegen
			IPS_SetName($CatID, "statePP");	
			$this->RegisterVariableInteger("LPR", "Letztes Paket empfangen", " ~UnixTimestamp", 900);
			IPS_SetParent($this->GetIDForIdent('LPR'),$CatID);
			*/
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
					//SetValue(37729, time());
					$this->statePP($data);
					break;
				case "errors":
					//SetValue(37729, time());
					$this->errors($data);
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

			
			//$VorlaufSoll = GetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"));
			$AussenTemp = GetValueFloat($this->GetIDForIdent("T1"));
			$VorlaufTempDiff = 70 - 45;
			$VorlaufTempStep = $VorlaufTempDiff/40;
			$VorlaufSoll = ((20-$AussenTemp)* $VorlaufTempStep) + 45;
			IPS_LogMessage("$VorlaufTempStep",$VorlaufTempStep);
			IPS_LogMessage("AußentemperaturT",$VorlaufSoll);
			IPS_LogMessage("Außentemperatur", $this->GetIDForIdent("T1"));
			SetValueFloat($this->GetIDForIdent("VorlaufTemperaturSoll"), $VorlaufSoll);
		}
		
		private function errors($data)
		{
			$arr = [];
			$array = json_decode(json_encode(simplexml_load_string($data)),true);
			if(!empty($array))
			{
				$sortarray = array();
				array_multisort($sortArray['date'],SORT_DESC,$array);
				$i=0;
				foreach($sortarray['error'] as $elem)
				{
					IPS_LogMessage("BHKW Fehler datum:", $elem['date']);	
				}
			}
			//print_r ($array);
			IPS_LogMessage("BHKW errors:", $data);
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
			
			$ScriptData['TI3'] =  (float) $xmlData->sensors[0]->TI3;
			SetValueFloat($this->GetIDForIdent("TI3") , $ScriptData['TI3']);						
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
