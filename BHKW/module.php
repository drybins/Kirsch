<?php
// include autoloader
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/libs/BHKW_Functions.php');
require_once(__ROOT__ . '/libs/BHKW_Zusatzheizung.php');
require_once(__ROOT__ . '/libs/BHKW_statePP.php');
require_once(__ROOT__ . '/libs/BHKW_statePower.php');
require_once(__ROOT__ . '/libs/BHKW_stateHeatControl.php');
require_once(__ROOT__ . '/libs/BHKW_errors.php');
require_once(__ROOT__ . '/libs/BHKW_valuePP.php');

require_once(__ROOT__ . '/libs/BHKW_stateExternal.php');
require_once(__ROOT__ . '/libs/BHKW_CreateProfile.php');
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
	
		use BHKWFunctions, BHKWZusatzHeizung, BHKWstatePP, BHKWstatePower, BHKWstateHeatControl, BHKWerrors, BHKWvaluePP, BHKWstateExternal, BHKWCreateProfile;
		
		public function Create()
		{
			//global $WWID;
			//Never delete this line!
			parent::Create();
			$this->RegisterPropertyInteger("CounterID", 0);
			$this->BHKWCreateProfile();
			// lese GUID des BHKW
			$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
			// 
			$ID = IPS_GetInstanceListByModuleID($GUID)[0];
			//IPS_LogMessage("BHKW ID1", $ID);
			$HKID = @IPS_GetCategoryIDByName("Heizkreislauf 1", $ID);
			if($HKID === false)
			{
				IPS_LogMessage("Heizkreislauf nicht da!", "dierk1");	

				$HKID=IPS_CreateCategory();
				ips_setname($HKID, "Heizkreislauf 1");
				IPS_SetParent($HKID, $ID);
				
				$this->RegisterVariableInteger("APreset", "aktive Voreinstellungen", "", 10);
				$TPID = $this->GetIDForIdent("APreset");
				IPS_SetParent($TPID, $HKID);
				
				$this->RegisterVariableInteger("TKFlowMin", "Vorlauf bei 20°C", "Kirsch.GradC", 20);
				$TFnID = $this->GetIDForIdent("TKFlowMin");
				IPS_SetParent($TFnID, $HKID);

				$this->RegisterVariableInteger("TKFlowMax", "Vorlauf bei -20°C", "Kirsch.GradC", 30);
				$TFxID = $this->GetIDForIdent("TKFlowMax");
				IPS_SetParent($TFxID, $HKID);
			}
				
			if(@IPS_GetCategoryIDByName("Nachtabsenkung", $HKID) === false)
			{
				$HSID=IPS_CreateCategory();
				ips_setname($HSID, "Nachtabsenkung");
				IPS_SetParent($HSID, $HKID);
		
				$this->RegisterVariableInteger("NAStatus", "Status", "Kirsch.PGMStatus", 10);
				$NASID = $this->GetIDForIdent("NAStatus");
				IPS_SetParent($NASID, $HSID);
				//$NASID2 = $this->GetIDForIdent("NAStatus");
			
				$this->RegisterVariableInteger("StartZeit", "Start Zeit", "~UnixTimestampTime", 20);
				$SZID = $this->GetIDForIdent("StartZeit");
				IPS_SetParent($SZID, $HSID);
				
				$this->RegisterVariableInteger("EndeZeit", "Ende Zeit", "~UnixTimestampTime", 30);
				$EZID = $this->GetIDForIdent("EndeZeit");
				IPS_SetParent($EZID, $HSID);
			
				$this->RegisterVariableInteger("TFall", "Absenken um °Celsius", "Kirsch.GradC", 40);
				$TFID = $this->GetIDForIdent("TFall");
				IPS_SetParent($TFID, $HSID);
				$this->RegisterVariableInteger("TMin", "nicht absenken bei unter °Celsius", "Kirsch.GradC", 50);
				$TMID = $this->GetIDForIdent("TMin");
				IPS_SetParent($TMID, $HSID);
			}

			if(@IPS_GetCategoryIDByName("Urlaubsmodus", $HKID) === false)
			{
				$HUID=IPS_CreateCategory();
				ips_setname($HUID, "Urlaubsmodus");
				IPS_SetParent($HUID, $HKID);
				
				$this->RegisterVariableInteger("UStatus", "Status", "Kirsch.PGMStatus", 10);
				$USID = $this->GetIDForIdent("UStatus");
				IPS_SetParent($USID, $HUID);
				//$NASID2 = $this->GetIDForIdent("NAStatus");
			
				$this->RegisterVariableInteger("StartZeit", "Start Zeit", "~UnixTimestampDate", 20);
				$SZID = $this->GetIDForIdent("StartZeit");
				IPS_SetParent($SZID, $HUID);
			
				$this->RegisterVariableInteger("EndeZeit", "Ende Zeit", "~UnixTimestampDate", 30);
				$EZID = $this->GetIDForIdent("EndeZeit");
				IPS_SetParent($EZID, $HUID);
			
				$this->RegisterVariableInteger("TTarget", "Absenktemperatur", "Kirsch.GradC", 40);
				$TFID = $this->GetIDForIdent("TTarget");
				IPS_SetParent($TFID, $HUID);
			}
			
			if(@IPS_GetCategoryIDByName("Sommer", $HKID) === false)
			{
				$SID=IPS_CreateCategory();
				ips_setname($SID, "Sommer");
				IPS_SetParent($SID, $HKID);

				$this->RegisterVariableInteger("SStatus", "Status", "Kirsch.PGMStatus", 10);
				$SSID = $this->GetIDForIdent("SStatus");
				IPS_SetParent($SSID, $SID);
				
				$this->RegisterVariableInteger("TOutdoor", "Abschalten bei Außentemperatur über", "Kirsch.GradC", 20);
				$TOID = $this->GetIDForIdent("TOutdoor");
				IPS_SetParent($TOID, $SID);
			}

			if(@IPS_GetCategoryIDByName("Heißwasser", $HKID) === false)
			{
				$HWID=IPS_CreateCategory();
				ips_setname($HWID, "Heißwasser");
				IPS_SetParent($HWID, $HKID);
				
				$this->RegisterVariableInteger("WWStatus", "Status", "Kirsch.PGMStatus", 10);
				$WWID = $this->GetIDForIdent("WWStatus");
				IPS_SetParent($WWID, $HWID);
				//$NASID2 = $this->GetIDForIdent("NAStatus");
			
				$this->RegisterVariableInteger("WWStartZeit", "Start Zeit", "~UnixTimestampTime", 20);
				$WWSID = $this->GetIDForIdent("WWStartZeit");
				IPS_SetParent($WWSID, $HWID);
			
				$this->RegisterVariableInteger("WWEndeZeit", "Ende Zeit", "~UnixTimestampTime", 30);
				$WWEID = $this->GetIDForIdent("WWEndeZeit");
				IPS_SetParent($WWEID, $HWID);
			
				$this->RegisterVariableInteger("WWTTarget", "warm Wasser Temperatur", "Kirsch.GradC", 40);
				$WWTID = $this->GetIDForIdent("WWTTarget");
				IPS_SetParent($WWTID, $HWID);
			}
			
			$HWID = @IPS_GetCategoryIDByName("Hardware", $ID);
			if($HKID === false)
			{
				IPS_LogMessage("Hardware nicht da!", "dierk1");	

				$HWID=IPS_CreateCategory();
				ips_setname($HWID, "Hardware");
				IPS_SetParent($HWID, $ID);
				
				$this->RegisterVariableInteger("IP", "IP ADDR", "", 10);
				$IPID = $this->GetIDForIdent("IP");
				IPS_SetParent($IPID, $HWID);
				
				$this->RegisterVariableString("Geraetename", "Geräte Name", "", 20);
				$GNID = $this->GetIDForIdent("Geraetename");
				IPS_SetParent($GNID, $HWID);

				$this->RegisterVariableInteger("GeraeterSerial", "Geräte Seriennummer", "", 30);
				$GSID = $this->GetIDForIdent("GeraeterSerial");
				IPS_SetParent($GSID, $HWID);

				$this->RegisterVariableInteger("GeraeterDT", "Geräte Device Type", "", 40);
				$GDTPID = $this->GetIDForIdent("GeraeterDT");
				IPS_SetParent($GDTPID, $HWID);		
				
				$this->RegisterVariableString("GeraeteFirmware", "Geräte NFirmware", "", 50);
				$GFWID = $this->GetIDForIdent("Geraetename");
				IPS_SetParent($GFWID, $HWID);				
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
			$this->RegisterVariableBoolean("R2", "Mischer auf", "Kirsch.AnAus", 533);
			$this->RegisterVariableBoolean("R3", "Mischer zu", "Kirsch.AnAus", 534);
			
			$this->RegisterVariableFloat("VorlaufTemperaturSoll", "Vorlauf Temperatur soll", "~Temperature", 535);
			$this->RegisterVariableFloat("T5", "Vorlauf Heizkreis 1", "~Temperature", 538);
			$this->RegisterVariableFloat("T6", "Rücklauf Heizkreis 1", "~Temperature", 540);
			$this->RegisterVariableBoolean("R4", "ZusatzHeizung", "Kirsch.AnAus", 542);
			$this->RegisterVariableBoolean("R5", "externes Fehlersignal", "Kirsch.AnAus", 543);
			$this->RegisterVariableBoolean("BHKWFehler", "BHKW Fehler", "Kirsch.AnAus", 544);
			
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
			global $WWID;
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
					//$this->WriteLog($data);
					$this->statePP($data);
					break;
				case "errors":
					//SetValue(37729, time());
					$this->errors($data);
					break;
				case "statePower":
					//$this->WriteLog1($data);
					$this->statePower($data);
					break;
				case "stateHeatControl":
					//$this->WriteLog4($data);
					$this->stateHeatControl($data);
					break;
				case "valuePP":
					//$this->WriteLog3($data);
					$this->valuePP($data);
					break;
				case "stateExternal":
					//$this->WriteLog10($data);
					$this->stateExternal($data);
					break;
				default:
					//IPS_LogMessage("Splitter CMD1", $cmd);
					//IPS_LogMessage("Splitter data", $data);
					break;
			}
			if (date("H")>1)
    		{
				//$CatID = @IPS_GetCategoryIDByName("Heißwasser", $this);
				//if ($CatID === false) 
				//	echo "Category not found!";
				//else
				//	echo "The Category ID is: ". $CatID;
				//IPS_LogMessage("zHeizung", $WWID);
				//$Heißwasser = GetValue($this->GetIDForIdent("WWTTarget"));
				//$WarmwasserStart = GetValue($this->GetIDForIdent("WWStartZeit"));
				//$WarmwasserEnde = GetValue($this->GetIDForIdent("WWEndeZeit"));
				$this->ZusatzHeizung();
			}
		}
		
		/**
     		* Interne Funktion des SDK.
     		
    		public function GetConfigurationForm()
    		{
        		$form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);
		        //$ConfigVars = json_decode($this->ReadPropertyString('Variables'), true);
		        return json_encode($form);
    		}
			*/
				
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
		
    	protected function GetParentId()
    	{
        	$instance = @IPS_GetInstance($this->InstanceID);
        	return $instance['ConnectionID'];
    	}
	}
