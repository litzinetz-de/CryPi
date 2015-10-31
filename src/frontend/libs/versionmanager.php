<?php
class version
{
	private $curver='indev-008';
	
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