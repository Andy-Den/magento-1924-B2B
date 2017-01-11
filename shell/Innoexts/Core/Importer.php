<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Shell
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

require_once rtrim(dirname(__FILE__), '/').'/../../abstract.php';

/**
 * Product price importer script
 * 
 * @category   Innoexts
 * @package    Innoexts_Shell
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Shell_Core_Importer extends Mage_Shell_Abstract 
{
    /**
     * New line
     * 
     * @var string
     */
    protected $_nl = "\n";
    /**
     * Tab
     * 
     * @var string 
     */
    protected $_tab = "\t";
    /**
     * FTP config
     * 
     * @var array
     */
    protected $_ftpConfig = array(
        'host' => 'ftp.yourhost.com', 
        'user' => 'username', 
        'password' => 'password', 
        'filename' => 'remotefilename', 
    );
    /**
     * File config
     * 
     * @var array
     */
    protected $_fileConfig = array(
        'path' => '/var/import/', 
        'filename' => 'localfilename', 
        'delimiter' => ',', 
        'enclosure' => '"', 
    );
    /**
     * FTP
     * 
     * @var Varien_Io_Ftp
     */
    protected $_ftp;
    /**
     * File
     * 
     * @var Varien_Io_File
     */
    protected $_file;
    /**
     * Get new line
     * 
     * @return string
     */
    protected function getNl()
    {
        return $this->_nl;
    }
    /**
     * Get tab
     * 
     * @return string
     */
    protected function getTab()
    {
        return $this->_tab;
    }
    /**
     * Get FTP config
     * 
     * @return array
     */
    protected function getFTPConfig()
    {
        return $this->_ftpConfig;
    }
    /**
     * Get File config
     * 
     * @return array
     */
    protected function getFileConfig()
    {
        return $this->_fileConfig;
    }
    /**
     * Get FTP filename
     * 
     * @return string
     */
    protected function getFTPFilename()
    {
        $config = $this->getFTPConfig();
        return (isset($config['filename'])) ? $config['filename'] : null;
    }
    /**
     * Get file name
     * 
     * @return string
     */
    protected function getFileFilename()
    {
        $config = $this->getFileConfig();
        return (isset($config['filename'])) ? $config['filename'] : null;
    }
    /**
     * Get file path
     * 
     * @return string
     */
    protected function getFilePath()
    {
        $config = $this->getFileConfig();
        return Mage::getBaseDir().DS.trim($config['path'], DS).DS.$config['filename'];
    }
    /**
     * Print message
     * 
     * @param string    $message
     * @param bool      $newLine 
     * @param int|null  $tabs
     * 
     * @return Innoexts_Shell_Core_Importer
     */
    protected function printMessage($message, $newLine = true, $tabs = null)
    {
        if (!is_null($tabs)) {
            $message = str_pad($this->getTab(), (int) $tabs).$message;
        }
        if ($newLine) {
            $message .= $this->getNl();
        }
        echo $message;
        return $this;
    }
    /**
     * Get file
     * 
     * @return Varien_Io_File
     */
    protected function getFile()
    {
        if (is_null($this->_file)) {
            $file = new Varien_Io_File();
            $config = $this->getFileConfig();
            $path = $file->getCleanPath(Mage::getBaseDir().DS.trim($config['path'], DS));
            $file->checkAndCreateFolder($path);
            $config['path'] = rtrim(realpath($path), DS);
            try {
                $file->open($config);
                $this->_file = $file;
            } catch (Exception $e) {
                $this->printMessage($e->getMessage());
            }
        }
        return $this->_file;
    }
    /**
     * Get FTP
     * 
     * @return Varien_Io_Ftp
     */
    protected function getFTP()
    {
        if (is_null($this->_ftp)) {
            $ftp = new Varien_Io_Ftp();
            $config = $this->getFTPConfig();
            try {
                $ftp->open($config);
                $this->_ftp = $ftp;
            } catch (Exception $e) {
                $this->printMessage($e->getMessage());
            }
        }
        return $this->_ftp;
    }
    /**
     * Download file
     * 
     * @return bool
     */
    protected function download()
    {
        $isDownloaded = false;
        $this->printMessage('Downloading data file...');
        $ftp = $this->getFTP();
        if (!is_null($ftp)) {
            $file = $this->getFile();
            if (!is_null($file)) {
                $data = $ftp->read($this->getFTPFilename(), $this->getFilePath());
                if (false !== $data) {
                    $isDownloaded = true;
                    $this->printMessage('Downloaded.');
                } else {
                    $this->printMessage("Could not download file: {$this->getFTPFilename()}");
                }
            }
        }
        return $isDownloaded;
    }
    /**
     * Parse FTP arguments
     * 
     * @return bool
     */
    protected function parseFTPArgs()
    {
        $isParsed = true;
        if ($this->getArg('ftp')) {
            $ftpHost = trim($this->getArg('ftp-host'));
            if (!$ftpHost) {
                $this->printMessage('FTP host is required.');
                $isParsed = false;
            } else {
                $this->_ftpConfig['host'] = $ftpHost;
            }
            $ftpUser = trim($this->getArg('ftp-user'));
            if (!$ftpUser) {
                $this->printMessage('FTP user is required.');
                $isParsed = false;
            } else {
                $this->_ftpConfig['user'] = $ftpUser;
            }
            $ftpPassword = trim($this->getArg('ftp-password'));
            if (!$ftpPassword) {
                $this->printMessage('FTP password is required.');
                $isParsed = false;
            } else {
                $this->_ftpConfig['password'] = $ftpPassword;
            }
            $ftpFilename = trim($this->getArg('ftp-filename'));
            if (!$ftpFilename) {
                $this->printMessage('FTP filename is required.');
                $isParsed = false;
            } else {
                $this->_ftpConfig['filename'] = $ftpFilename;
            }
        }
        return $isParsed;
    }
    /**
     * Parse file arguments
     * 
     * @return bool
     */
    protected function parseFileArgs()
    {
        $isParsed = true;
        $filePath = trim($this->getArg('file-path'));
        if (!$filePath) {
            $this->printMessage('File path is required.');
            $isParsed = false;
        } else {
            $this->_fileConfig['path'] = $filePath;
        }
        $fileFilename = trim($this->getArg('file-filename'));
        if (!$fileFilename) {
            $this->printMessage('File filename is required.');
            $isParsed = false;
        } else {
            $this->_fileConfig['filename'] = $fileFilename;
        }
        $fileCsvDelimiter = trim($this->getArg('file-csv-delimiter'));
        if ($fileCsvDelimiter) {
            $this->_fileConfig['delimiter'] = $fileCsvDelimiter;
        }
        $fileCsvEnclosure = trim($this->getArg('file-csv-enclosure'));
        if ($fileCsvEnclosure) {
            $this->_fileConfig['enclosure'] = $fileCsvEnclosure;
        }
        return $isParsed;
    }
    /**
     * Parse arguments
     * 
     * @return bool
     */
    protected function parseArgs()
    {
        return $this->parseFTPArgs() && $this->parseFileArgs();
    }
    /**
     * Reindex
     * 
     * @return Innoexts_Shell_Core_Importer
     */
    protected function reindex()
    {
        $this->printMessage('Reindexing.');
        return $this;
    }
    /**
     * Import prices
     * 
     * @return bool
     */
    protected function import()
    {
        $isImported = false;
        if (!$this->parseArgs()) {
            return $this;
        }
        if ($this->getArg('ftp')) {
            $this->download();
        }
        $this->printMessage('Importing data file...');
        $file = $this->getFile();
        if ($file) {
            $config = $this->getFileConfig();
            try {
                $file->streamOpen($this->getFileFilename(), 'r');
                $_fieldNames = $file->streamReadCsv($config['delimiter'], $config['enclosure']);
                if (count($_fieldNames)) {
                    $fieldNames = array();
                    foreach ($_fieldNames as $index => $fieldName) {
                        $fieldNames[$index] = trim(strtolower($fieldName));
                    }
                    while (($csvData = $file->streamReadCsv($config['delimiter'], $config['enclosure'])) !== false) {
                        if (count($csvData) == 1 && $csvData[0] === null) { 
                            continue; 
                        }
                        $row = array();
                        foreach ($fieldNames as $index => $fieldName) {
                            if (isset($csvData[$index])) {
                                $row[$fieldName] = $csvData[$index];
                            }
                        }
                        $this->importRow($row);
                    }
                    $isImported = true;
                    $this->reindex();
                    $this->printMessage('Imported.');
                } else {
                    $this->printMessage('Data file header was not found.');
                }
            } catch (Exception $e) {
                $this->printMessage($e->getMessage());
            }
        }
        return $isImported;
    }
    /**
     * Run script
     */
    public function run()
    {
        if (!$this->getArg('help')) {
            $this->import();
        } else {
            $this->printHelp();
        }
    }
    /**
     * Print help
     * 
     * @return Innoexts_Shell_Core_Importer
     */
    protected function printHelp()
    {
        $this->printMessage($this->usageHelp());
        return $this;
    }
        /**
     * Get help message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f Importer.php -- [options]
  
  ftp <flag>                                    Check if data file should be downloaded from the FTP server first
  ftp-host <ftp-host>                           FTP host
  ftp-user <ftp-user>                           FTP user
  ftp-password <ftp-password>                   FTP password
  ftp-filename <ftp-filename>                   FTP filename
  
  file-path <file-path>                         File path
  file-filename <file-filename>                 File filename
  file-csv-delimiter <file-csv-delimiter>       File CSV delimiter
  file-csv-enclosure <file-csv-enclosure>       File CSV enclosure
  
  help                                          This help
USAGE;
    }
}