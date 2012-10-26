<?php
if(!defined('_FOO_'))
{
	define('_FOO_', 1);
	class SimpleImage {
		var $image;
		var $image_type;
		
		function load($filename)
		{
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];
			if( $this->image_type == IMAGETYPE_JPEG )
			{
				$this->image = imagecreatefromjpeg($filename);
			}
			elseif( $this->image_type == IMAGETYPE_GIF )
			{
				$this->image = imagecreatefromgif($filename);
			}
			elseif( $this->image_type == IMAGETYPE_PNG )
			{
				$this->image = imagecreatefrompng($filename);
			}
		}
		
		function save($filename, $image_type, $compression=75, $permissions=null)
		{
			if( $image_type == IMAGETYPE_JPEG )
			{
				imagejpeg($this->image,$filename,$compression);
			}
			elseif( $image_type == IMAGETYPE_GIF )
			{
				imagegif($this->image,$filename);
			}
			elseif( $image_type == IMAGETYPE_PNG )
			{
				imagepng($this->image,$filename);
			}
			else die("Image type $image_type is unknown");
			if( $permissions != null)
			{
				chmod($filename,$permissions);
			}
		}
		
		function output($image_type=IMAGETYPE_JPEG)
		{
			if( $image_type == IMAGETYPE_JPEG )
			{
				imagejpeg($this->image);
			}
			elseif( $image_type == IMAGETYPE_GIF )
			{
				imagegif($this->image);
			}
			elseif( $image_type == IMAGETYPE_PNG )
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
			$this->resize($width,$height);
		}
		
		function resizeToWidth($width)
		{
			$ratio = $width / $this->getWidth();
			$height = $this->getheight() * $ratio;
			$this->resize($width,$height);
		}
		
		function scale($scale)
		{
			$width = $this->getWidth() * $scale/100;
			$height = $this->getheight() * $scale/100;
			$this->resize($width,$height);
		}
		
		function resize($width,$height)
		{
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
			$this->image = $new_image;
		}
	}
}

/*Usage
Save the above file as SimpleImage.php and take a look at the following examples of how to use the script.
The first example below will load a file named picture.jpg resize it to 250 pixels wide and 400 pixels high and resave it as picture2.jpg
1
2
3
4
5
6
7
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resize(250,400);
   $image->save('picture2.jpg');
If you want to resize to a specifed width but keep the dimensions ratio the same then the script can work out the required height for you, just use the resizeToWidth function.
1
2
3
4
5
6
7
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resizeToWidth(250);
   $image->save('picture2.jpg');
You may wish to scale an image to a specified percentage like the following which will resize the image to 50% of its original width and height
1
2
3
4
5
6
7
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->scale(50);
   $image->save('picture2.jpg');
You can of course do more than one thing at once. The following example will create two new images with heights of 200 pixels and 500 pixels
1
2
3
4
5
6
7
8
9
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resizeToHeight(500);
   $image->save('picture2.jpg');
   $image->resizeToHeight(200);
   $image->save('picture3.jpg');
The output function lets you output the image straight to the browser without having to save the file. Its useful for on the fly thumbnail generation
1
2
3
4
5
6
7
8
   header('Content-Type: image/jpeg');
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resizeToWidth(150);
   $image->output();
The following example will resize and save an image which has been uploaded via a form
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
   if( isset($_POST['submit']) ) {
 
      include('SimpleImage.php');
      $image = new SimpleImage();
      $image->load($_FILES['uploaded_image']['tmp_name']);
      $image->resizeToWidth(150);
      $image->output();
   } else {
 */
?>