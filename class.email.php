<?php
	require_once('phpmailer/class.phpmailer.php');
	require ('phpmailer/PHPMailerAutoload.php');

	function decryptor($encText) {
		$key = pack("H*", "cf372282683d4802ee035e793218e2e4a8a8eb4f6a1d5675b6a6a289c860abde");

		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);

		$cipherText = base64_decode($encText);

		$iv = substr($cipherText, 0, $iv_size);

		$cipherText = substr($cipherText, $iv_size);

		$text = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cipherText, MCRYPT_MODE_CBC, $iv);

		$block = mcrypt_get_block_size("rijndael_256", "cbc");
		$pad = ord($text[($len = strlen($text)) - 1]);

		$text = substr($text, 0, strlen($text) - $pad);

		return $text;
	}

	class email {
		private $strFromMail = "owaes@owaes.org";
		private $strFromName = "OWAES";
		private $strToMail = "owaes@owaes.org";
		private $strToName = "OWAES";
		private $strSubject = "OWAES";
		private $strMessage = "";
		private $oTemplate = NULL;

		public function email($strTo = "", $strSubject = "", $strMessage = "") {
			$this->template("[body]");
			if ($strTo != "") {
				$this->strToMail = $strTo;
				$this->strToName = $strTo;
			}
			if ($strSubject != "") $this->strSubject = $strSubject;
			if ($strMessage != "") $this->strMessage = $strMessage;
			if (($strTo != "") && ($strMessage != "")) $this->send();
		}

		public function setTo($strMail, $strName = "") {
			$this->strToMail = $strMail;
			$this->strToName = ($strName == "") ? $strMail : $strName;
		}

		public function setBody($strHTML) {
			$this->strMessage = $strHTML;
		}

		public function template($strTemplate = NULL) {
			if (!is_null($strTemplate)) $this->oTemplate = template($strTemplate);
			return $this->oTemplate;
		}

		public function setSubject($strSubject) {
			$this->strSubject = $strSubject;
		}

		public function send() {
			$oTemplate = $this->template();
			$oTemplate->tag("body", $this->strMessage);
			$strMessage = $oTemplate->html();

			if (settings("mail", "smtp")) {
				$oPHPmailer = new PHPMailer();

				$oPHPmailer->IsSMTP();

				//$oPHPmailer->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
				//  $oPHPmailer->Debugoutput = 'html';
				$oPHPmailer->Host       = settings("mail", "Host"); // "smtp.gmail.com";      // sets GMAIL as the SMTP server
				$oPHPmailer->SMTPAuth   = settings("mail", "SMTPAuth"); // true;                  // enable SMTP authentication
				$oPHPmailer->SMTPSecure = settings("mail", "SMTPSecure"); // "ssl";                 // sets the prefix to the servier
				$oPHPmailer->Port       = settings("mail", "Port"); // 465;                   // set the SMTP port for the GMAIL server
				$oPHPmailer->Username   = settings("mail", "Username");   // GMAIL username
				$oPHPmailer->Password   = decryptor(settings("mail", "Password"));             // GMAIL password

				$oPHPmailer->SetFrom($this->strFromMail, $this->strFromName);
				$oPHPmailer->AddAddress($this->strToMail, $this->strToName);
				$oPHPmailer->Subject    = $this->strSubject;
				$oPHPmailer->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test


				$oPHPmailer->MsgHTML($strMessage);

				if(!$oPHPmailer->Send()) {
					//echo "Mailer Error: " . $oPHPmailer->ErrorInfo;
					return FALSE;
				} else {
					//echo "Message sent!";
					return TRUE;
				}

			} else {

				$strHeaders  = 'MIME-Version: 1.0' . "\r\n";
				$strHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$strHeaders .= 'To: ' . $this->strToName . ' <' . $this->strToMail . '>' . "\r\n";
				//$strHeaders .= 'From: ' . $this->strFromName . ' <' . $this->strFromMail . '>' . "\r\n";

				mail($this->strToMail, $this->strSubject, $strMessage, $strHeaders);
			}

 		}


	}

