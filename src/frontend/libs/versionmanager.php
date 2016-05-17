<?php
class version
{
	private $curver='0.1.0-alpha';
	
	function __construct()
	{
		//
	}
	
	public function GetCurrentVersion()
	{
		return $this->curver;
	}
}
?>
