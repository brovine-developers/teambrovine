<?php

/**
 *  http://stackoverflow.com/a/118886/444810
 * 
 *  Given a file, i.e. /css/base.css, replaces it with a string containing the
 *  file's mtime, i.e. /css/base.1221534296.css.
 * 
 *  **MUST** be accompanied by a RewriteEngine change to parse out the mtime string!
 *  * RewriteEngine on
 *  * RewriteRule ^(.*)\.[\d]{10}\.(css|js)$ $1.$2 [L]
 *  
 *  @param $file  The file to be loaded.  Must be an absolute path (i.e.
 *                starting with slash).
 */
function version($file)
{
	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $file))
    	return $file;

	$mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . '/' . $file);
	return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
}

?>