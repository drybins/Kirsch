<?php

declare(strict_types=1);

trait BHKWCMD
{
	public function BHKW_Start()
	{
		CSCK_SendText(22145,"<?xml version=\"1.0\" encoding=\"UTF-8\"?><setPower>\r\n <automatic>on</automatic>\r\n <automatic>on</automatic></setPower>");
	}
	
	public function BHKW_Stop()
	{
		CSCK_SendText(22145,"<?xml version=\"1.0\" encoding=\"UTF-8\"?><setPower>\r\n <automatic>on</automatic>\r\n <automatic>off</automatic></setPower>");
	}
}
