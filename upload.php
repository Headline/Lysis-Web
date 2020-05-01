<?php
	/**  Lysis Web Decompiler Wrapper
	 *
	 *  Copyright (C) 2017 Michael Flaherty // michaelwflaherty.com // michaelwflaherty@me.com
	 * 
	 * This program is free software: you can redistribute it and/or modify it
	 * under the terms of the GNU General Public License as published by the Free
	 * Software Foundation, either version 3 of the License, or (at your option) 
	 * any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but WITHOUT 
	 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
	 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License along with 
	 * this program. If not, see http://www.gnu.org/licenses/.
	 *
	 **/


	$target_dir = "/home/admin/public_html/lysis/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$type = pathinfo($target_file, PATHINFO_EXTENSION);
	
	if(isset($_POST["submit"])) // submit pressed
	{
		if (is_valid_file($_FILES["fileToUpload"], $type)) // file is valid
		{							
			$output = shell_exec('timeout 30s java -jar lysis-java.jar '.$_FILES["fileToUpload"]["tmp_name"]); // get lysis output
			if ($output == NULL) {
				include('error.html');
				die();
			}
			if (isset($_POST["fileOutput"])) // download to file
			{
				download_output($output, basename($_FILES["fileToUpload"]["name"]));
			}
			else // print to browser
			{
				$cleanoutput = htmlentities($output);
				echo "<pre> $cleanoutput </pre>";
			}
			
			$file = basename($_FILES["fileToUpload"]["name"]).'.txt';
			if (file_exists($file))
				unlink($file); // we're not nsa
		}
		else // ya fucked up
		{
			include('error.html');
		}
	}
	
	function download_output($output, $filename)
	{
		$file = fopen($filename . ".txt", "w");
		
		fwrite($file, $output);
		fclose($file);
		
		
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($filename . ".txt")).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: application/force-download');
		header('Content-Disposition: attachment; filename="'.basename($filename . ".txt").'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($filename . ".txt"));
		header('Connection: close');
		readfile($filename . ".txt");
	}

	function is_valid_file($array, $type)
	{
		$check = filesize($array["tmp_name"]);
		
		if($check == false)
		{
			return false;
		}
		else if ($array["size"] > 500000)
		{
			return false;
		}
		else if(!($type == "smx" || $type == "amxx"))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
?>
