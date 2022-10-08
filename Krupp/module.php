<?php
class EseraGaszaehler extends IPSModule 
{
    public function Create()
	{
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation 
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyInteger("AN_Aus_Schalter", 0);
	}
}
?>