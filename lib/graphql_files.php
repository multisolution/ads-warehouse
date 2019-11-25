<?php declare(strict_types=1);

namespace AdsWarehouse;

use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

function graphql_files(string $basedir): string
{
    $dir_iter = new RecursiveDirectoryIterator($basedir);

    $filter_iter = new  class ($dir_iter) extends RecursiveFilterIterator
    {
        public function accept()
        {
            return preg_match('/\.graphql$/', $this->current()->getFilename());
        }
    };

    $iter = new RecursiveIteratorIterator($filter_iter);

    return array_reduce(iterator_to_array($iter), function (string $schema, SplFileInfo $fileInfo): string {
        return "$schema\n" . file_get_contents($fileInfo->getFilename());
    }, "");
}