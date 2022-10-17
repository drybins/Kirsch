<?php
// include autoloader
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/libs/BHKW_Functions.php');
define('__ROOT1__', dirname(dirname(__FILE__)));
require_once(__ROOT1__ . '/libs/BHKW_Zusatzheizung.php');

define('__ROOT2__', dirname(dirname(__FILE__)));
require_once(__ROOT2__ . '/libs/BHKW_statePP.php');
//require_once __DIR__ . '../libs/BHKW_Zusatzheizung.php';
//const TempDiff =40;
//const VorlaufSoll20 = 45;
//const VorlaufSollminus20 = 70;
if (!defined('TempDiff')) {
    define('TempDiff', '40');
}
if (!defined('VorlaufSoll20')) {
    define('VorlaufSoll20', '20');
}
if (!defined('VorlaufSollminus20')) {
    define('VorlaufSollminus20', '70');
}

	class BHKW extends IPSModule {
	
		use BHKWFunctions, BHKWZusatzHeizung, BHKWstatePP;
		
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
			/*
			$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
			$ID = IPS_GetInstanceListByModuleID($GUID)[0];
			IPS_LogMessage("BHKW ID1", $ID);
			$statePPID = IPS_GetCategoryIDByName ("statePP", $ID);
			//if(!IPS_GetCategoryIDByName ("statePP", $ID))
			//{
				IPS_LogMessage("statePP schon da!", $statePPID);	
			//}
			*/
			
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
			/*
			$KategorieID = @IPS_GetCategoryIDByName("Visualisierung" , 0);
			if($KategorieID === false)
			{ 
				$ViscatID=IPS_CreateCategory();
				ips_setname($ViscatID, "Visualisierung");
			
				$BHKWID=IPS_CreateCategory();
				ips_setname($BHKWID, "BHKW");
				ips_setparent($BHKWID, $ViscatID) ;    
				
				$uebersichtID=IPS_CreateCategory();
				ips_setname($uebersichtID, "Überblick");
				ips_setparent($uebersichtID, $BHKWID);
				ips_setposition($uebersichtID,10);
				
				$StatusID=IPS_CreateCategory();
				ips_setname($StatusID, "Statusdaten");
				ips_setparent($StatusID, $BHKWID) ; 
				ips_setposition($StatusID,20);
				
					$AllesID=IPS_CreateCategory();
					ips_setname($AllesID, "Alles");
					ips_setparent($AllesID, $StatusID) ; 
					ips_setposition($AllesID,10);
									
					$BHKSSID=IPS_CreateCategory();
					ips_setname($BHKSSID, "BHKW");
					ips_setparent($BHKSSID, $StatusID) ; 
					ips_setposition($BHKSSID,20);
				
				

					$BetriebsdatenID=IPS_CreateCategory();
					ips_setname($BetriebsdatenID, "Betriebsdaten");
					ips_setparent($BetriebsdatenID, $StatusID) ; 
					ips_setposition($BetriebsdatenID,30);
					
					$HeizungID=IPS_CreateCategory();
					ips_setname($HeizungID, "Heizung");
					ips_setparent($HeizungID, $StatusID) ; 
					ips_setposition($HeizungID,40);
				
						$Link1ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link1ID, "Außentemperatur" ); // Link benennen
						IPS_SetParent($Link1ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link1ID, $this->GetIDForIdent("T1"));    // Link verknüpfen
						ips_setposition($Link1ID,10);
						
						$Link2ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link2ID, "Speichertemperatur oben" ); // Link benennen
						IPS_SetParent($Link2ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link2ID, $this->GetIDForIdent("T2"));    // Link verknüpfen
						ips_setposition($Link2ID,20);
								
						$Link3ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link3ID, "Speichertemperatur mitte" ); // Link benennen
						IPS_SetParent($Link3ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link3ID, $this->GetIDForIdent("T3"));    // Link verknüpfen
						ips_setposition($Link3ID,30);
								
						$Link4ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link4ID, "Speichertemperatur unten" ); // Link benennen
						IPS_SetParent($Link4ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link4ID, $this->GetIDForIdent("T4"));    // Link verknüpfen
						ips_setposition($Link4ID,40);
						
						$Link5ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link5ID, "VorlaufTemperatur Soll" ); // Link benennen
						IPS_SetParent($Link5ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link5ID, $this->GetIDForIdent("VorlaufTemperaturSoll"));    // Link verknüpfen
						ips_setposition($Link5ID,50);
				
						$Link6ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link6ID, "Vorlauf Heizkreis 1" ); // Link benennen
						IPS_SetParent($Link6ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link6ID, $this->GetIDForIdent("T5"));    // Link verknüpfen
						ips_setposition($Link6ID,60);
						
						$Link7ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link7ID, "Rüklauf Heizkreis 1" ); // Link benennen
						IPS_SetParent($Link7ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link7ID, $this->GetIDForIdent("T6"));    // Link verknüpfen
						ips_setposition($Link7ID,70);
				
						$Link8ID = IPS_CreateLink();             // Link anlegen
						IPS_SetName($Link8ID, "Pumpe Heizkreis 1" ); // Link benennen
						IPS_SetParent($Link8ID, $HeizungID); // Link einsortieren unter dem Objekt mit der ID "12345"
						IPS_SetLinkTargetID($Link8ID, $this->GetIDForIdent("R1"));    // Link verknüpfen
						ips_setposition($Link8ID,80);
				
					$InbetribnahmeID=IPS_CreateCategory();
					ips_setname($InbetribnahmeID, "Inbetriebnahme");
					ips_setparent($InbetribnahmeID, $StatusID) ; 
					ips_setposition($InbetribnahmeID,50);
				
				$FehlerID=IPS_CreateCategory();
				ips_setname($FehlerID, "Fehlermeldungen");
				ips_setparent($FehlerID, $BHKWID) ;
				ips_setposition($FehlerID,30);
				
				$GuiID=IPS_CreateCategory();
				ips_setname($GuiID, "GUI");
				ips_setparent($GuiID, $BHKWID) ; 
				ips_setposition($GuiID,40);
				
				$TestID=IPS_CreateCategory();
				ips_setname($TestID, "Test");
				ips_setparent($TestID, $BHKWID) ; 
				ips_setposition($TestID,50);
				
				$KonfigID=IPS_CreateCategory();
				ips_setname($KonfigID, "Konfiguration");
				ips_setparent($KonfigID, $BHKWID) ; 
				ips_setposition($KonfigID,60);				
				
				$KalibrierungID=IPS_CreateCategory();
				ips_setname($KalibrierungID, "Kaliebrierung");
				ips_setparent($KalibrierungID, $BHKWID) ; 
				ips_setposition($KalibrierungID,70);
			}
			*/
			//$instance = IPS_GetCategoryIDByName ("Kirsch BHKW", 0)
			//IPS_LogMessage("BHKW ID", $instance);
			//$this->RegisterVariableInteger("LPR", "Letztes Paket empfangen", " ~UnixTimestamp", 900);
			//IPS_SetParent($this->GetIDForIdent('LPR'),$CatID);
			
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
			$delimeter = "<?xml version='1.0' encoding='UTF-8'?>";
			$pos = strrpos($data, $delimeter);
			if($pos === true)
			{
				$data = substr($data,0,$pos);
				IPS_LogMessage("Fehler Pos:", $pos);
				IPS_LogMessage("Fehler ende pos:",substr($data,$pos));
			}
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
			if (date("H")<>0)
    			{
				$this->ZusatzHeizung();
			}
		}
		

		
		private function errors($data)
		{
			//$arr = [];
			$Datumalt =GetValue($this->GetIDForIdent("DLF"));
			$array = json_decode(json_encode(simplexml_load_string($data)),true);
			if(!empty($array))
			{
				//usort($array, function($a, $b)
				//{
				//	return new DateTime($a['datetime']) <=> new DateTime(b['datetime']);
				//});
				//print_r ($array);
				//$sortarray = array();["error"]
				//array_multisort( array_column( $array, 'date' ),SORT_DESC);
				$i=0;
				foreach($array['error'] as $elem)
				{
					$Datum = strtotime($elem['date'] . $elem['time']);
					if($Datumalt == 0)
					{
						$Datumalt = $Datum;
					}
					if($Datum > $Datumalt)
					{
						SetValue($this->GetIDForIdent("DLF"),$Datum);
						$Datumalt = $Datum;
						IPS_LogMessage("BHKW Fehler datum:", $elem['date']);
						IPS_LogMessage("BHKW Fehler Time:", $elem['time']);
					}
				}
			}
			IPS_LogMessage("BHKW errors:", $data);
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
				IPS_LogMessage("neue Kategorie:", $NeueID .":" . $KategorieName);
				$ID =  $NeueID;
		    	}
			else
			{
				IPS_LogMessage("alte Kategorie:", $KategorieID .":"  . $KategorieName);
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
