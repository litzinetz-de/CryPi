<?php
require_once('includes/config.php');

class crypto
{
	function __construct()
	{
		//$db=new mysqli(MYSQL_HOST,MYSQL_USER,MYSQL_PWD,MYSQL_DB);
		
	}
	
	private function cleanup_filename($filename)
	{
		return preg_replace("/[^a-z0-9\.]/", "", strtolower($filename));
	}
	
	public function create_container($pwd,$cont_name)
	{
		$cont_path=VPN_CONF_ENC.$this->cleanup_filename($cont_name);
		//$cont_path=VPN_CONF_ENC+$this->cleanup_filename($cont_name);
		$runcmd=TC_BIN.' -t --size=200000000 --password="'.$pwd.'" -k "" --random-source=/dev/urandom --volume-type=normal --encryption=AES --hash=SHA-512 --filesystem=FAT -c '.$cont_path;
		
		exec($runcmd,$cmd_output,$return_var);
		
		if($return_var==0)
		{
			//echo $cont_path.'<br><br>';
			//print_r($cmd_output);
			return true;
		} else {
			// Handle error
			return false;
		}
	}
	
	public function mount_container($pwd,$cont_name)
	{
		$cont_path=VPN_CONF_ENC.$this->cleanup_filename($cont_name);
		$runcmd=TC_BIN.' --non-interactive '.$cont_path.' '.VPN_CONF_MNT.' -p "'.$pwd.'"';
		
		exec($runcmd,$cmd_output,$return_var);
		
		if($return_var==0)
		{
			return true;
		} else {
			// Handle error
			return false;
		}
	}
	
	public function dismount_container()
	{
		$runcmd=TC_BIN.' -d';
		
		exec($runcmd,$cmd_output,$return_var);
		
		if($return_var==0)
		{
			return true;
		} else {
			// Handle error
			return false;
		}
	}
}
?>
