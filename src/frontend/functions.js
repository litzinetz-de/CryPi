function SubmitCreateContainer()
{
	alert('...');
	var submit_ok=true;
	if(document.getElementByID('c_name').value=='')
	{
	  document.getElementByID('c_name').backgroundColor='red';
	  submit_ok=false;
	  alert('d1');
	}
	if(document.getElementByID('c_password').value=='')
	{
	  document.getElementByID('c_password').backgroundColor='red';
	  submit_ok=false;
	  alert('d2');
	}
	if(document.getElementByID('c_password').value!=document.getElementByID('c_confirm').value)
	{
	  document.getElementByID('c_password').backgroundColor='red';
	  document.getElementByID('c_confirm').backgroundColor='red';
	  submit_ok=false;
	  alert('d3');
	}
	
	if(submit_ok)
	{
	  alert('ok');
	  document.getElementByID('c_submitbutton').value='Please wait, this takes some time...';
	  document.getElementByID('c_submitbutton').disabled=true;
	  document.create_container.submit();
	}
}