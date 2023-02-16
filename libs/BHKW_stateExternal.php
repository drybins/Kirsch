<?php

declare(strict_types=1);

trait BHKWstateExternal
{
	private function WriteLog1($data)
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
		
		$ScriptData['I1'] = (Float) $xmlData->I1;
		SetValue ($this->GetIDForIdent("I1") , $ScriptData['I1']);

	}
}
