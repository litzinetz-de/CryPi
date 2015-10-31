<?php
class version
{
	private $curver='indev-011';
	
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