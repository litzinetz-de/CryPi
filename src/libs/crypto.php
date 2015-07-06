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
		$runcmd='sudo '.TC_BIN.' --non-interactive '.$cont_path.' '.VPN_CONF_MNT.' -p "'.$pwd.'"';
		
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
	
	public function container_mounted()
	{
		$runcmd=TC_BIN.' -l';
		exec($runcmd,$cmd_output,$return_var);
		
		foreach($cmd_output as $line)
		{
			$pos = strpos($line, rtrim(VPN_CONF_MNT,'/'));
			if ($pos === false)
			{
				//
			} else {
				return true;
			}
		}
		return false;
	}
	
	private function ReadConfigs($dir)
	{
		$configlist=array();
		//array_push($configlist,'test1');
		$handle=opendir($dir);
		while($file=readdir($handle))
		{
			if($file!='.' && $file!='..')
			{
				$fullpath=$dir.'/'.$file;
				//echo 'Processing '.$fullpath."\n";
				if(is_dir($fullpath))
				{
					//echo "is dir\n";
					$configlist=array_merge($configlist,$this->ReadConfigs($fullpath));
				} else {
					//echo $fullpath." is file\n";
					if(preg_match('/.*\.ovpn/i',$file))
					{
						array_push($configlist,$fullpath);
					}
					//print_r($configlist);
				}
			}
		}
		//array_push($configlist,'test2');
		//print_r($configlist);
		//echo "end.\n\n";
		return $configlist;
	}
	
	public function FindConfigs()
	{
		if(!$this->container_mounted())
		{
			return false;
		}
		
		return $this->ReadConfigs(VPN_CONF_MNT);
	}
	
	public function AddCredentials($username,$password)
	{
		if(!$this->container_mounted())
		{
			return false;
		}
		file_put_contents(VPN_CONF_MNT.'cred.dat',$username."\n".$password) or die('Error writing cred.dat');
		
		// TODO: Read list of ovpn files into array $ovpn_list. For testing purposes, we make it static
		$ovpn_list=array('/crypi/mnt/testvpn/testvpn.ovpn');
		
		foreach($ovpn_list as $ovpn)
		{
			$fh=fopen($ovpn,'a');
			fwrite($fh,"auth-user-pass ".VPN_CONF_MNT."cred.dat");
			fclose($fh);
		}
	}
	
	public function VPNConnected()
	{
		exec('ip link show dev tun0 2> /dev/null',$cmd_output,$return_var);
		if($return_var==0)
		{
			return true;
		} else {
			return false;
		}
	}
	
	public function KillVPN()
	{
		exec('sudo '.PKILL_BIN.' openvpn');
	}
	
	public function StartVPN($configfile)
	{
		chdir(dirname($configfile));
		//$cmd='cd '.dirname($configfile).' && sudo '.OVPN_BIN.' '.$configfile.' 2> '.CRYPI_LOGFILE.' &';
		exec('sudo '.OVPN_BIN.' '.$configfile.' > /dev/null 2>&1 &');
		//exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, CRYPI_LOGFILE, OVPN_PIDFILE));
	}
}
?>
