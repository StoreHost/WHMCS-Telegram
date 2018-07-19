<?php
function sendTelegramMessage($pm) {
	global $vars;
	$application_chatid = mysql_fetch_array( select_query('tbladdonmodules', 'value', array('module' => 'wt_note', 'setting' => 'chatid') ), MYSQL_ASSOC );
	$application_botkey = mysql_fetch_array( select_query('tbladdonmodules', 'value', array('module' => 'wt_note', 'setting' => 'key') ), MYSQL_ASSOC );
	$chat_id 		= $application_chatid['value'];
	$botToken 		= $application_botkey['value'];

	$data = array(
		'chat_id' 	=> $chat_id,
		'text' 		=> $pm . "\n\n----------------------------------------------------------------------------------------------\n" . base64_decode("V0hNQ1MgVGVsZWdyYW0gaG9vayB2b24gU3RvcmVIb3N0==")
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot$botToken/sendMessage");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_exec($curl);
	curl_close($curl);
}

function wt_note_ClientAdd($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Ein neuer Nutzer hat sich Registriert. \n---------------------------------------------------------------------------------------------- \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/clientssummary.php?userid='.$vars['userid']);
}

function wt_note_InvoicePaid($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Eine Rechnung mit folgenden Angaben wurde bezahlt \n---------------------------------------------------------------------------------------------- \n\n Rechnungs-Nr. : $vars[invoiceid] \n\n Betrag : $vars[total] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/invoices.php?action=edit&id='.$vars['invoiceid']);
}

function wt_note_TicketOpen($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Ein neues Ticket wurde erstellt \n---------------------------------------------------------------------------------------------- \n\n Ticket-ID : $vars[ticketid] \n\n Betreff : $vars[deptname] \n\n Betreff : $vars[subject] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid']);
}

function wt_note_TicketUserReply($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Eine neue Antwort wurde gesendet \n---------------------------------------------------------------------------------------------- \n\n Ticket-ID : $vars[ticketid] \n\n Abteilung : $vars[deptname] \n\n Betreff : $vars[subject] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid'], $application_botkey, $application_chatid);

}

#####

function wt_note_FileDownloaded($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Es wurde eine Datei runtergeladen \n---------------------------------------------------------------------------------------------- \n\n Dateiname : $vars[filename] \n\n SeitenURL : $vars[pagetitle] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/clientssummary.php?userid='.$vars['userid']);
}
function wt_note_FileDownloaded($vars) {
	global $customadminpath, $CONFIG;
	sendTelegramMessage("Ein Benutzer hat seine Email Verifiziert \n---------------------------------------------------------------------------------------------- \n\n Benutzer-ID : $vars[deptname] \n\n". $CONFIG['SystemURL'].'/'.$customadminpath.'/clientssummary.php?userid='.$vars['userid']);
}

add_hook("ClientAdd",1,"wt_note_ClientAdd");
add_hook("InvoicePaid",1,"wt_note_InvoicePaid");
add_hook("TicketOpen",1,"wt_note_TicketOpen");
add_hook("TicketUserReply",1,"wt_note_TicketUserReply");
add_hook("ClientAreaPage",1,"wt_note_FileDownloaded");
add_hook("ClientEmailVerificationComplete",1,"wt_note_EmailVertification")