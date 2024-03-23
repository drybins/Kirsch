<?php

declare(strict_types=1);

trait BHKWInfo
{
	private function WriteLog11($data)
	{
		IPS_LogMessage("BHKW Info TestLog", $data);
	}
	
	private function Info($data)
	{
		try
		{
			$xmlData = @new SimpleXMLElement(utf8_encode($data), LIBXML_NOBLANKS + LIBXML_NONET);
			//echo "Alles ok!";
		}
 			catch(Exception $ex)
		{
			//print_r($ex);
			IPS_LogMessage("BHKW Info Fehler", $data);
		}
		
		$GUID = "{13D080B9-10DD-1AAD-4C21-B06937CDCA3C}";
		$ID = IPS_GetInstanceListByModuleID($GUID)[0];
		
		$KategorieID = @IPS_GetCategoryIDByName("Hardware", $ID);
		$ScriptData['IP'] = (string) $xmlData->ip;


	}
}
