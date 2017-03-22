<?php
class Mail
{
	private $imap_host = 'vweb17.nitrado.net';
	private $imap_port = '110';
	private $imap_user = 'admin@baneofthelegion.de';
	private $imap_pass = '23jdfsjkdsfdss';
	private $imap_parameter = '/pop3/novalidate-cert';
	
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
		if(!imap_open("{".$this->imap_host.":".$this->imap_port.$this->imap_parameter."}", $this->imap_user, $this->imap_pass))
			{
				echo 'Beim senden der Verifizierungsmail ist ein schwerwiegender Fehler aufgetreten. Bitte wenden Sie sich an die Administration.';
				// print_r(imap_errors());
				return;
			}
	
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
				<html>
					<head>
						<meta name="Content-Type" content="text/html; charset=utf-8">
						<meta http-equiv="content-type" content="text/html; charset=utf-8">
						<style>
							@import url(https://fonts.googleapis.com/css?family=Josefin+Sans:400,400italic,700&subset=latin,latin-ext);
							.biglink {
								font-size:1.2em;
								color:#DDD;
							}
							.main, .footer {
								padding:11px;background:rgba(0, 0, 0, 0.75);
								border-radius:11px;
								width:70%;
								margin:100px auto;
							}
							body {
								background:url("http://baneofthelegion.de/img/bg/highmountain.jpg") center no-repeat,rgb(13,16,12);
								background-attachment:fixed;
								background-size:cover;
								font-family: "Josefin Sans",sans-serif;
								color:rgb(42, 154, 59);
								text-shadow:1px 1px 3px rgba(38, 84, 17, 0.71);
							}
							.header {
								background:url("http://baneofthelegion.de/img/gfx/logo_2_small.png") top center no-repeat;
								width:350px;
								height:127px;
								background-size:350px 127px;
								padding-bottom:12px;
								margin-bottom:12px;
								border-bottom: 2px groove rgb(40, 95, 40);
								width:100%;
							}
							.title, .message {
								text-align: left;
								margin:25px;
							}
							.message {
								padding-bottom:12px;
								border-bottom: 2px groove rgb(40, 95, 40);
							}
							.confirmAccount {
								font-size:1.15em;
								font-weight:bold;
							}
							.btn {
							  margin-top: 25px;
							  background: #6fc750;
							  background-image: -webkit-linear-gradient(top, #6fc750, #3d692e);
							  background-image: -moz-linear-gradient(top, #6fc750, #3d692e);
							  background-image: -ms-linear-gradient(top, #6fc750, #3d692e);
							  background-image: -o-linear-gradient(top, #6fc750, #3d692e);
							  background-image: linear-gradient(to bottom, #6fc750, #3d692e);
							  -webkit-border-radius: 6;
							  -moz-border-radius: 6;
							  border-radius: 6px;
							  -webkit-box-shadow: 0px 1px 3px #666666;
							  -moz-box-shadow: 0px 1px 3px #666666;
							  box-shadow: 0px 1px 3px #666666;
							  font-family: Arial;
							  color: #d1d1d1;
							  font-size: 20px;
							  padding: 10px 20px 10px 20px;
							  border: solid #438c1f 2px;
							  text-decoration: none;
							}

							.btn:hover {
							  background: #8bc7a5;
							  background-image: -webkit-linear-gradient(top, #8bc7a5, #35754b);
							  background-image: -moz-linear-gradient(top, #8bc7a5, #35754b);
							  background-image: -ms-linear-gradient(top, #8bc7a5, #35754b);
							  background-image: -o-linear-gradient(top, #8bc7a5, #35754b);
							  background-image: linear-gradient(to bottom, #8bc7a5, #35754b);
							  text-decoration: none;
							  cursor: pointer;
							}
							
							.footer {
								margin-top:175px;
								font-size:0.85em;
								width:50%;
							}
						</style>
					</head>
					<body>
						<center>
							<div class="main">
								<div class="header">
								</div>
								<div class="content">
									<h2 class="title">
										'.$_SESSION['USERNAME'].', 
									</h2>
									<p class="message">
										wir heißen dich herzlichst Willkommen in unserem Hauseigenen Forum!<br>
										Bevor du jedoch loslegen und dich mit anderen Legionsflüchen abgeben darfst, musst du ledliglich eine Würdigkeit und mentale Existenz unter Beweis stellen!
									</p>
									<div class="confirmAccount">
										Durch einen Klick auf den unten zu findenden Button erklärst du dich, wie schon in der Registrierung auch, mit unseren Nutzungsbestimmungen einverstanden.
									</div>
									<form action="http://www.baneofthelegion.de/forum/?page=Portal&action=validuser&token='.$token.'">
										<input type="submit" value="Account verifizieren!" class="btn">
									</form>
								</div>
							</div>
							<div class="footer">
								"Bane of the Legion" ist ein fiktiver Zusammenschluss im MMORPG "World of Warcraft".<br>
								<br>
								Diese Mail dient einzig und allein der Information und Verifikation der dazugehörigen Personen.<br>
								Haben Sie diese E-Mail fälschlicherweise erhalten, leiten Sie diese bitte an "admin@baneofthelegion.de" weiter.
							</div>
						</center>
					</body>
				</html>
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