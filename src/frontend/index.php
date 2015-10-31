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
if($_GET['do']=='system')
{
	if($_GET['write']!='y')
	{
		$buffer=$c->GetNetworkSettings();
		echo '<form action="?do=system&write=y" method="post"><table border="0">
		<tr class="tableheader"><td colspan="2"><b>Network settings</b></td></tr>
		<tr><td>IP address:</td><td><input type="text" name="networking_addr" size="20" class="formstyle" value="'.$buffer['addr'].'"></td></tr>
		<tr><td>Netmask:</td><td><input type="text" name="networking_mask" size="20" class="formstyle" value="'.$buffer['mask'].'"></td></tr>
		<tr><td>Gateway:</td><td><input type="text" name="networking_gateway" size="20" class="formstyle" value="'.$buffer['gateway'].'"></td></tr>
		</table>
		<br><br>
		<input type="submit" value="Apply" class="formstyle"></form>';
	} else {
		$buffer=$c->GetNetworkSettings();
		if($_POST['networking_addr']!=$buffer['addr'] || $_POST['networking_mask']!=$buffer['mask'] || $_POST['networking_gateway']!=$buffer['gateway'])
		{
			if($c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']))
			{
				$g->SysMSG('Network settings applied.');
			} else {
				$g->SysMSG('Error! Please check the entered addresses.');
			}
			//$c->SetNetworkSettings($_POST['networking_addr'],$_POST['networking_mask'],$_POST['networking_gateway']);
		}
	}
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
			$msg=$msg.'<li>I don\'t see any vpn configs in this container yet. Please upload your VPN config bundle that you have got from your VPN provider below. It must be an OpenVPN package. I will sort it and find the config files, which must end with &quot;.ovpn&quot;.</li>';
		}
		if(!$cred_avail)
		{
			$msg=$msg.'<li>You have not provided the login credentials for your VPN provider. I need them to setup a VPN connection. No worries, I will store them inside the encrypted container.</li>';
		}
		echo $msg;
		$g->SysMSG('<br>Please complete the steps mentioned above and we will try it again.');
	}
	
	if(!$cl_empty)
	{
		echo '<form action="?do=vpn_connect" method="post">Select a VPN config from the list below and click &quot;Connect&quot;.<br><select name="vpn_config" size="10">';
		foreach($configlist as $config)
		{
			$config=$c->RemoveMntPath($config);
			echo '<option value="'.$config.'">'.$config.'</option>';
		}
		echo '</select><br><input type="submit" value="Connect"></form>';
	}
	echo '<br><br>Upload container (ZIP file, max. 12 MB): <form enctype="multipart/form-data" action="?do=vpn_upload" method="post"><input type="hidden" name="MAX_FILE_SIZE" value="12000"><input name="vpn_file" type="file">
	<input type="submit" value="Upload container"></form>';
}

$g->GlobalFooter();

?>
