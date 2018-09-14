<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 21.08.18
 * Time: 19:38
 */

namespace cronfy\geoname\common\misc;


class CachedRemoteFile extends LocalFileProxy
{
    public $srcUrl;
    public $destFilePath;

    protected function getDownloadedFilePath() {
        return $this->destFilePath;
    }

    protected function download() {
        $dstFileName = $this->getDownloadedFilePath();
        $dstDir = dirname($dstFileName);
        $tmpFile = tempnam($dstDir, basename($dstFileName));
        $srcUrl = $this->srcUrl;

        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0755, true);
        }

        file_put_contents($tmpFile, fopen($srcUrl, 'r'));

        rename($tmpFile, $dstFileName);
    }

    protected $_localFile;
    protected function getLocalFile() {
        if (!$this->_localFile) {
            $this->prepare();
            $file = new LocalFile();
            $file->path = $this->getDownloadedFilePath();
            $this->_localFile = $file;
        }

        return $this->_localFile;
    }

    protected function prepare() {
        $filePath = $this->getDownloadedFilePath();

        if (!is_file($filePath)) {
            $this->download();
        }
    }

}