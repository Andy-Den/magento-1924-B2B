<?php

class Rockwells_LogViewer_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function getLogFiles() {
        $dir = Mage::getBaseDir('log') . DS;
        $files = scandir($dir);
        $useWc = PHP_OS != 'WINNT';
        $collection = array();
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $path = "$dir$file";
            if (is_dir($path)) {
                continue;
            }
            $lines = 0;
            if ($useWc) {
                $lines = intval(exec('wc -l ' . escapeshellarg($path)));
            } else {
                $fh = fopen($path, 'rb');
                while (!feof($fh)) {
                    $lines += substr_count(fread($fh, 8192), "\n");
                }
                fclose($fh);
            }
            $collection[] = array(
                'filename'  => $file,
                'filesize'  => filesize($path),
                'lines'     => $lines,
            );
        }
        return $collection;
    }
    
    public function humanFilesize($bytes, $decimals = 2) {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $size[$factor]);
    }

}