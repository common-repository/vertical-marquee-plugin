// JavaScript Document
function _vm_submit()
{
	if(document.vm_form.vm_text.value=="")
	{
		alert(vertical_marquee_adminscripts.vm_text);
		document.vm_form.vm_text.focus();
		return false;
	}
	else if(document.vm_form.vm_group.value == "" || document.vm_form.vm_group.value == "Select")
	{
		alert(vertical_marquee_adminscripts.vm_group);
		document.vm_form.vm_group.focus();
		return false;
	}
}

function _vm_delete(id)
{
	if(confirm(vertical_marquee_adminscripts.vm_delete))
	{
		document.frm_vm_display.action="options-general.php?page=vertical-marquee-plugin&ac=del&did="+id;
		document.frm_vm_display.submit();
	}
}	

function _vm_redirect()
{
	window.location = "options-general.php?page=vertical-marquee-plugin";
}

function _vm_help()
{
	window.open("http://www.gopiplus.com/work/2012/06/30/vertical-marquee-wordpress-plugin/");
}		  