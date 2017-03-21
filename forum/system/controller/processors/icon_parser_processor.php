<?php
function emoticons($text) {
        $icons = array(
				' :3'    =>  ' <img src="./images/emoticons/Smiley1.png" alt=" :3">',
				' :)'    =>  ' <img src="./images/emoticons/Smiley2.png" alt=" :)">',
				' ^^'    =>  ' <img src="./images/emoticons/Smiley3.png" alt=" ^^">',
				' 3>'    =>  ' <img src="./images/emoticons/Smiley4.png" alt=" 3>">',
				' x3'    =>  ' <img src="./images/emoticons/Smiley5.png" alt=" x3">',
                ' Dx'    =>  ' <img src="./images/emoticons/Smiley6.png" alt=" Dx">',	
				' <D'    =>  ' <img src="./images/emoticons/Smiley7.png" alt=" <D">',	
				' :D'    =>  ' <img src="./images/emoticons/Smiley8.png" alt=" :D">',	
				' D:'    =>  ' <img src="./images/emoticons/Smiley9.png" alt=" D:">', 
				' <3'    =>  ' <img src="./images/emoticons/Smiley17.png" alt=" <3">'				
        );
        return strtr($text, $icons);
    }
?>