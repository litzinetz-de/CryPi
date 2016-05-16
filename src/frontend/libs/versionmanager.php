<?php
class version
{
	private $curver='0.0.1-alpha';
	
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
