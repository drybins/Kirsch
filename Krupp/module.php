<?php
class KRUPP extends IPSModule 
{
    public function Create()
	{
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation 
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyInteger("AN_Aus_Schalter", 0);
		
		$this->RegisterVariableBoolean("Automatik", "Automatik", "Kirsch.AnAus", 10);
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
	
	public function Power()
	{
		//schaltet die Heizung An/Aus
	
	}
	
	public function Brenner()
	{
		//schaltet die Brennner An/Aus
	
	}
	
	public function Pumpe(bool state)
	{
		//schaltet die Pumpe An/Aus
	
	}
	
		public function Automatik(bool state)
	{
		//schaltet die Automatic An/Aus
	
	}
}
?>