<?php
class version
{
	private $curver='indev-016';
	
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