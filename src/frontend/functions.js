function SubmitCreateContainer()
{
	submit_ok=true;
	if(document.getElementByID('c_name').value=='')
	{
	  document.getElementByID('c_name').backgroundColor='red';
	  submit_ok=false;
	}
	if(document.getElementByID('c_password').value=='')
	{
	  document.getElementByID('c_password').backgroundColor='red';
	  submit_ok=false;
	}
	if(document.getElementByID('c_password').value!=document.getElementByID('c_confirm').value)
	{
	  document.getElementByID('c_password').backgroundColor='red';
	  document.getElementByID('c_confirm').backgroundColor='red';
	  submit_ok=false;
	}
	
	if(submit_ok)
	{
	  alert('..');
	  document.getElementByID('c_submitbutton').value='Please wait, this takes some time...';
	  document.getElementByID('c_submitbutton').disabled=true;
	  document.create_container.submit();
	}
}