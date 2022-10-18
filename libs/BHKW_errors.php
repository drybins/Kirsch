<?php

declare(strict_types=1);

trait BHKWerrors
{
	private function WriteLog2($data)
	{
		IPS_LogMessage("BHKW errors TestLog", $data);
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
}
