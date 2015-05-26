<?php

namespace Lib;

use Phalcon\Validation\Validator;

class File extends \Phalcon\Di\Injectable {


	const DIR_FILES = __DIR__.'/../files/';

	public function printFile($filePath)
	{
		$filePath = self::DIR_FILES.$filePath;

		if ( !file_exists($filePath) ) {
			header('HTTP/1.1 404 Not Found');
			return false;
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $filePath);
		$matches = explode('/', $filePath);
		$name = end($matches);
		$mtime = filemtime($filePath);

        //If they sent a If-Modified-Since header, compare it to the file's last modified time. 
        if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || $mtime > strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])){ 
            //Our modified time is newer, send our copy so they update the cache. 
            //[snip error handling]             
            header('Content-type: '.$mime); 
            header('Content-length: '.filesize($filePath)); 
          
            //This copy is valid for 5 days. 
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time()+(24*60*60*5))); 
             
            //This copy was last modified today. 
            header('Last-modified: '.gmdate('D, d M Y H:i:s \G\M\T')); 

            header('Content-Disposition: inline; filename="'.$name.'"');
            
            readfile($filePath);
            return true;
        } 
        else { 
            //Their copy is still good.  Tell them that with a 304, then exit. 
            header('HTTP/1.1 304 Not Modified'); 
            return true;
        } 

        return true;
	}

}