<!-- BEGIN: MAIN -->

<script type='text/javascript'>
function toggleAll(element) {
	var form = document.forms.openinviter, z = 0;
	for(z=0; z<form.length;z++) {
		if(form[z].type == 'checkbox')
			form[z].checked = element.checked;
	}
}
</script>

<!-- BEGIN: ERROR -->
<table cellspacing='0' cellpadding='0' style='border:1px solid red;' align='center'>
	<tr>
		<td valign='middle' style='padding:3px' valign='middle'>
			<img src='plugins/openinviter/OpenInviter/images/ers.gif'>
		</td>
		<td valign='middle' style='color:red;padding:5px;'>
			<!-- BEGIN: ERROR_ROW -->
			{ERROR_ROW_MSG}<br />
			<!-- END: ERROR_ROW -->
		</td>
	</tr>
</table>
<br >
<!-- END: ERROR -->

<!-- BEGIN: OK -->
<table border='0' cellspacing='0' cellpadding='10' style='border:1px solid #5897FE;' align='center'>
	<tr>
		<td valign='middle' valign='middle'>
			<img src='plugins/openinviter/OpenInviter/images/oks.gif' >
		</td>
		<td valign='middle' style='color:#5897FE;padding:5px;'>
			<!-- BEGIN: OK_ROW -->
			{OK_ROW_MSG}<br />
			<!-- END: OK_ROW -->
		</td>
	</tr>
</table>
<br >
<!-- END: OK -->

<form action='' method='POST' name='openinviter'>
	
<!-- BEGIN: GET -->
<table align='center' class='thTable' cellspacing='2' cellpadding='0' style='border:none;'>
	<tr class='thTableRow'>
		<td align='right'>
			<label for='email_box'>{PHP.L.Email}</label>
		</td>
		<td>
			<input class='thTextbox' type='text' name='email_box' value='{GET_EMAIL_BOX}'>
		</td>
	</tr>
	<tr class='thTableRow'>
		<td align='right'>
			<label for='password_box'>{PHP.L.Password}</label>
		</td>
		<td>
			<input class='thTextbox' type='password' name='password_box' value='{GET_PASSWORD_BOX}'>
		</td>
	</tr>
	<tr class='thTableRow'>
		<td align='right'>
			<label for='provider_box'>{PHP.L.oi_email_provider}</label>
		</td>
		<td>
			<select class='thSelect' name='provider_box'>
				<option value=''></option>
				<!-- BEGIN: GET_SERVICE -->
				<optgroup label='{GET_SERVICE_TYPE}'>
					<!-- BEGIN: GET_SERVICE_PROVIDER -->
					<option value='{GET_SERVICE_PROVIDER_VAL}'{GET_SERVICE_PROVIDER_SEL}>{GET_SERVICE_PROVIDER_NAME}</option>
					<!-- END: GET_SERVICE_PROVIDER -->
				</optgroup>
				<!-- END: GET_SERVICE -->
			</select>
		</td>
	</tr>
	<tr class='thTableImportantRow'>
		<td colspan='2' align='center'>
			<input class='thButton' type='submit' name='import' value='Import Contacts'>
		</td>
	</tr>
</table>
<input type='hidden' name='step' value='get_contacts'>
<!-- END: GET -->

<!-- BEGIN: SENDBUTTON -->
<table class='thTable' cellspacing='0' cellpadding='0' style='border:none;'>
	<tr class='thTableRow'>
		<td align='right' valign='top'>
			<label for='message_box'>{PHP.L.Message}</label>
		</td>
		<td>
			<textarea rows='5' cols='50' name='message_box' class='thTextArea' style='width:300px;'>{SENDBUTTON_MESSAGE_BOX}</textarea>
		</td>
	</tr>
	<tr class='thTableRow'>
		<td align='center' colspan='2'>
			<input type='submit' name='send' value='{PHP.L.oi_send_invites}' class='thButton' >
		</td>
	</tr>
</table>
<!-- END: SENDBUTTON -->

<!-- BEGIN: SEND -->
<!-- BEGIN: SEND_SHOW -->
<table class='thTable' align='center' cellspacing='0' cellpadding='0'>
	<tr class='thTableHeader'>
		<td colspan='{SEND_SHOW_COLSPAN}'>{PHP.L.oi_your_contacts}</td>
	</tr>
	<!-- BEGIN: NO_CONTACTS -->
	<tr class='thTableOddRow'>
		<td align='center' style='padding:20px;' colspan='{SEND_SHOW_COLSPAN}'>{PHP.L.oi_no_contacts_book}</td>
	</tr>
	<!-- END: NO_CONTACTS -->
	
	<!-- BEGIN: CONTACTS -->
	<tr class='thTableDesc'>
		<td>
			<input type='checkbox' onChange='toggleAll(this)' name='toggle_all' title='Select/Deselect all' checked>{PHP.L.oi_invite}
		</td>
		<td>{PHP.L.Name}</td>
		<!-- IF {PHP.plugType} == 'email -->
		<td>{PHP.L.Email}</td>
		<!-- ENDIF -->
		</tr>
		<!-- BEGIN: CONTACTS_ROW -->
		<tr class='{CONTACTS_ROW_CLASS}'>
			<td>
				<input name='check_{CONTACTS_ROW_COUNTER}' value='{CONTACTS_ROW_COUNTER}' type='checkbox' class='thCheckbox' checked>
				<input type='hidden' name='email_{CONTACTS_ROW_COUNTER}' value='{CONTACTS_ROW_EMAIL}'><input type='hidden' name='name_{CONTACTS_ROW_COUNTER}' value='{CONTACTS_ROW_NAME}'>
			</td>
			<td>{CONTACTS_ROW_NAME}</td>
			<!-- IF {PHP.plugType} == 'email -->
			<td>{CONTACTS_ROW_EMAIL}</td>
			<!-- ENDIF -->
		</tr>
		<!-- END: CONTACTS_ROW -->
		<tr class='thTableFooter'>
			<td colspan='{SEND_SHOW_COLSPAN}' style='padding:3px;'>
				<input type='submit' name='send' value='{PHP.L.oi_send_invites}' class='thButton'>
			</td>
		</tr>
	<!-- END: CONTACTS -->
</table>
<!-- END: SEND_SHOW -->

<input type='hidden' name='step' value='send_invites'>
<input type='hidden' name='provider_box' value='{SEND_PROVIDER_BOX}'>
<input type='hidden' name='email_box' value='{SEND_EMAIL_BOX}'>
<input type='hidden' name='oi_session_id' value='{SEND_SESSION_ID}'>
<!-- END: SEND -->

</form>

<!-- END: MAIN -->