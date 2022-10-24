<?php
// include autoloader
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/libs/BHKW_Functions.php');
//define('__ROOT1__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/libs/BHKW_Zusatzheizung.php');
//define('__ROOT2__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/libs/BHKW_statePP.php');
//define('__ROOT3__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/libs/BHKW_statePower.php');
require_once(__ROOT__ . '/libs/BHKW_stateHeatControl.php');
require_once(__ROOT__ . '/libs/BHKW_errors.php');
require_once(__ROOT__ . '/libs/BHKW_valuePP.php');
//const TempDiff =40;
//const VorlaufSoll20 = 45;
//const VorlaufSollminus20 = 70;
//if (!defined('TempDiff')) {
//    define('TempDiff', '40');
//}
//if (!defined('VorlaufSoll20')) {
//    define('VorlaufSoll20', '20');
//}
//if (!defined('VorlaufSollminus20')) {
//    define('VorlaufSollminus20', '70');
//}

	class BHKW extends IPSModule {
	
		use BHKWFunctions, BHKWZusatzHeizung, BHKWstatePP, BHKWstatePower, BHKWstateHeatControl, BHKWerrors, BHKWvaluePP;
		
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
			
			//$HSID=IPS_CreateCategory();
			//ips_setname($HSID, "HeizungsSteuerung");
			//$Parent = @IPS_GetCategoryIDByName("BHKW" , 58416);
			//IPS_LogMessage("BHKW ID2", $Parent);
			//$Parent = $this->GetParentId();
			//print_r(IPS_GetInstance($Parent));
			//$ID = IPS_GetInstanceIDByName ("Kirsch BHKW Nano 4.12", 0);
			//IPS_LogMessage("BHKW ID1", $ID);
			///*
			$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
			$ID = IPS_GetInstanceListByModuleID($GUID)[0];
			IPS_LogMessage("BHKW ID1", $ID);
			if(!IPS_GetCategoryIDByName ("Heizkreislauf", $ID))
			{
			//	IPS_LogMessage("statePP schon da!", $statePPID);	

			$HKID=IPS_CreateCategory();
			ips_setname($HKID, "Heizkreislauf 1");
			IPS_SetParent($HKID, $ID);
			$HSID=IPS_CreateCategory();
			ips_setname($HSID, "Nachtabsenkung");
			IPS_SetParent($HSID, $HKID);
			
			$this->RegisterVariableInteger("NAStatus", "Status", "", 10);
			$NASID = $this->GetIDForIdent("NAStatus");
			IPS_SetParent($NASID, $HSID);
			}
			
			
			//$statePPID = IPS_GetCategoryIDByName ("statePP", $ID);
			//if(!IPS_GetCategoryIDByName ("statePP", $ID))
			//{
			//	IPS_LogMessage("statePP schon da!", $statePPID);	
			//}
			//*/
			$this->RegisterVariableInteger("Seriennummer", "Seriennummer", "", 10);
			//BHKW statePP Variablen anlegen
			$this->RegisterVariableInteger("KirschStatus", "Status", "Kirsch.Status", 100);
			$this->RegisterVariableInteger("HeizungsStatus", "Heizung Status", "Kirsch.Heizung", 101);
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
			$this->RegisterVariableFloat("TI2", "Kalt Wasser BHKW", "~Temperature", 285);
			$this->RegisterVariableFloat("TI3", "Heizwasser BHKW", "~Temperature", 290);
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
			$this->RegisterVariableFloat("T5", "Vorlauf Heizkreis 1", "~Temperature", 538);
			$this->RegisterVariableFloat("T6", "Rücklauf Heizkreis 1", "~Temperature", 540);
			$this->RegisterVariableBoolean("zH1", "ZusatzHeizung", "Kirsch.AnAus", 542);
			
			//$this->RegisterVariableBoolean("mixer1", "Mischer Heizkreis 1", "", 550);
			$this->RegisterVariableFloat("totalTime", "Gesamtbetriebszeit", "Kirsch.Std", 560);
			$this->RegisterVariableFloat("oilTime", "Betriebszeit nach Ölnachfüllung", "Kirsch.Std", 570);
			$this->RegisterVariableFloat("electricity", "Elektrische Energiemenge", "Kirsch.kWh", 580);
			$this->RegisterVariableFloat("heat", "Thermische Energiemenge", "Kirsch.kWh", 590);			
			//BHKW statePP Variablen anlegen ende
			
			//BHKW Status Variablen anlegen
			
			//BHKW Status Variablen anlegen ende
						
			//Errors Variablen anlegen
			$this->RegisterVariableString("class", "Klasse", "", 700);
			$this->RegisterVariableString("device", "Gerät", "", 710);
			$this->RegisterVariableString("type", "Typ", "", 720);
			$this->RegisterVariableString("occurrence", "Anzahl", "", 730);
			$this->RegisterVariableString("remoteConfirmed", "bestätigt", "", 740);
			$this->RegisterVariableString("level", "Level", "", 750);
			$this->RegisterVariableString("state", "Status", "", 760);
			$this->RegisterVariableString("date", "Datum", "", 770);
			$this->RegisterVariableString("time", "Zeit", "", 780);
			
			$this->RegisterVariableString("Software", "Software", "", 800);
			$this->RegisterVariableString("Messpunkt", "Mespunkt", "", 810);
			$this->RegisterVariableString("Fehler", "Fehler", "", 820);
						
			$this->RegisterVariableInteger("DLF", "Datum letzter Fehler", "~UnixTimestamp", 900);	

			$this->RegisterVariableBoolean("ZielleistungDA", "Zielleistung Dynamische Anpassung", "Kirsch.AnAus", 999);
			$this->RegisterVariableInteger("Zielleistung0", "Zielleistung 0 Uhr", "Kirsch.Kw", 1000);
			$this->RegisterVariableInteger("Zielleistung1", "Zielleistung 1 Uhr", "Kirsch.Kw", 1010);
			$this->RegisterVariableInteger("Zielleistung2", "Zielleistung 2 Uhr", "Kirsch.Kw", 1020);
			$this->RegisterVariableInteger("Zielleistung3", "Zielleistung 3 Uhr", "Kirsch.Kw", 1030);
			$this->RegisterVariableInteger("Zielleistung4", "Zielleistung 4 Uhr", "Kirsch.Kw", 1040);
			$this->RegisterVariableInteger("Zielleistung5", "Zielleistung 5 Uhr", "Kirsch.Kw", 1050);
			$this->RegisterVariableInteger("Zielleistung6", "Zielleistung 6 Uhr", "Kirsch.Kw", 1060);
			$this->RegisterVariableInteger("Zielleistung7", "Zielleistung 7 Uhr", "Kirsch.Kw", 1070);
			$this->RegisterVariableInteger("Zielleistung8", "Zielleistung 8 Uhr", "Kirsch.Kw", 1080);
			$this->RegisterVariableInteger("Zielleistung9", "Zielleistung 9 Uhr", "Kirsch.Kw", 1090);
			$this->RegisterVariableInteger("Zielleistung10", "Zielleistung 10 Uhr", "Kirsch.Kw", 1100);
			$this->RegisterVariableInteger("Zielleistung11", "Zielleistung 11 Uhr", "Kirsch.Kw", 1110);
			$this->RegisterVariableInteger("Zielleistung12", "Zielleistung 12 Uhr", "Kirsch.Kw", 1120);
			$this->RegisterVariableInteger("Zielleistung13", "Zielleistung 13 Uhr", "Kirsch.Kw", 1130);
			$this->RegisterVariableInteger("Zielleistung14", "Zielleistung 14 Uhr", "Kirsch.Kw", 1140);
			$this->RegisterVariableInteger("Zielleistung15", "Zielleistung 15 Uhr", "Kirsch.Kw", 1150);
			$this->RegisterVariableInteger("Zielleistung16", "Zielleistung 16 Uhr", "Kirsch.Kw", 1160);
			$this->RegisterVariableInteger("Zielleistung17", "Zielleistung 17 Uhr", "Kirsch.Kw", 1170);
			$this->RegisterVariableInteger("Zielleistung18", "Zielleistung 18 Uhr", "Kirsch.Kw", 1180);
			$this->RegisterVariableInteger("Zielleistung19", "Zielleistung 19 Uhr", "Kirsch.Kw", 1190);
			$this->RegisterVariableInteger("Zielleistung20", "Zielleistung 20 Uhr", "Kirsch.Kw", 1200);
			$this->RegisterVariableInteger("Zielleistung21", "Zielleistung 21 Uhr", "Kirsch.Kw", 1210);
			$this->RegisterVariableInteger("Zielleistung22", "Zielleistung 22 Uhr", "Kirsch.Kw", 1220);
			$this->RegisterVariableInteger("Zielleistung23", "Zielleistung 23 Uhr", "Kirsch.Kw", 1230);

			
			//statePower Variablen anlegen
			//$eventID = IPS_CreateEvent(0);
			//IPS_SetParent($eventID, $this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventCondition($eventID,$this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventTrigger($eventID,0,$this->GetIDForIdent('Aussentemperatur'));
			//IPS_SetEventScript($eventID, "$this->VorlaufSoll()");
			
			//$CatID = IPS_CreateCategory();       // Kategorie anlegen
			//IPS_SetName($CatID, "Test1");	
			//$instance = IPS_GetInstance($this->InstanceID);
			//$RCID = IPS_CreateKategorie("Visualisierung",0);
			$ViscatID =  $this->IPS_CreateKategorie("Visualisierung1",0,10);
			$BHKWID =  $this->IPS_CreateKategorie("BHKW",$ViscatID,10);
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
			//IPS_LogMessage("Device RECV", $data);
			//IPS_LogMessage("Device RECV", utf8_decode($data->Buffer));
			
			$start = strpos($data,"<",5);
			$end = strpos($data,">",$start);
			$cmd = substr($data, $start+1, $end-$start-1);
			//IPS_LogMessage("Splitter CMD", $cmd);
			//IPS_LogMessage("Splitter data", $data);
			$delimeter = "<?xml version='1.0' encoding='UTF-8'?>";
			$pos = strrpos($data, $delimeter);
			//IPS_LogMessage("Splitter Pos:", $pos);
			if($pos !== false)
			{
				$start = strpos($data,"<",43);
				$end = strpos($data,">",$start);
				$cmd = substr($data, $start+1, $end-$start-1);
				$data1 = $data;
				$data=substr($data1,0,38) . substr($data1,76);
			}	//$data = substr($data,0,$pos);
				//IPS_LogMessage("Splitter CMD2", $cmd);
				//IPS_LogMessage("Splitter Fehler Pos:", $pos);
				//IPS_LogMessage("Splitter Fehler ende pos:",substr($data,0,$pos));
			//}
			switch ($cmd)
			{
				case "statePP":
					//SetValue(37729, time());
					$this->WriteLog($data);
					$this->statePP($data);
					break;
				case "errors":
					//SetValue(37729, time());
					$this->errors($data);
					break;
				case "statePower":
					$this->WriteLog1($data);
					$this->statePower($data);
					break;
				case "stateHeatControl":
					$this->WriteLog4($data);
					$this->stateHeatControl($data);
					break;
				case "valuePP":
					$this->WriteLog3($data);
					$this->valuePP($data);
					break;
				default:
					IPS_LogMessage("Splitter CMD1", $cmd);
					IPS_LogMessage("Splitter data", $data);
					break;
			}
			if (date("H")<>0)
    			{
				$this->ZusatzHeizung();
			}
		}
		
		/**
     		* Interne Funktion des SDK.
     		*/
    		public function GetConfigurationForm()
    		{
        		$form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
		        //$ConfigVars = json_decode($this->ReadPropertyString('Variables'), true);
		        return json_encode($form);
    		}
				
		private function IPS_CreateKategorie($KategorieName, $ParentCat, $Position) 
		{
			$KategorieID = @IPS_GetCategoryIDByName($KategorieName, $ParentCat);
			if($KategorieID === false)
			{
				$NeueID=IPS_CreateCategory();
				ips_setname($NeueID, $KategorieName);
				if($ParentCat > 0)
				{
					ips_setparent($NeueID, $ParentCat);
				}
				if($Position > 0)
				{
					ips_setposition($NeueID,$Position);
				} 
				//IPS_LogMessage("neue Kategorie:", $NeueID .":" . $KategorieName);
				$ID =  $NeueID;
		    	}
			else
			{
				//IPS_LogMessage("alte Kategorie:", $KategorieID .":"  . $KategorieName);
				$ID =  $KategorieID;
			}
			return $ID;
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
