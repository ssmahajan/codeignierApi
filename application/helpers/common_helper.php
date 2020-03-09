<?php



/**
 * Sends an email, returns true if succeded, false on failure
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param boolean $html
 * @param array $cc
 * @param array $bcc
 * @param array $attachs
 * @param string $altmessage
 * @return boolean
 */
function sendEmail($to, $subject, $message , $html = false , $cc = array() , $bcc = array() , $attachs = array() , $altmessage = null , $extraheaders = array() , $fromEMailAddress = '', $replyToEMail = '', $replyToName = '') {
    $CI = &get_instance();
    $CI->email->clear(true);
    $initArray=[];
/*
    $initArray=array(
        'protocol' => 'smtp',
        'smtp_host' =>'smtp.sendgrid.net',
//        'smtp_host' =>'smtp.gmail.com',
        'smtp_user' => '',
        'smtp_pass' => '',
        'smtp_port' => '587',
        'crlf' => "\r\n",
        'newline' => "\r\n"
    );*/

    if ($html) {
        $initArray['mailtype']="html";
        if ($altmessage != null)
            $CI->email->set_alt_message($altmessage);
        else
            $CI->email->set_alt_message( strip_tags(str_replace(array("<br/>" , "<br>" , "<BR/>" , "<BR>"), "\n", $message)) );
    }
    $CI->email->initialize($initArray);

    if (count($extraheaders)) {
        foreach ($extraheaders as $key => $value) {
            $CI->email->set_header($key , $value);
        }
    }


    $CI->email->from( (empty($fromEMailAddress) ? $CI->config->item('fromEmail') : $fromEMailAddress) ,$CI->config->item('fromEmailName'));
    // $CI->email->from('your@example.com', 'Your Name');
    $CI->email->to(explode(',',$to));

    if (!empty($replyToEMail)) $CI->email->reply_to($replyToEMail, $replyToName);


    $CI->email->subject($subject);
    $CI->email->message($message);

    if (count($cc) > 0)
        foreach ($cc as $e)
            $CI->email->cc( $e );

    if (count($bcc) > 0)
    {
        foreach ($bcc as $e)
        {
            $CI->email->bcc( $e );
        }
    }

    if (count($attachs) > 0)
        foreach ($attachs as $f)
            $CI->email->attach( $f );

    return @$CI->email->send();
}


function randomString($str=8){
    return create_guid_section($str);
}
/**
 * A temporary method of generating GUIDs of the correct format for our DB.
 *
 * @return string contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
 *
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function uuid()
{
    $microTime = microtime();
    list($a_dec, $a_sec) = explode(' ', $microTime);

    $dec_hex = dechex($a_dec * 1000000);
    $sec_hex = dechex($a_sec);

    ensure_length($dec_hex, 5);
    ensure_length($sec_hex, 6);

    $guid = '';
    $guid .= $dec_hex;
    $guid .= create_guid_section(3);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= $sec_hex;
    $guid .= create_guid_section(6);

    return $guid;
}

function create_guid_section($characters)
{
    $return = '';
    for ($i = 0; $i < $characters; ++$i) {
        $return .= dechex(mt_rand(0, 15));
    }

    return $return;
}

function getTitle(){
    $CI = &get_instance();
    return @$CI->session->userdata("key_title");
}
function setTitle($str){
    $CI = &get_instance();
    $arrrayKey=array("key_title"=>$str);
    $CI->session->set_userdata($arrrayKey);
}
