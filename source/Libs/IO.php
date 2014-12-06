<?php

class IO
{
	public static function deleteDir($stringPath){
		if(!$handle = @opendir($stringPath)){
			return false;
		}
		while (false !==($file = readdir($handle))){
			if($file !='.' && $file != '..'){
				$tmpdir = $stringPath."/".$file;
				if(is_dir($tmpdir)){
					self::deleteDir($tmpdir);
					rmdir($tmpdir);
				}
				if(is_file($tmpdir)){
					unlink($tmpdir);
				}
			}
		}
		closedir($handle);
	}
}