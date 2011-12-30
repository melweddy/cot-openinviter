<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

defined('COT_CODE') || die('Wrong URL.');

/**
 * Open Inviter as standalone Cotonti plugin.
 * Is a decomposed example.php using tpl and lang.
 *
 * @package openinviter
 * @version 1.1
 * @author Trustmaster
 * @copyright (c) 2011 Vladimir Sibirov, Skuola.net
 * @license BSD
 */

/**
 * Error display function
 * @param array $ers Error messages
 * @param XTemplate $t1 XTemplate object 
 */
function ers($ers, $t1)
{
	if (!empty($ers))
	{
		foreach ($ers as $key => $error)
		{
			$t1->assign('ERROR_ROW_MSG', $error);
			$t1->parse('MAIN.ERROR.ERROR_ROW');
		}
		$t1->parse('MAIN.ERROR');
	}
}

/**
 * Success display function
 * @param array $oks Success messages
 * @param XTemplate $t1 XTemplate object
 */
function oks($oks, $t1)
{
	if (!empty($oks))
	{
		foreach ($oks as $key => $msg)
		{
			$t1->assign('OK_ROW_MSG', $msg);
			$t1->parse('MAIN.OK.OK_ROW');
		}
		$t1->parse('MAIN.OK');
	}
}

include_once cot_langfile('openinviter', 'plug');

include($cfg['plugins_dir'].'/openinviter/OpenInviter/openinviter.php');
$inviter = new OpenInviter();
$oi_services = $inviter->getPlugins();
if (isset($_POST['provider_box']))
{
	if (isset($oi_services['email'][$_POST['provider_box']]))
		$plugType = 'email';
	elseif (isset($oi_services['social'][$_POST['provider_box']]))
		$plugType = 'social';
	else
		$plugType='';
}
else
	$plugType = '';

if (!empty($_POST['step']))
	$step = $_POST['step'];
else
	$step='get_contacts';

$ers = array();
$oks = array();
$import_ok = false;
$done = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($step == 'get_contacts')
	{
		if (empty($_POST['email_box']))
			$ers['email'] = $L['oi_email_missing'];
		if (empty($_POST['password_box']))
			$ers['password'] = $L['oi_password_missing'];
		if (empty($_POST['provider_box']))
			$ers['provider'] = $L['oi_provider_missing'];
		if (count($ers) == 0)
		{
			$inviter->startPlugin($_POST['provider_box']);
			$internal = $inviter->getInternalError();
			if ($internal)
				$ers['inviter'] = $internal;
			elseif (!$inviter->login($_POST['email_box'], $_POST['password_box']))
			{
				$internal = $inviter->getInternalError();
				$ers['login'] = ($internal ? $internal : $L['oi_login_failed']);
			}
			elseif (false === $contacts = $inviter->getMyContacts())
				$ers['contacts'] = $L['oi_contacts_failed'];
			else
			{
				$import_ok = true;
				$step = 'send_invites';
				$_POST['oi_session_id'] = $inviter->plugin->getSessionID();
				$_POST['message_box'] = '';
			}
		}
	}
	elseif ($step == 'send_invites')
	{
		if (empty($_POST['provider_box']))
			$ers['provider'] = $L['oi_provider_missing'];
		else
		{
			$inviter->startPlugin($_POST['provider_box']);
			$internal = $inviter->getInternalError();
			if ($internal)
				$ers['internal'] = $internal;
			else
			{
				if (empty($_POST['email_box']))
					$ers['inviter'] = $L['oi_inviter_missing'];
				if (empty($_POST['oi_session_id']))
					$ers['session_id'] = $L['oi_no_session'];
				if (empty($_POST['message_box']))
					$ers['message_body'] = $L['oi_msg_missing'];
				else
					$_POST['message_box'] = strip_tags($_POST['message_box']);
				$selected_contacts = array();
				$contacts = array();
				$message = array('subject' => $inviter->settings['message_subject'], 'body' => $inviter->settings['message_body'], 'attachment' => "\r\n{$L['oi_attached_msg']}: \r\n" . $_POST['message_box']);
				if ($inviter->showContacts())
				{
					foreach ($_POST as $key => $val)
						if (strpos($key, 'check_') !== false)
							$selected_contacts[$_POST['email_' . $val]] = $_POST['name_' . $val];
						elseif (strpos($key, 'email_') !== false)
						{
							$temp = explode('_', $key);
							$counter = $temp[1];
							if (is_numeric($temp[1]))
								$contacts[$val] = $_POST['name_' . $temp[1]];
						}
					if (count($selected_contacts) == 0)
						$ers['contacts'] = $L['oi_no_contacts'];
				}
			}
		}
		if (count($ers) == 0)
		{
			$sendMessage = $inviter->sendMessage($_POST['oi_session_id'], $message, $selected_contacts);
			$inviter->logout();
			if ($sendMessage === -1)
			{
				$message_subject = $_POST['email_box'] . $message['subject'];
				$message_body = $message['body'] . $message['attachment'];
				$headers = "From: {$_POST['email_box']}";
				foreach ($selected_contacts as $email => $name)
					mail($email, $message_subject, $message_body, $headers);
				$oks['mails'] = $L['oi_mails_sent'];
			}
			elseif ($sendMessage === false)
			{
				$internal = $inviter->getInternalError();
				$ers['internal'] = ($internal ? $internal : $L['oi_errors_found']);
			}
			else
				$oks['internal'] = $L['oi_invites_sent'];
			$done = true;
		}
	}
}
else
{
	$_POST['email_box'] = '';
	$_POST['password_box'] = '';
	$_POST['provider_box'] = '';
}

