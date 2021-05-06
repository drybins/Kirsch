<?php
	class BHKWSP extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			
			$this->RegisterPropertyInteger('port', 7603);
			$this->RequireParent("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}");

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
			$BHKWIOID = $this->GetParentId();
			IPS_SetName ($BHKWIOID, "BHKWIO");
			IPS_SetIdent ($BHKWIOID, "BHKWIO");
			IPS_LogMessage("Parent SPID: ",$BHKWIOID);
			IPS_SetProperty($BHKWIOID, 'Host', "192.168.178.111");
			IPS_SetProperty($BHKWIOID, 'Port', "7603");
		}

		public function ForwardData($JSONString)
		{
			$data = json_decode($JSONString);
			//IPS_LogMessage("Splitter FRWD", utf8_decode($data->Buffer));

			$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => $data->Buffer)));

			return "String data for device instance!";
		}

		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			//IPS_LogMessage("Splitter RECV", utf8_decode($data->Buffer));
					//Kontrollieren ob Buffer leer ist.
			$bufferData = $this->GetBuffer("DataBuffer");
			$bufferData .= $data->Buffer;
			//IPS_LogMessage("Splitter bufferData", $bufferData);
			
			$delimeter = '<?xml version="1.0" encoding="UTF-8"?>';
			$bufferParts = explode($delimeter, $bufferData);	
			//$bufferParts = explode("\r\n", $bufferData);

		//Letzten Eintrag nicht auswerten, da dieser nicht vollständig ist.
		if(sizeof($bufferParts) > 1) 
			{
				for($i=0; $i<sizeof($bufferParts)-1; $i++) 
				{
					//IPS_LogMessage("Splitter bufferParts", $i . ":" . $bufferParts[$i]);
					//$this->SendDebug("Data", $bufferParts[$i], 0);
					//$this->AnalyseData($delimeter . $bufferParts[$i]);
					$this->send2BHKW($delimeter . $bufferParts[$i]);
				}
			}
			$bufferData = $bufferParts[sizeof($bufferParts)-1];

			//Übriggebliebene Daten auf den Buffer schreiben
			$this->SetBuffer("DataBuffer", $bufferData);
			//$this->DRTEST(utf8_decode($data->Buffer));
			//$this->SendDataToChildren(json_encode(Array("DataID" => "{185A67F4-5748-3EE1-4EED-CAF56975F21B}", "Buffer" => $data->Buffer)));
		}
			
		public function send2BHKW(string $cmd)
		{
			//IPS_LogMessage("Splitter statePP", $cmd);
			
			//$xmlData = @new SimpleXMLElement(utf8_encode($cmd), LIBXML_NOBLANKS + LIBXML_NONET);
			//$SatusID = $this->GetIDForIdent("KirschStatus");
			//IPS_LogMessage("Splitter statePP StatusID", $StatusID);
			//$ScriptData['STATUS'] = (string) $xmlData->common[0]->state;
			//$data = $cmd;
			$this->SendDataToChildren(json_encode(Array("DataID" => "{185A67F4-5748-3EE1-4EED-CAF56975F21B}", "Buffer" => utf8_encode($cmd))));
		}
		
		
    /**
     * register parent instance
     */
    protected function RegisterParent()
    {
        $OldParentId = $this->GetParentId();
        $ParentId = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if ($ParentId <> $OldParentId) {
            if ($OldParentId > 0) {
                $this->UnregisterMessage($OldParentId, IM_CHANGESTATUS);
                $this->UnregisterMessage($OldParentId, IM_DISCONNECT);

                if ((float)IPS_GetKernelVersion() < 4.2) {
                    $this->RegisterMessage($OldParentId, IPS_KERNELMESSAGE);
                } else {
                    $this->RegisterMessage($OldParentId, IPS_KERNELSTARTED);
                    $this->RegisterMessage($OldParentId, IPS_KERNELSHUTDOWN);
                }
            }
        }

        if ($ParentId > 0) {
            $this->RegisterMessage($ParentId, IM_CHANGESTATUS);
            $this->UnregisterMessage($ParentId, IM_DISCONNECT);

            if ((float)IPS_GetKernelVersion() < 4.2) {
                $this->RegisterMessage($ParentId, IPS_KERNELMESSAGE);
            } else {
                $this->RegisterMessage($ParentId, IPS_KERNELSTARTED);
                $this->RegisterMessage($ParentId, IPS_KERNELSHUTDOWN);
            }
        } else {
            $ParentId = 0;
        }

        return $ParentId;
    }

    /**
     * check, if module has an active parent
     * @return bool
     */
    protected function HasActiveParent()
    {
        if ($ParentID = $this->GetParentId()) {
            $parent = IPS_GetInstance($ParentID);
            if ($parent['InstanceStatus'] == 102) {
                return true;
            }
        }

        return false;
    }
		
		/**
     		* get connected parent instance id
     		* @return mixed
     		*/
    		protected function GetParentId()
    		{
        		$instance = @IPS_GetInstance($this->InstanceID);
        		return $instance['ConnectionID'];
    		}		
	}
