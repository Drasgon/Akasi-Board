<?php
class Mail
{
	private $imap_host = 'vweb17.nitrado.net';
	private $imap_port = '143';
	private $imap_user = 'admin@baneofthelegion.de';
	private $imap_pass = 'f89f9224';
	
	private $fromSubject = "Gildenleitung | Bane of the Legion";
	private $from = "admin@baneofthelegion.de";
	private $subject = "Mitgliedsinformation";
	public $to;
	private $html;
	private $headers;
	
	public function __construct($data_to, $subject = '', $header_content = '', $message = '')
	{
		if(!empty($subject))
			$this->subject = $subject;
		imap_open("{".$this->imap_host.":".$this->imap_port."}", $this->imap_user, $this->imap_pass) or die('Cannot connect to Host: ' . imap_last_error());
	
		$this->to = $data_to;
		
		if(empty($header_content))
			$header_content = '
				<style>
					p {
						color:#FFF;
					}
					.biglink {
						font-size:1.2em;
						color:#DDD;
					}
				</style>
			';
		
		if(empty($message))
			$message = '
				<center style="padding:11px;background:rgba(0, 0, 0, 0.75);border-radius:11px;width:70%;margin:100px auto;">
					<p>
						Vielen Dank, für die Registrierung im Forum von Bane of the Legion, $n!
					</p>
					<p>
						Um ihren Account freizuschalten, klicken Sie auf den unten stehenden Link.
					</p>
					<p class="biglink">
						<a href="http://www.baneofthelegion.de/forum/?page=Portal&action=validuser&token=3483ohfjdhssdfiu6h49g3f4gv9fb23b89n6459b68g567956o98h574n8975g34756d3485g4569456j890g645g675g67dfj8t45t84ui348zr48tzr">Authentifizieren</a>
					</p>
				</center>
			';
		$this->html = '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://ww=w.w3.org/TR/html4/loose.dtd">
		<html style="background:url(\'http://www.baneofthelegion.de/img/bg/bg_poster.jpg\')">
			<head>
				'.$header_content.'
			</head>
			<body>
				'.$message.'
			</body>
		</html>';
	}
	
	public function set_default_headers()
	{
		$this->headers  = 'From: "'.$this->fromSubject.'" <'.$this->from.'>' . "\r\n";
		$this->headers .= 'MIME-Version: 1.0' . "\r\n";
		$this->headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$this->headers .= 'X-Mailer: PHP/' . phpversion();
	}

	public function send_imap_mail()
	{
		$mail = imap_mail($this->to, $this->subject, $this->html, $this->headers);
		
		if(!$mail)
			return false;
		else
			return true;
	}



}
?>