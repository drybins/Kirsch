<?php
class Krupp extends IPSModule 
{
    public function Create()
	{
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation 
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyInteger("AN_Aus_Schalter", 0);
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
}
?>