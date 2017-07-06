<?php
class Mail extends CApplicationComponent
{

	public $host = "";
	public $port = 25;
	public $user = "";
	public $password = "";
	public $from = "";
	public $fromName = "";

	public function send($to, $subject, $content, $attachmentPath = array())
	{
		$mail = new PHPMailer;

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $this->host;  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $this->user;                 // SMTP username
		$mail->Password = $this->password;                           // SMTP password
		$mail->Port = $this->port;
		//$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

		$mail->From = $this->from;
		$mail->FromName = $this->fromName;

		//支持多个收件人
		$tos = array_filter(explode(';', $to), function($item) {
			return strpos($item, '@') > 0;//过滤不合规的email
		});

		foreach ($tos as $receiver)
		{
			$type = 'to';
			if (strpos($receiver, ':') !== false)
			{
				list($type, $receiver) = explode(':', $receiver);
			}

			switch (strtolower($type))
			{
				case 'cc';//抄送
					$mail->addCC($receiver);
					break;
				case 'bcc';//密送
					$mail->addBCC($receiver);
					break;
				default:
					$mail->addAddress($receiver);
			}
		}


//		$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
//		$mail->addAddress($to);               // Name is optional
//		$mail->addReplyTo('info@example.com', 'Information');
//		$mail->addCC('cc@example.com');
//		$mail->addBCC('bcc@example.com');

//		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		if (!empty($attachmentPath) )
		{
			foreach($attachmentPath as $val)
			{
				$mail->addAttachment($val);         // Add attachments
			}
		}
//		$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//		$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->CharSet = "utf-8";
		//$mail->SMTPDebug = 4;
		$mail->SMTPKeepAlive = true;
		//$mail->Debugoutput = "echo";
		$mail->Timeout = 300;


		$mail->Subject = $subject;
		$mail->Body    = $content;
//		$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if($mail->send())
		{
			return LError::SUCCESS;
		}
		else
		{
			throw new LException(LError::MT_SENDMAIL_FAILD, $mail->ErrorInfo);
		}
	}
}