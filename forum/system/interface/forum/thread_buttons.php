<?php
function generateButtons($currentThread, $subMsgTitle, $subMsgImage, $threadExists)
{

$html = '';

if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

	if(isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true || $main->boardConfig($main->getThreadBoardID($currentThread), "guest_posts"))
		{
		$html .= '
			<div class="largeButtons">
				<ul>
					<li>
						<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;form=postAdd" id="replyButton1" title="Antworten">
							<img src="images/icons/threadAdd-Msg.png" alt="">

							<span>
								Antworten
							</span>
						</a>
					</li>';
		}
		
		if (isset($_SESSION['angemeldet']) && $_SESSION['angemeldet'] == true && isset($threadExists) && $threadExists == true)
		{
		
		$html .= '
					<li>
						<a href="?page=Index&amp;threadID=' . $currentThread . '&amp;action=threadSub" id="subButton1" title="Thread abonnieren und damit dynamische Benachrichtigungen bei neuen Beiträgen erhalten.">
							<img src="' . $subMsgImage . '" alt="">

							<span>
								' . $subMsgTitle . '
							</span>
						</a>
					</li>
				</ul>';
		}
		
		$html .= '
			</div>
		';
		
	return $html;
}
?>