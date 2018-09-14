<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 21.08.18
 * Time: 19:38
 */

namespace cronfy\geoname\common\misc;


class CachedZipFile extends LocalFileProxy
{
    /** @var File */
    public $sourceZip;
    public $archiveItemFileName;

    public $destFilePath;

    protected function prepare() {
        $filePath = $this->getExtractedFilePath();

        if (!is_file($filePath)) {
            $this->extract();
        }
    }

    protected $_localFile;
    protected function getLocalFile() {
        if (!$this->_localFile) {
            $this->prepare();
            $file = new LocalFile();
            $file->path = $this->getExtractedFilePath();
            $this->_localFile = $file;
        }

        return $this->_localFile;
    }

    protected function getExtractedFilePath() {
        return $this->destFilePath;
    }

    protected function extract() {
        $zipFile = $this->sourceZip->getPath();

        $dstFileName = $this->getExtractedFilePath();
        $dstDir = dirname($dstFileName);
        $tmpFile = tempnam($dstDir, basename($dstFileName));

        copy("zip://{$zipFile}#{$this->archiveItemFileName}", $tmpFile);
        rename($tmpFile, $dstFileName);
    }

}