<?php

namespace Hth;

/**
 * Class ImageResourceProcessor
 * @package Hth
 */
class ImageUrlProcessor
{
    /**
     * @var ZbarCliReader
     */
    protected $barcodeReader;

    /**
     * ImageResourceProcessor constructor.
     * @param ZbarCliReader $barcodeReader
     */
    public function __construct(ZbarCliReader $barcodeReader) {
        $this->barcodeReader = $barcodeReader;
    }

    /**
     * @param string $url
     * @return array
     */
    public function process($url)
    {
        $imageFile = $this->saveFileByUrl($url);

        $resultList = $this->barcodeReader->scan($imageFile);

        $count = 0;
        while (count($resultList) === 0 && $count < 3) {
            $count++;
            $imagick = new \Imagick($imageFile);
            $imagick->brightnessContrastImage(20, 20);
            $imagick->despeckleImage();
            $imagick->writeImage();
            $resultList = $this->barcodeReader->scan($imageFile);
        }

        unlink($imageFile);

        return $resultList;
    }

    /**
     * @param string $url
     * @return string - Local filename with full path
     * @throws \RuntimeException
     */
    protected function saveFileByUrl($url)
    {
        $localFile = tempnam(sys_get_temp_dir(), 'barcode');
        if ($localFile === false) {
            $message = sprintf(
                'ImageResourceProcessor: Failed to create tmp file under %s',
                sys_get_temp_dir()
            );
            throw new \RuntimeException($message);
        }

        $fileResource = fopen($localFile, 'w+');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FILE, $fileResource);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fileResource);
        return $localFile;
    }
}
