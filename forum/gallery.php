<?php

global $langGlobal;

// Re initialize the DB and Runtime Class
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (!isset($gallery) || $gallery == NULL)
{
	$main->useFile('./system/classes/akb_gallery.class.php', 1);
	$gallery = new Gallery($db, $connection, $main);
}

if (!isset($_GET['action'])) {
    
    // Show Gallery Browser if no image was selected
    if (!isset($_GET['Image']) || empty($_GET['Image'])) {
        /*
        VARIABLES BEGIN
        */
        
        $results_per_page = '25';
        
        /*
        VARIABLES END
        */
        
        if (!isset($_COOKIE['akb_gallery_last_visit'])) {
            echo '
<div class="userInfobox">
	<div class="userInfobox_inner">
		<div class="userInfobox_img">
			<div class="icons_big" id="information"></div>
		</div>
		<div>
			' . $langGlobal['gallery_lang_welcome'] . '
		</div>
	</div>
</div>';
            setcookie('akb_gallery_last_visit', time(), time() + (60 * 60 * 24 * 365));
        }
        
        // Use language specific lang file.
        $main->useFile('./system/controller/processors/lang_processor.php', 1);
        
        $gallery_header = '';
        $gallery_header .= '
<div class="mainHeadline">
    <div class="icons" id="forumiconMain"></div>
    <div class="headlineContainer">
      <h1>
        Galerie
      </h1>
    </div>
	<p>. . . eine Galerie für alle Fotografen</p>
</div>
<div class="contentHeader">';
        // Only show upload button and form, if logged in
        if (isset($_SESSION['STATUS']) && $_SESSION['STATUS'] == true) {
            $gallery_header .= '
	<div class="largeButtons">
		<ul>
			<li>
				<a id="submitImage" title="Bild hochladen" class="no-smoothstate">
					<div class="icons" id="upload"></div>
					<span>
						Bild hochladen
					</span>
				</a>
			</li>
		</ul>
	</div>
	<div class="uploadForm">
		<div class="uploadForm_header">
			<div class="icons" id="upload"></div>
			<span>
				Bild hochladen
			</span>
		</div>
		<div class="uploadForm_content">
		<form enctype="multipart/form-data" method="POST" action="?page=Gallery&action=submitImage" id="galleryUploadForm" class="no-smoothstate">
				<span>Die maximale Uploadgröße beträgt 5MB</span>
				<input type="file" name="image" id="file" accept="image/*" onchange="updateImage(this)" /><br>
				<!--<input type="hidden" name="test" value="FILEINPT" />-->
				<input type="submit" value="Weiter" id="imageUploadContinue" disabled="1" />
		</form>
		<img id="uploadPreview">
		</div>
	</div>';
        }
        $gallery_header .= '
</div>
';
        
        echo $gallery_header;
        
        $gallery_body = '';
        $gallery_body .= '
<div class="galleryBody">
	<div class="galleryPages">';
        
        for ($i = 1; $i <= 9; $i++) {
            $gallery_body .= '
				<a href="?page=Gallery&galleryPage=' . $i . '">' . $i . '</a>
			';
        }
        
        $gallery_body .= '
	</div>
	<center>
	<div class="galleryContent">';
        
        if (isset($_GET['galleryPage']) && !empty($_GET['galleryPage'])) {
            $limit_start = $_GET['galleryPage'] * $results_per_page - $results_per_page;
        } else {
            $limit_start = '0';
        }
        
        
        
        // Read all images, that were registered in DB
        $imageQuery = $db->query("SELECT gallery.id, gallery.img_name, gallery.img_display_name, gallery.img_description, gallery.uploaded_by_id, accdata.username FROM $db->table_gallery_data gallery, $db->table_accdata accdata WHERE gallery.id AND accdata.account_id=gallery.uploaded_by_id ORDER BY gallery.id DESC LIMIT " . mysqli_real_escape_string($GLOBALS['connection'], $limit_start) . ", " . mysqli_real_escape_string($GLOBALS['connection'], $results_per_page) . "") or die(mysqli_error($GLOBALS['connection']));
        while ($processImages = mysqli_fetch_object($imageQuery))
		{
            
            $image_id            = $processImages->id;
            $image_name          = $processImages->img_name;
            $image_display_name  = $processImages->img_display_name;
            $image_description   = $processImages->img_description;
            $image_uploader_id   = $processImages->uploaded_by_id;
            $image_uploader_name = $processImages->username;
            
            // Split filename to 2 separate parts. Name and extension
            $actualUser = $main->getUsername($image_uploader_id);
            $imagePath  = $gallery->getUserDir($actualUser);
			
            
			$imageParts = explode("-", $image_name);
			$image_base = $imageParts[1];
			
			$image_name = "thumb-".$image_base;
			
            $image_split = explode('.', $imagePath . $image_name);
			$image_path_ext = $image_split[0];
            $image_name  = $image_split[1];
            $image_ext   = $image_split[2];
		
		$img_src = $gallery->validateImage(".".$image_name . '.' . $image_ext);
		
            
            // @ !TODO: Thumbnail support [DONE]
            $gallery_body .= '
		<div class="galleryRow" id="' . $image_id . '">
			<a href="?page=Gallery&Image=' . $image_id . '" title="' . $image_description . '">
			<img src="'.$img_src.'">
			<p>
				<a href="?page=Gallery&Image=' . $image_id . '">
					' . $image_display_name . '
				</a>
			</p>
			<p>';
            $gallery_body .= $langGlobal['gallery_uploaded_by'];
            $gallery_body .= '
				<a href="?page=Profile&User=' . $image_uploader_id . '">
					' . $image_uploader_name . '
				</a>
			</p>
			</a>
		</div>';
        }
        
		if(mysqli_num_rows($imageQuery) <= 0)
		{
			$gallery_body .= "Die Galerie ist leer!<br>Schnell, füge Bilder hinzu!";
		}
        
        
        $gallery_body .= '
	</div>
	</center>
<div class="galleryPages">';
        
        for ($i = 1; $i <= 9; $i++) {
            $gallery_body .= '
				<a href="?page=Gallery&galleryPage=' . $i . '">' . $i . '</a>
			';
        }
        
        $gallery_body .= '
	</div>
</div>
';
    }
    
    /*
    IMAGE VIEWER BEGIN
    */
    if (isset($_GET['Image']) || !empty($_GET['Image'])) {
        
        $imageID          = mysqli_real_escape_string($GLOBALS['connection'], $_GET['Image']);
        $actual_link      = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        
        if (!$pageWasRefreshed) {
            $db->query("UPDATE $db->table_gallery_data SET views=views+1 WHERE id=('" . $imageID . "')");
        }
        
        $actualImage = mysqli_real_escape_string($GLOBALS['connection'], $_GET['Image']);
        $getData = $db->query("SELECT gallery.id, gallery.img_name, gallery.img_display_name, gallery.img_description, gallery.uploaded_by_id, gallery.upload_time, gallery.views, gallery.comments, gallery.favorites, gallery.category, gallery.theme, gallery.rating, accdata.username, accdata.avatar FROM $db->table_gallery_data gallery, $db->table_accdata accdata WHERE gallery.id = ('" . $actualImage . "') AND accdata.account_id=gallery.uploaded_by_id ORDER BY gallery.id DESC LIMIT 1") or die(mysqli_error($GLOBALS['connection']));
        while ($resolveData = mysqli_fetch_object($getData)) {
            $image_id              = $resolveData->id;
            $image_name            = $resolveData->img_name;
            $image_display_name    = $resolveData->img_display_name;
            $image_description     = $resolveData->img_description;
            $image_uploader_id     = $resolveData->uploaded_by_id;
            $image_upload_time     = $resolveData->upload_time;
            $image_views           = $resolveData->views;
            $image_comments        = $resolveData->comments;
            $image_favs            = $resolveData->favorites;
            $image_category_id     = $resolveData->category;
            $image_theme_id        = $resolveData->theme;
            $image_rating_id       = $resolveData->rating;
            $image_uploader_name   = $resolveData->username;
            $image_uploader_avatar = $resolveData->avatar;
        }
        
        $actualUser = $main->getUsername($image_uploader_id);
		$img_src = $gallery->validateImage($gallery->getUserDir($actualUser) . $image_name);
        
        $image_category = $gallery->processCategory($image_category_id);
        $image_theme    = $gallery->processTheme($image_theme_id);
        $image_rating   = $gallery->processRating($image_rating_id);
        
        $image_upload_time = $main->convertTime($image_upload_time);
        
        if (isset($_SESSION['ID'])) {
            $userid   = $_SESSION['USERID'];
            
            
            if ($image_uploader_id == $userid) {
                $categories   = $gallery->getOption("category");
                $categoryData = '<select id="categoryChanger">';
                $i            = 0;
                foreach ($categories as $categoryIndex) {
                    if ($i != $image_category_id)
                        $categoryData .= '<option value="' . $i . '">' . $categoryIndex . '</option>';
                    else
                        $categoryData .= '<option value="' . $i . '" selected="selected">' . $categoryIndex . '</option>';
                    $i++;
                }
                $categoryData .= '/<select>';
                
                $themes    = $gallery->getOption("theme");
                $themeData = '<select id="themeChanger">';
                $i         = 0;
                foreach ($themes as $themeIndex) {
                    if ($i != $image_theme_id)
                        $themeData .= '<option value="' . $i . '">' . $themeIndex . '</option>';
                    else
                        $themeData .= '<option value="' . $i . '" selected="selected">' . $themeIndex . '</option>';
                    $i++;
                }
                $themeData .= '/<select>';
                
                $rating     = $gallery->getOption("rating");
                $ratingData = '<select id="ratingChanger">';
                $i          = 0;
                foreach ($rating as $ratingIndex) {
                    if ($i != $image_rating_id)
                        $ratingData .= '<option value="' . $i . '">' . $ratingIndex . '</option>';
                    else
                        $ratingData .= '<option value="' . $i . '" selected="selected">' . $ratingIndex . '</option>';
                    $i++;
                }
                $ratingData .= '/<select>';
            } else {
                $categoryData = '<i><u><b>Kategorie:</b></u></i><br><span>' . $image_category . '</span>';
                $themeData    = '<i><u><b>Thema:</b></u></i><br><span>' . $image_theme . '</span>';
                $ratingData   = '<i><u><b>Rating:</b></u></i><br><span>' . $image_rating . '</span>';
            }
        } else {
            $categoryData = '<i><u><b>Kategorie:</b></u></i><br><span>' . $image_category . '</span>';
            $themeData    = '<i><u><b>Thema:</b></u></i><br><span>' . $image_theme . '</span>';
            $ratingData   = '<i><u><b>Rating:</b></u></i><br><span>' . $image_rating . '</span>';
        }
        
        include('./system/classes/akb_simple_image.class.php');
        $simage       = new SimpleImage();
        $imageRes_src = $simage->load($img_src);
        $imageRes_x   = $simage->getWidth();
        $imageRes_y   = $simage->getHeight();
        
        $imageRes = $imageRes_x . "x" . $imageRes_y;
        
        $gallery_body = '
	<div class="galleryBody">
		<div class="galleryImage">
			<h2 class="fancy_font">' . $image_display_name . '</h2>
			<img src="' . $img_src . '">
		</div>
		
		<table class="galleryImageInfo">
			<tbody>
				<tr>
					<td class="imageUploaderInformation">
						<div class="imageUploader">
							<div class="imageUploaderInner">
								<p>
									' . $image_uploader_name . '
								</p>
								<img src="' . $image_uploader_avatar . '" />
							</div>
						</div>
						<div class="imageDescription">
							<div class="imageUploadTime">
							<p class="imageUploadTimeCon">
								' . $image_upload_time . '
							</p>
							</div>
							' . $image_description . '
						</div>
					</td>
					<td class="imageInformation" align="right" id="imageInformation">
							<ul>
								<li>
									<i><u><b>Hochgeladen:</b></u></i><br><span>' . $image_upload_time . '</span>
								</li>
								<li>
									<i><u><b>Ansichten:</b></u></i><br><span>' . $image_views . '</span>
								</li>
								<li>
									<i><u><b>Kommentare:</b></u></i><br><span>n.A</span>
								</li>
								<li>
									<i><u><b>Favoriten:</b></u></i><br><span>n.A</span>
								</li>
								<li>
									<i><u><b>Auflösung:</b></u></i><br><span>' . $imageRes . '</span>
								</li>
								<li>
									' . $categoryData . '
								</li>
								<li>
									' . $themeData . '
								</li>
								<li>
									' . $ratingData . '
								</li>
							</ul>
					</td>
				<tr>
			<tbody>
		</table>
		<div class="galleryCommentsection">
			<h2>
				Kommentare zu diesem Bild
			</h2>
	';
        
        // <<< COMMENTS >>>
        
        $getComments = $db->query("SELECT comments.user_id, comments.time_posted, comments.comment, accdata.avatar, accdata.username FROM $db->table_gallery_comments comments, $db->table_accdata accdata WHERE comments.image_id = ('" . $actualImage . "') AND accdata.account_id = comments.user_id ORDER BY comments.time_posted DESC");
        
        while ($comments = mysqli_fetch_object($getComments)) {
            $user_id     = $comments->user_id;
            $time_posted = $comments->time_posted;
            $comment     = $comments->comment;
            $user_avatar = $comments->avatar;
            $username    = $comments->username;
            
            $time_posted = $main->convertTime($time_posted);
            
            $gallery_body .= '
		<table class="galleryImageInfo galleryCommentRow">
			<tbody>
				<tr>
					<td class="imageUploaderInformation">
						<div class="imageUploader">
							<div class="imageUploaderInner">
								<p>
									' . $username . '
								</p>
								<img src="' . $user_avatar . '">
							</div>
						</div>
						<div class="imageDescription">
							<div class="imageUploadTime">
							<p class="imageUploadTimeCon">
								' . $time_posted . '
							</p>
							</div>
							' . $comment . '
						</div>
					</td>
					
				</tr></tbody><tbody>
		</tbody></table>
	';
        }
        
        $gallery_body .= '
	<div class="galleryCommentwrite">
	<h3>Einen Kommentar verfassen</h3>
		<textarea></textarea>
	</div>
		</div>
	</div>
	';
    }
    /*
    IMAGE VIEWER END
    */
    
} else {
    
    $main->useFile('./system/controller/processors/gallery_upload_processor.php');
    
}

if (isset($gallery_body) && $gallery_body != NULL)
    echo $gallery_body;

?>