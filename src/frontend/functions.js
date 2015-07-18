function SubmitCreateContainer()
{
	submit_ok=true;
	if(document.create_container.c_name.value=='')
	{
	  document.create_container.c_name.backgroundColor='red';
	  submit_ok=false;
	}
	if(document.create_container.c_password.value=='')
	{
	  document.create_container.c_password.backgroundColor='red';
	  submit_ok=false;
	}
	if(document.create_container.c_name.value!=document.create_container.c_confirm.value)
	{
	  document.create_container.c_password.backgroundColor='red';
	  document.create_container.c_confirm.backgroundColor='red';
	  submit_ok=false;
	}
	
	if(submit_ok)
	{
	  document.create_container.submitbutton.value='Please wait, this takes some time...';
	  document.create_container.submitbutton.disabled=true;
	  document.create_container.submit();
	}
}