$t1 = new XTemplate(cot_tplfile('openinviter.body', 'plug'));

ers($ers, $t1);
oks($oks, $t1);

if (!$done)
{
	if ($step == 'get_contacts')
	{
		$t1->assign(array(
			'GET_EMAIL_BOX' => htmlspecialchars($_POST['email_box']),
			'GET_PASSWORD_BOX' => htmlspecialchars($_POST['password_box']),
		));
		foreach ($oi_services as $type => $providers)
		{
			$t1->assign('GET_SERVICE_TYPE', htmlspecialchars($inviter->pluginTypes[$type]));
			foreach ($providers as $provider => $details)
			{
				$selected = ($_POST['provider_box'] == $provider) ? ' selected' : '';
				$t1->assign(array(
					'GET_SERVICE_PROVIDER_VAL' => htmlspecialchars($provider),
					'GET_SERVICE_PROVIDER_SEL' => $selected,
					'GET_SERVICE_PROVIDER_NAME' => $details['name']
				));
				$t1->parse('MAIN.GET.GET_SERVICE.GET_SERVICE_PROVIDER');
			}
			$t1->parse('MAIN.GET.GET_SERVICE');
		}
		$t1->parse('MAIN.GET');
	}
	else
	{
		$t1->assign(array(
			'SENDBUTTON_MESSAGE_BOX' => htmlspecialchars($_POST['message_box'])
		));
		$t1->parse('MAIN.SENDBUTTON');
	}
}

if (!$done)
{
	if ($step == 'send_invites')
	{
		if ($inviter->showContacts())
		{
			$t1->assign('SEND_SHOW_COLSPAN', ($plugType == 'email') ? "3" : "2");
			if (count($contacts) == 0)
			{
				$t1->parse('MAIN.SEND.SEND_SHOW.NO_CONTACTS');
			}
			else
			{
				$odd = true;
				$counter = 0;
				foreach ($contacts as $email => $name)
				{
					$counter++;
					$class = ($odd) ? 'thTableOddRow' : 'thTableEvenRow';
					$t1->assign(array(
						'CONTACTS_ROW_EMAIL' => htmlspecialchars($email),
						'CONTACTS_ROW_NAME' => htmlspecialchars($name),
						'CONTACTS_ROW_COUNTER' => $counter,
						'CONTACTS_ROW_CLASS' => $class
					));
					$odd = !$odd;
					$t1->parse('MAIN.SEND.SEND_SHOW.CONTACTS.CONTACTS_ROW');
				}
				$t1->parse('MAIN.SEND.SEND_SHOW.CONTACTS');
			}
			$t1->parse('MAIN.SEND.SEND_SHOW');
		}
		$t1->assign(array(
			'SEND_PROVIDER_BOX' => htmlspecialchars($_POST['provider_box']),
			'SEND_EMAIL_BOX' => htmlspecialchars($_POST['email_box']),
			'SEND_SESSION_ID' => htmlspecialchars($_POST['oi_session_id'])
		));
		$t1->parse('MAIN.SEND');
	}
}

$t1->parse('MAIN');

$plugin_body = $t1->text('MAIN');
if (!empty($L['oi_subtitle']))
    $out['subtitle'] = $L['oi_subtitle'];

?>
