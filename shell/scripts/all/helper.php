<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/28/15
 * Time: 11:40 AM
 */

function toFile($directoryExport, $fileName, $fileType, $data)
{
	if (!$data)
	{
		return;
	}
	$currentDate = date('Y-m-d');
	$myfile = fopen($directoryExport . $currentDate . "_" . $fileName . $fileType, "w") or die("Unable to open file!");
	fwrite($myfile, $data);
	fclose($myfile);
}