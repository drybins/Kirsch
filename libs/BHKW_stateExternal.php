<?php

declare(strict_types=1);

trait BHKWstateExternal
{
	private function WriteLog10($data)
	{
		IPS_LogMessage("BHKW stateExternal TestLog", $data);
	}
	
	private function stateExternal($data)
	{
		try
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			//echo "Alles ok!";
		}
 			catch(Exception $ex)
		{
			//print_r($ex);
			IPS_LogMessage("BHKW stateExternal Fehler", $data);
		}
		
		$ScriptData['R1'] = (string) $xmlData->R1;
		switch ($ScriptData['R1']) 
		{
			case "on":
				SetValueBoolean($this->GetIDForIdent("R1"), true);
			break;
			case "off":
				SetValueBoolean ($this->GetIDForIdent("R1"), false);
			break;
			default:
		}	

		$ScriptData['R2'] = (string) $xmlData->R2;
		switch ($ScriptData['R2']) 
		{
			case "on":
				$R2 = GetValue($this->GetIDForIdent("R2"));
				SetValueBoolean($this->GetIDForIdent("R2"), true);
				if(!$R2)
				{
					$Mischer = GetValue($this->GetIDForIdent("Mischer"));
					$Mischer = $Mischer + 1;
					SetValueInteger($this->GetIDForIdent("Mischer"), $Mischer);
				}
			break;
			case "off":
				SetValueBoolean ($this->GetIDForIdent("R2"), false);
			break;
			default:
		}

		$ScriptData['R3'] = (string) $xmlData->R3;
		switch ($ScriptData['R3']) 
		{
			case "on":
				$R3 = GetValue($this->GetIDForIdent("R3"));
				SetValueBoolean($this->GetIDForIdent("R3"), true);
				if(!$R3)
				{
					$Mischer = GetValue($this->GetIDForIdent("Mischer"));
					$Mischer = $Mischer - 1;
					SetValueInteger($this->GetIDForIdent("Mischer"), $Mischer);
				}
			break;
			case "off":
				SetValueBoolean ($this->GetIDForIdent("R3"), false);
			break;
			default:
		}
//		IPS_LogMessage("BHKW stateExternal R4 Ident:", $this->GetIDForIdent("zH1"));
		$ScriptData['R4'] = (string) $xmlData->R4;
		switch ($ScriptData['R4']) 
		{
			case "on":
			{
				IPS_LogMessage("BHKW stateExternal R4 on:", $ScriptData['R4']);
				SetValueBoolean($this->GetIDForIdent("R4"), true);
			break;
			}
			case "off":
				IPS_LogMessage("BHKW stateExternal R4 off:", $ScriptData['R4']);
				SetValueBoolean ($this->GetIDForIdent("R4"), false);
			break;
			default:
		}
		
		$ScriptData['R5'] = (string) $xmlData->R5;
		switch ($ScriptData['R5']) 
		{
			case "on":
			{
				IPS_LogMessage("BHKW stateExternal R5 on:", $ScriptData['R5']);
				SetValueBoolean($this->GetIDForIdent("R5"), true);
			break;
			}
			case "off":
				IPS_LogMessage("BHKW stateExternal R5 off:", $ScriptData['R5']);
				SetValueBoolean ($this->GetIDForIdent("R5"), false);
			break;
			default:
		}

	}
}
