<?php
class version
{
	private $curver='indev-013';
	
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