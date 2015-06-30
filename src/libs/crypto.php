<?php
require_once('includes/config.php');

class crypto
{
	function __construct()
	{
		$db=new mysqli(MYSQL_HOST,MYSQL_USER,MYSQL_PWD,MYSQL_DB);
		
	}
	
	private function cleanup_filename($filename)
	{
		return preg_replace(“/[^a-z0-9\.]/”, “”, strtolower($filename));
	}
	
	private function create_container($pwd,$cont_name)
	{
		$cont_path=VPN_CONF_CONTAINER_DIR+$this->cleanup_filename($cont_name);
		$runcmd='/usr/bin/truecrypt --non-interactive --encryption=AES --hash=SHA-512 --filesystem=EXT4 --password=asdf -c '.$cont_path.' --size=100000000';
	}
	
	private function mount_container($pwd,$cont_name)
	{
		//
	}
}
?>
