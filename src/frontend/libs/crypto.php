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
		$filename=preg_replace("/[^a-z0-9\.]/", "", strtolower($filename));
		$d1 = array("ä" , "ö", "ü", "ß", "Ä", "Ö", "Ü");
		$d2 = array("ae" , "oe", "ue", "ss", "Ae", "Oe", "Ue");
		$filename=str_replace($d1, $d2, $filename);
		return $filename;
	}
	
	public function ReadContainers()
	{
		$containerlist=array();
		$handle=opendir(VPN_CONF_ENC);
		while($file=readdir($handle))
		{
			if($file!='.' && $file!='..')
			{
				$fullpath=$dir.'/'.$file;
				if(!is_dir($fullpath))
				{
					if(preg_match('/.*\.crypi/i',$file))
					{
						array_push($containerlist,$file);
					}
				}
			}
		}
		return $containerlist;
	}
	
	public function create_container($pwd,$cont_name)
	{
		$cont_path=VPN_CONF_ENC.$this->cleanup_filename($cont_name).'.crypi';
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
		if($this->VPNConnected()) $this->KillVPN();
		if($this->container_mounted()) $this->dismount_container();
		
		$cont_path=VPN_CONF_ENC.$this->cleanup_filename($cont_name);
		$runcmd='sudo '.TC_BIN.' --non-interactive '.$cont_path.' '.VPN_CONF_MNT.' -p "'.$pwd.'"';
		
		exec($runcmd,$cmd_output,$return_var);
		
		if($return_var==0)
		{
			return true;
		} else {
			// Handle error
			/*echo 'debug information:<br><pre>';
			print_r($cmd_output);
			echo '</pre>';*/
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
				$fullpath=$dir.$file;
				if(is_dir($fullpath))
				{
					$configlist=array_merge($configlist,$this->ReadConfigs($fullpath));
				} else {
					if(preg_match('/.*\.ovpn/i',$file))
					{
						array_push($configlist,$fullpath);
					}
				}
			}
		}
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
	
	public function RemoveMntPath($path)
	{
		return str_replace(VPN_CONF_MNT,'',$path);
	}
	
	public function AddCredentials($username,$password)
	{
		if(!$this->container_mounted())
		{
			return false;
		}
		file_put_contents(VPN_CONF_MNT.'cred.dat',$username."\n".$password) or die('Error writing cred.dat');
		
		if(!$ovpn_list=$this->FindConfigs())
		{
			die('Could not read config list. Maybe no container mounted?');
		}
		
		foreach($ovpn_list as $ovpn)
		{
			$fh=fopen($ovpn,'a');
			fwrite($fh,"auth-user-pass ".VPN_CONF_MNT."cred.dat");
			fclose($fh);
		}
	}
	
	public function CredentialsAvailable()
	{
		return file_exists(VPN_CONF_MNT.'cred.dat');
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
	
	public function SetNetworkSettings($addr,$mask,$gateway)
	{
		if(!filter_var($addr, FILTER_VALIDATE_IP) || !filter_var($mask, FILTER_VALIDATE_IP) || !filter_var($gateway, FILTER_VALIDATE_IP))
		{
			//echo 'NOVAL-'.$addr.'-'.$mask.'-'.$gateway;
			return false;
		}
		if(!@file_put_contents(DATAPATH.'networking_addr.dat',$addr)) return false;
		if(!@file_put_contents(DATAPATH.'networking_mask.dat',$mask)) return false;
		if(!@file_put_contents(DATAPATH.'networking_gateway.dat',$gateway)) return false;
		
		exec('sudo /sbin/ifconfig eth0 '.$addr.' netmask '.$mask,$cmd_output,$return_var);
		exec('sudo /sbin/route add default gw '.$gateway,$cmd_output2,$return_var2);
		
		if($return_var==0 && $return_var2==0)
		{
			//echo 'OK';
			return true;
		} else {
			//print_r($cmd_output);
			//print_r($cmd_output2);
			//echo 'ERROR';
			return false;
		}
	}
	
	public function GetNetworkSettings()
	{
		$addr=file_get_contents(DATAPATH.'networking_addr.dat');
		$mask=file_get_contents(DATAPATH.'networking_mask.dat');
		$gateway=file_get_contents(DATAPATH.'networking_gateway.dat');
		
		$buffer=array('addr' => $addr,'mask' => $mask,'gateway' => $gateway);
		return $buffer;
	}
	
	public function HandleVPNUpload()
	{
		$vpn_filepath=UPLOAD_WORKDIR.$_FILES['vpn_file']['name'];
		if(!move_uploaded_file($_FILES['vpn_file']['tmp_name'], $vpn_filepath))
		{
			return false;
		}
		$zip = new ZipArchive;
		$res = $zip->open($vpn_filepath);
		if ($res === TRUE)
		{
		    $zip->extractTo(VPN_CONF_MNT);
		    $zip->close();
		} else {
		    return false;
		}
	}
}
?>
