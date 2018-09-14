<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 21.08.18
 * Time: 19:38
 */

namespace cronfy\geoname\common\misc;


class GenericFileRepository
{
    public $dataCacheDir;
    public $destFileName;
    public $zip;
    public $archiveItemFileName;
    public $srcUrl;
    public $columns;

    protected function getDataFilePath() {
        if (!$this->dataCacheDir) {
            throw new \Exception("\$dataCacheDir must be set");
        }

        return $this->dataCacheDir . "/" . $this->destFileName;
    }

    protected function download() {
        $dstFileName = $this->getDataFilePath();
        $dstDir = dirname($dstFileName);
        $tmpFile = tempnam($dstDir, basename($dstFileName));
        $srcUrl = $this->srcUrl;

        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0755, true);
        }

        file_put_contents($tmpFile, fopen($srcUrl, 'r'));

        if ($this->zip) {
            copy("zip://$tmpFile#{$this->archiveItemFileName}", "$tmpFile.txt");
            rename("$tmpFile.txt", $dstFileName);
            unlink($tmpFile);
        } else {
            rename($tmpFile, $dstFileName);
        }

    }

    protected function open() {
        $filePath = $this->getDataFilePath();

        if (!is_file($filePath)) {
            $this->download();
        }

        return fopen($filePath, 'r');
    }

    protected function populateItem($lineData) {
        return $lineData;
    }

    public function iterate() {
        $fh = $this->open();

        $columnNames = array_keys($this->columns);
        $columnNames = array_map(
            function ($item) {
                return strtr($item, ' ', '_');
            },
            $columnNames
        );

        while ($line = fgetcsv($fh, 0, "\t")) {
            $lineData = array_combine($columnNames, $line);

            $item = $this->populateItem($lineData);

            yield $item;
        }
    }

    public function iterateFiltered($filters) {
        foreach ($this->iterate() as $item) {
            foreach ($filters as $filter) {
                if (!$filter($item)) {
                    continue 2;
                }
            }

            yield $item;
        }
    }

}