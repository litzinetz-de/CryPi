<?php
class version
{
	private $curver='indev-021';
	
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