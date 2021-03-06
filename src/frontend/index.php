<?php
require('libs/versionmanager.php');
require('libs/crypto.php');
require('libs/gui.php');

$v = new version();
$c = new crypto();
$g = new gui($v->GetCurrentVersion());

$g->GlobalHeader();

$g->GlobalNavigation();

echo '</td><td>';

if($_GET['do']=='')
{
	$g->IndexPage();
}
if($_GET['do']=='network')
{
	if($_GET['write']!='y')
	{
		$network_buffer=$c->GetNetworkSettings();
		$bypass_vpn_buffer=$c->GetBypassVPN();
		if($bypass_vpn_buffer)
		{
			$bypass_checked=' checked';
		} else {
			$bypass_checked='';
		}
		echo '<form action="?do=network&write=y" method="post"><table border="0">
		<tr class="tableheader"><td colspan="2"><b>Network settings</b></td></tr>
		<tr><td>IP address:</td><td><input type="text" name="networking_addr" size="20" class="formstyle" value="'.$network_buffer['addr'].'"></td></tr>
		<tr><td>Netmask:</td><td><input type="text" name="networking_mask" size="20" class="formstyle" value="'.$network_buffer['mask'].'"></td></tr>
		<tr><td>Gateway:</td><td><input type="text" name="networking_gateway" size="20" class="formstyle" value="'.$network_buffer['gateway'].'"></td></tr>
		<tr><td colspan="2"><hr size="1"></td></tr>
		<tr><td>Bypass VPN:<br><div width="200px"><small>Enable this to allow traffic to bypass the VPN tunnel when it\'s not connected. By default, this is not allowed and also not recommended. Changing this requires a reboot.</small></div></td><td><input type="checkbox" name="bypass_vpn" value="y"'.$bypass_checked.'></td></tr>
		</table>
		<br><br>
		<input type="submit" value="Apply" class="formstyle"></form>';
	} else {
		$network_buffer=$c->GetNetworkSettings();
		if($_POST['networking_addr']!=$network_buffer['addr'] || $_POST['networking_mask']!=$network_buffer['mask'] || $_POST['networking_gateway']!=$network_buffer['gateway'])
		{
			if($c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']))
			{
				$g->SysMSG('Network settings applied.');
			} else {
				$g->SysMSG('Error! Please check the entered addresses.');
			}
			//$c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']);
		}
		if($_POST['bypass_vpn']=='y')
		{
			$bypass_vpn=true;
		} else {
			$bypass_vpn=false;
		}
		$c->SetBypassVPN($bypass_vpn);
	}
}

if($_GET['do']=='static_routes')
{
	echo '<table><tr class="tableheader"><td colspan="4"><b>Static routes</b></td></tr>
	<tr class="tableheader"><td>Network</td><td>Netmask</td><td>Gateway</td><td>&nbsp;</td></tr>';
	$buffer=$c->GetStaticRoutes();
	foreach($buffer as $line)
	{
		echo '<tr><td>'.$line[0].'</td><td>'.$line[1].'</td><td>'.$line[2].'</td><td>&nbsp;</td></tr>';
	}
	echo '</table>';
}

if($_GET['do']=='containers')
{
	$containerlist=$c->ReadContainers();
	$container_mounted=$c->container_mounted();
	
	echo '<center><b>';
	if($container_mounted)
	{
		echo 'There is a container mounted. <a href="?do=dismount">Dismount now</a>';
	} else {
		echo 'There is no container mounted. Choose a container below or create a new one.';
	}
	
	echo '</b></center><br><br><form action="?do=mount" method="post">
	<select name="mnt_container" size="5" class="formstyle">';
	foreach($containerlist as $cur_container)
	{
		echo '<option value="'.$cur_container.'">'.$cur_container.'</option>';
	}
	echo '</select><br>Container password: <input type="password" name="mnt_password" id="mnt_password" class="formstyle"> <input type="submit" value="Mount" class="formstyle"></form><br><hr size="2"><br>
	<form name="create_container" action="?do=create_container" method="post">Create container: <input type="text" size="20" name="c_name" class="formstyle" id="c_name"> with password: <input type="password" name="c_password" id="c_password" size="20" class="formstyle"> confirm: <input type="password" name="c_confirm" id="c_confirm" size="20" class="formstyle"> 
	<input type="submit" value="Create" name="c_submitbutton" id="c_submitbutton" class="formstyle"> (Currently mounted containers will be dismounted and connected VPN tunnels will be dropped!)</form>';
}

if($_GET['do']=='create_container')
{
	if(trim($_POST['c_name'])=='')
	{
		die('Error: no container name given.');
	}
	if(trim($_POST['c_password'])=='')
	{
		die('Error: no password given.');
	}
	if($_POST['c_password']!=$_POST['c_confirm'])
	{
		die('Error: the passwords do not match.');
	}
	if($c->create_container($_POST['c_password'],$_POST['c_name']))
	{
		echo 'The container has been created. You may mount it now.';
	} else {
		echo 'Couldn\'t create container :(';
	}
}

if($_GET['do']=='mount')
{
	if($c->mount_container($_POST['mnt_password'],$_POST['mnt_container']))
	{
		echo 'Container mounted.';
	} else {
		echo 'Coudn\'t mount container. Please make sure you use the right password! Forgotten passwords are lost forever.';
	}
}

if($_GET['do']=='dismount')
{
	if($c->dismount_container())
	{
		echo 'Container dismounted.';
	} else {
		echo 'Couldn\'t dismount container. Is it even mounted?';
	}
}

if($_GET['do']=='vpn')
{
	if(!$c->container_mounted())
	{
		$g->SysMSG('Please mount or create a container first');
		die();
	}
	$configlist=$c->FindConfigs();
	if(empty($configlist))
	{
		$cl_empty=true;
	} else {
		$cl_empty=false;
	}
	
	if($c->CredentialsAvailable())
	{
		$cred_avail=true;
	} else {
		$cred_avail=false;
	}
	
	if($cl_empty || !$cred_avail)
	{
		$msg='Before we can connect to a VPN, there are still some steps to complete:<br><br>';
		if($cl_empty)
		{
			$msg=$msg.'<li><font color="red">I don\'t see any vpn configs in this container yet. Please upload your VPN config bundle that you have got from your VPN provider below. It must be an OpenVPN package. I will sort it and find the config files, which must end with &quot;.ovpn&quot;.</font></li>';
		}
		if(!$cred_avail)
		{
			$msg=$msg.'<li><font color="red">You have not provided the login credentials for your VPN provider. I need them to setup a VPN connection. No worries, I will store them inside the encrypted container.</font></li>';
		}
		echo $msg;
		$g->SysMSG('<br>Please complete the steps mentioned above and we will try it again.');
	}
	
	if(!$cl_empty)
	{
		echo '<table border="0" cellspacing="1"><tr><td valign="top"><form action="?do=vpn_connect" method="post">Select a VPN config from the list below and click &quot;Connect&quot;.<br><select name="vpn_config" size="10">';
		foreach($configlist as $config)
		{
			$config=$c->RemoveMntPath($config);
			echo '<option value="'.$config.'">'.$config.'</option>';
		}
		echo '</select><br><input type="submit" value="Connect"></form></td><td width="200px">&nbsp;</td>
		<td valign="top">VPN: ';
		
		if($c->VPNConnected())
		{
			echo '<font color="green">Connected</font> [<a href="?do=vpn_disconnect">Disconnect</a>]';
		} else {
			echo '<font color="red">Disconnected</font>';
		}
		$routeinfo=$c->ShowWANRoute();
		echo '<br><br>WAN routing via:<br>'.$routeinfo[1].'<br>'.$routeinfo[2].'</td>
		</tr></table>';
	}
	echo '<br><br>Upload container (ZIP file, max. 12 MB): <form enctype="multipart/form-data" action="?do=vpn_upload" method="post"><input name="vpn_file" type="file">
	<input type="submit" value="Upload container"></form><br><br>
	<form action="?do=set_credentials" method="post">Set VPN login credentials:<br>Username: <input type="text" name="username" size="20"> Password: <input type="password" size="20" name="password"> <input type="submit" value="Save"></form><br>
	<small>This will overwrite given credentials (<font color="red">please don\'t use spaces here!</font>). I will also modify the VPN config files if needed. It might take a moment, please wait until I have finished with that!</small>';
}

if($_GET['do']=='set_credentials')
{
	if(!$c->container_mounted())
	{
		$g->SysMSG('Please mount a container first.');
		die();
	}
	$c->AddCredentials(trim($_POST['username']),trim($_POST['password']));
	echo 'Credentials set.';
}

if($_GET['do']=='vpn_upload')
{
	if($c->HandleVPNUpload())
	{
		$g->SysMSG('Upload successful.');
	} else {
		$g->SysMSG('Upload failed! Is is a ZIP file? File corrupted? Too large?');
	}
}

if($_GET['do']=='vpn_connect')
{
	if(trim($_POST['vpn_config'])=='')
	{
		$g->SysMSG('No config selected');
		die();
	}
	$c->StartVPN($_POST['vpn_config']);
	echo 'Building tunnel, should be up in a few seconds.';
}

if($_GET['do']=='vpn_disconnect')
{
	$c->KillVPN();
	$g->SysMSG('I have killed the tunnel.');
}

$g->GlobalFooter();

?>
