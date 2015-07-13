# CryptoPi

=== CURRENTLY IN DEVELOPMENT - NOT READY FOR USE YET - CONTRIBUTIONS ARE WELCOME! ===

CryptoPi (CryPi) is a web based application written in PHP that helps you to manage OpenVPN connections to secure your internet connection.

Using the web based frontent, you can create secure encrypted containers which are stored on the CryPi. Then, upload your OpenVPN config files to this container and tell CryPi your VPN login credentials (stored secure in the container). After that, CryPi will create the VPN tunnel using OpenVPN and will start acting as a NAT router. Simply configure all the devices in your network to use CryPi as their default gateway - that's all. All the traffic coming to CryPi will then be routed trough the tunnel automatically.

Due to safety reasons, it's not possible to save a container's password on the device - therefore, you have to unlock the config container every time CryPi boots by entering it's password.

Create as many containers as you with. Open one of them and go online using an encrypted VPN tunnel.

Currently, CryPi is only tested with the VPN provider "Perfect Privacy" (which seems to be the best when it comes to anonymity and privacy), but should work fine with other providers, too.

If there's a provider that doesn't work with CryPi, just create a testing account and send the credentials to us - we will try to get it work.

Thank you for using this software.

CryptoPi - Anonymity, Privacy, Security.
