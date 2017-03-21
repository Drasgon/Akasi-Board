<?php
/*
Copyright (C) 2015  Alexander Bretzke

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
Process the uploaded image
*/

// Build MySqli Connection
if (!isset($db) || $db == NULL)
{
    $db = NEW Database();
	$connection = $db->mysqli_db_connect();
}
if (!isset($main) || $main == NULL)
    $main = new Board($db, $connection);

if (!isset($gallery) || $gallery == NULL)
    $gallery = new Gallery($db, $connection);


/*****
 * Initialize the last processing steps.
 * Move image to public directory.
 * The image name is UNIQUE, so there's no danger to use it.
 ******/
$imageName = $gallery->getLastUploaded($main->getUserId());

$imageTitle = mysqli_real_escape_string($connection, $_POST['image_title']);
$imageDesc = mysqli_real_escape_string($connection, $_POST['image_desc']);
$imageTheme = mysqli_real_escape_string($connection, $_POST['image_theme']);
$imageRating = mysqli_real_escape_string($connection, $_POST['image_rating']);
$imageCategory = mysqli_real_escape_string($connection, $_POST['image_category']);

$publish = $gallery->publishImage($imageName, $imageTitle, $imageDesc, $imageTheme, $imageRating, $imageCategory);

if($publish)
echo "Bild wurde erfolgreich veröffentlicht!";
echo '<meta http-equiv="refresh" content="3; url=?page=Gallery">';
?>