<?php
class SimpleImage

	{
	var $image;
	var $image_type;
	function load($filename)
		{
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if ($this->image_type == IMAGETYPE_JPEG)
			{
			$this->image = imagecreatefromjpeg($filename);
			}
		elseif ($this->image_type == IMAGETYPE_GIF)
			{
			$this->image = imagecreatefromgif($filename);
			}
		elseif ($this->image_type == IMAGETYPE_PNG)
			{
			$this->image = imagecreatefrompng($filename);
			imagealphablending($this->image, true);
			imagesavealpha($this->image, true);
			}
		}

	function save($filename, $image_type = 'png', $compression = 95, $permissions = null)
		{
		if ($image_type == 'jpeg' || $image_type == 'jpg')
			{
			imagejpeg($this->image, $filename, $compression);
			}
		elseif ($image_type == 'gif')
			{
			imagegif($this->image, $filename);
			}
		elseif ($image_type == 'png')
			{
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);

				$trans_layer_overlay = imagecolorallocatealpha($this->image, 220, 220, 220, 127);
				imagefill($this->image, 0, 0, $trans_layer_overlay);
			imagepng($this->image, $filename);
			}

		if ($permissions != null)
			{
			chmod($filename, $permissions);
			}
		}

	function output($image_type = IMAGETYPE_JPEG)
		{
		if ($image_type == IMAGETYPE_JPEG)
			{
			imagejpeg($this->image);
			}
		elseif ($image_type == IMAGETYPE_GIF)
			{
			imagegif($this->image);
			}
		elseif ($image_type == IMAGETYPE_PNG)
			{
			imagepng($this->image);
			}
		}

	function getWidth()
		{
		return imagesx($this->image);
		}

	function getHeight()
		{
		return imagesy($this->image);
		}

	function resizeToHeight($height)
		{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
		}

	function resizeToWidth($width)
		{
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width, $height);
		}

	function scale($scale)
		{
		$width = $this->getWidth() * $scale / 100;
		$height = $this->getheight() * $scale / 100;
		$this->resize($width, $height);
		}

	function resize($width, $height, $x = 0, $y = 0)
	{
	$new_image = imagecreatetruecolor($width, $height);
	if ($this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG)
		{
		$current_transparent = imagecolortransparent($this->image);
		if ($current_transparent != - 1)
			{
			$transparent_color = imagecolorsforindex($this->image, $current_transparent);
			$current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
			imagefill($new_image, 0, 0, $current_transparent);
			imagecolortransparent($new_image, $current_transparent);
			}
		elseif ($this->image_type == IMAGETYPE_PNG)
			{
			imagealphablending($new_image, false);
			$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
			imagefill($new_image, 0, 0, $color);
			imagesavealpha($new_image, true);
			}
		}
	
	
	imagecopyresampled($new_image, $this->image, 0, 0, $x, $y, $width, $height, $this->getWidth() , $this->getHeight());
	$this->image = $new_image;
	}
	}
?>