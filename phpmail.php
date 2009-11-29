<?php

require_once('lib/class/classTextile.php');
require_once('config/config.php');
require_once('lib/class/NotAProfile.php');

$host="{s9117.gridserver.com:110/pop3}"; // pop3host
$login="upload@notaprofile.com"; //pop3 login
$password="notaprof1le"; //pop3 password
$savedirpath="" ; // attachement will save in same directory where scripts run othrwise give abs path
$data = read_mail($host,$login,$password,$savedirpath,true); // calling member function

if (count($data)>0) {
	print_r($data);
	mail_attachment($data, "mauricio@pingpongestudio.com", "no-reply@notaprofile.com", "not_a_profile bot", "", "[not_a_profile] key via mail");
}

function read_mail ($host,$login,$password,$must_delete=false) {
	$r = array();
	$r["text"] = "";
	$r["attachment"] = array();
	// Open pop mailbox
	if (!$mbox = imap_open ($host, $login, $password)) {
		die ('Cannot connect/check pop mail! Exiting');
	}
	
	if ($hdr = imap_check($mbox)) {
		$msgCount = $hdr->Nmsgs;
	} else {
		echo "Failed to get mail";
		exit;
	}
	
	$MN=$msgCount;
	
	for ($X = 1; $X <= $MN; $X++) {
		$struct = imap_fetchstructure($mbox, $X);
		$parts = create_part_array($struct);
		foreach ($parts as $part) {
			if ($part["part_object"]->type==0) {
				// es texto... meter en el string
				$r["text"] .= imap_fetchbody($mbox,$X,$part["part_number"]);
			} else if ($part["part_object"]->type==5) {
				$r["attachment"]["filename"] = $part["part_object"]->dparameters[0]->value;
				$r["attachment"]["string"] = imap_fetchbody($mbox,$X,$part["part_number"]);
			}
		}
		if ($must_delete) {
			imap_delete($mbox, $X);
		}
	}
	
	if ($must_delete) {
		imap_expunge($mbox);
	}
	imap_close($mbox);
	return $r;
}

function mail_attachment($data, $mailto, $from_mail, $from_name, $replyto, $subject) {
    $filename = $data["attachment"]["filename"];
    $content = $data["attachment"]["string"];
	$foto = NotAProfile::subirFotoEmail($content);
	$textile = new Textile();
	$texto = $textile->TextileThis($data["text"]);
	$codigo = NotAProfile::crearLlave(1,1,$texto,$foto); // luego sacar lat/long desde el EXIF
    
	$uid = md5(uniqid(time()));
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $data["text"]."\r\n\r\n";
    $header .= "http://www.notaprofile.com/key/".$codigo."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    if (mail($mailto, $subject, "", $header)) {
    } else {
    }
}

function create_part_array($struct) {
    if (sizeof($struct->parts) > 0) {    // There some sub parts
        foreach ($struct->parts as $count => $part) {
            add_part_to_array($part, ($count+1), $part_array);
        }
    }else{    // Email does not have a seperate mime attachment for text
        $part_array[] = array('part_number' => '1', 'part_object' => $struct);
    }
   return $part_array;
}

// Sub function for create_part_array(). Only called by create_part_array() and itself.
function add_part_to_array($obj, $partno, & $part_array) {
    $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
    if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
        //print_r($obj);
        if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
            foreach ($obj->parts as $count => $part) {
                // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                if (sizeof($part->parts) > 0) {
                    foreach ($part->parts as $count2 => $part2) {
                        add_part_to_array($part2, $partno.".".($count2+1), $part_array);
                    }
                }else{    // Attached email does not have a seperate mime attachment for text
                    $part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
                }
            }
        }else{    // Not sure if this is possible
            $part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
        }
    }else{    // If there are more sub-parts, expand them out.
        if (sizeof($obj->parts) > 0) {
            foreach ($obj->parts as $count => $p) {
                add_part_to_array($p, $partno.".".($count+1), $part_array);
            }
        }
    }
}

?>