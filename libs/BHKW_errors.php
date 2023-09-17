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
					IPS_LogMessage("BHKW Fehler datum:", $elem['date']);
					IPS_LogMessage("BHKW Fehler Time:", $elem['time']);
					
				}
			}
		}
		IPS_LogMessage("BHKW errors:", $data);
	}
}
