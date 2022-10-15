<?php
class KRUPP extends IPSModule 
{
    public function Create()
	{
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation 
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyInteger("Hauptschalter", 0);
		$this->RegisterPropertyInteger("Brenner_Schalter", 0);
		$this->RegisterPropertyInteger("Pumpen_Schalter", 0);
		
		$this->RegisterVariableBoolean("Hauptschalter", "Hauptschalter", "Kirsch.AnAus", 0);
		$this->RegisterVariableBoolean("Automatik", "Automatik", "Kirsch.AnAus", 10);
		$this->RegisterVariableBoolean("Brenner", "Brenner", "Kirsch.AnAus", 20);
		$this->RegisterVariableBoolean("Punmpe", "Pumpe", "Kirsch.AnAus", 30);
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
	
	public function MainPower()
	{
		//schaltet die Heizung An/Aus
	 //echo "dierk";
	}
	
	public function Brenner()
	{
		//schaltet die Brennner An/Aus
	
	}
	
	public function Pumpe()
	{
		//schaltet die Pumpe An/Aus
	
	}
	
		public function Automatik()
	{
		//schaltet die Automatic An/Aus
	
	}
}
?>