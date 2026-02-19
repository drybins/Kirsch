<?php

declare(strict_types=1);

trait BHKWerrors
{
	private function WriteLog2($data)
	{
		IPS_LogMessage("BHKW errors TestLog", $data);
	}

	private function Perror($data)
	{
		SetValue($this->GetIDForIdent("date"),date("Y-m-d"));
		SetValue($this->GetIDForIdent("time"),date("h:i"));
		
		SetValue($this->GetIDForIdent("class"),"FF");
		SetValue($this->GetIDForIdent("device"),"01");
		SetValue($this->GetIDForIdent("type"),"10");
		SetValue($this->GetIDForIdent("occurrence"),"1");
		SetValue($this->GetIDForIdent("remoteConfirmed"),"0");
		SetValue($this->GetIDForIdent("level"),"System");
		SetValue($this->GetIDForIdent("state"),"unconfirmed"); 
		
		SetValue($this->GetIDForIdent("DLF"),$Datum);
		
		SetValue($this->GetIDForIdent("Software"),"IP Symcon");
		SetValue($this->GetIDForIdent("Messpunkt"),"Drosselklappensteuerung");
		SetValue($this->GetIDForIdent("Fehler"),"Drosselklappenstellung > 71%");
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
					$level = $elem['level'];
					$class = $elem['class'];
					$device = $elem['device'];
					$type = $elem['type'];
					$occurrence = $elem['occurrence'];
					
					SetValue($this->GetIDForIdent("DLF"),$Datum);
					SetValue($this->GetIDForIdent("class"),$elem['class']);
					SetValue($this->GetIDForIdent("device"),$elem['device']);
					
					SetValue($this->GetIDForIdent("type"),$elem['type']);
					SetValue($this->GetIDForIdent("occurrence"),$elem['occurrence']);
					SetValue($this->GetIDForIdent("remoteConfirmed"),$elem['remoteConfirmed']);
					SetValue($this->GetIDForIdent("level"),$elem['level']);
					SetValue($this->GetIDForIdent("state"),$elem['state']); 
					SetValue($this->GetIDForIdent("date"),$elem['date']);
					SetValue($this->GetIDForIdent("time"),$elem['time']);
					$Datumalt = $Datum;
					
					switch ($level) 
					{
						case "user":
						switch ($class) 
						{
							case "01":
								SetValue($this->GetIDForIdent("Software"),"Drosselklappensteuerung");
								break;
							case "02":
								SetValue($this->GetIDForIdent("Software"),"Motorsteuerung");
								switch ($device) 
								{
									case "04":
										SetValue($this->GetIDForIdent("Messpunkt"),"Motoröl (TI4)");
										switch ($type) 
										{
											case "0B":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Übertemperatur");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "0F":
										SetValue($this->GetIDForIdent("Messpunkt"),"Drehzahlmessung (S1)");
										switch ($type) 
										{
											case "09":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Generatordrehzahl zu gering");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "10":
										SetValue($this->GetIDForIdent("Messpunkt"),"Öldruckschalter (S4)");
										switch ($type) 
										{
											case "0A":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Kein Öldruck");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "18":
										SetValue($this->GetIDForIdent("Messpunkt"),"Leistungsüberwachung");
										switch ($type) 
										{
											case "10":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Rückleistung");
												break;
											case "33":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Phasenunsymertie");
												break;
											case "41":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Schleichender Leistungsabfall");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "1D":
										SetValue($this->GetIDForIdent("Messpunkt"),"Hauptschütz (C1)");
										switch ($type) 
										{
											case "01":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x nicht verbunden oder Kurzschluss zu Betriebsspannung");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "1E":
										SetValue($this->GetIDForIdent("Messpunkt"),"Sanftanlauf (SS)");
										switch ($type) 
										{
											case "01":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x nicht verbunden oder Kurzschluss zu Betriebsspannung");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									default:
										IPS_LogMessage("BHKW Fehler device:", $device);
								}
								break;
							case "04":
								SetValue($this->GetIDForIdent("Software"),"Netzüberwachung (ENS)");
								switch ($device) 
								{
									case "0B":
										SetValue($this->GetIDForIdent("Messpunkt"),"Energiemessung L1");
										switch ($type) 
										{																				
											case "2A":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Spannungsüberschreitung (200ms)");
												break;
											case "2B":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Frequenzeinbruch");
												break;
											case "2C":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Frequenzüberschreitung");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "0C":
										SetValue($this->GetIDForIdent("Messpunkt"),"Energiemessung L2");
										switch ($type) 
										{
											case "2A":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Spannungsüberschreitung (200ms)");
												break;
											case "2B":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Frequenzeinbruch");
												break;
											case "2C":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Frequenzüberschreitung");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "0D":
										SetValue($this->GetIDForIdent("Messpunkt"),"Energiemessung L3");
										switch ($type) 
										{
											case "2A":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Spannungsüberschreitung (200ms)");
												break;
											case "2B":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Frequenzeinbruch");
												break;
											case "2C":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "Frequenzüberschreitung");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									default:
										IPS_LogMessage("BHKW Fehler device:", $device);
								}
								break;
							case "05":
								SetValue($this->GetIDForIdent("Software"),"Sensorkontrolle");
								break;
							case "06":
								SetValue($this->GetIDForIdent("Software"),"Sicherheitskette");
								switch ($device) 
								{
									case "10":
										SetValue($this->GetIDForIdent("Messpunkt"),"Öldruckschalter (S4)");
										switch ($type) 
										{
											case "0A":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Kein Öldruck");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
									case "15":
										SetValue($this->GetIDForIdent("Messpunkt"),"Leckagesensor (S7)");
										switch ($type) 
										{
											case "0D":
												SetValue($this->GetIDForIdent("Fehler"),$occurrence . "x Leckage");
												break;
											default:
												IPS_LogMessage("BHKW Fehler type:", $type);
										}
										break;
								}
								break;
							default:
								IPS_LogMessage("BHKW Fehler class:", $class);
						}
						break;
					default:
						IPS_LogMessage("BHKW  level:", $level);
					}
					
					IPS_LogMessage("BHKW Fehler datum:", $elem['date']);
					IPS_LogMessage("BHKW Fehler Time:", $elem['time']);
					
				}
			}
		}
		IPS_LogMessage("BHKW Fehler:", $data);
	}
}
