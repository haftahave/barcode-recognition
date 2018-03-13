<?php
namespace Hth;

/**
 * Class ZbarCliReader
 * @package Hth
 */
class ZbarCliReader
{
    /**
     * @var StdoutLogger
     */
    protected $logger;


    /**
     * ZbarCliReader constructor.
     * @param StdoutLogger $logger
     */
    public function __construct(StdoutLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $filePath
     * @return array
     * @throws \LogicException
     */
    public function scan($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \LogicException(sprintf('File "%s" is not exists', $filePath));
        }

        $resultOrigin = $this->scanFile($filePath);

        if (count($resultOrigin) === 1) {
            return $resultOrigin;
        }

        $rotateCommand = sprintf('for file in %s; do convert $file -rotate 90 $file; done', escapeshellarg($filePath));
        shell_exec(escapeshellcmd($rotateCommand));

        $resultRotated = $this->scanFile($filePath);

        if (count($resultRotated) === 1) {
            return $resultRotated;
        }

        $result = array_intersect_key($resultOrigin, $resultRotated);
        if (count($result) === 1) {
            return $result;
        }

        return [];
    }

    /**
     * @param string $filePath
     * @return array
     */
    protected function scanFile($filePath)
    {
        $scanCommand = sprintf('zbarimg -q --xml %s', escapeshellarg($filePath));
        $output = shell_exec(escapeshellcmd($scanCommand));
        return $this->processResult($output);
    }

    /**
     * @param string $output
     * @return array
     */
    protected function processResult($output)
    {
        $result = [];
        $xml = simplexml_load_string($output);
        if ($xml === false) {
            $errorMessage = "Failed loading XML: ";
            foreach (libxml_get_errors() as $error) {
                $errorMessage .= ' ' . $error->message;
            }
            $this->logger->error($errorMessage);
            return $result;
        }
        if (!isset($xml->source->index->symbol)) {
            return $result;
        }
        $resultListXml = $xml->source->index->symbol;
        $itemsCount = count($resultListXml);
        for ($i = 0; $i < $itemsCount; $i++) {
            $resultXml = $resultListXml[$i];
            $barcode = (string) $resultXml->data;
            $result[$barcode] = [
                'barcodeUpcCode' => $barcode,
                'barcodeType' => (string) $resultXml['type'],
                'quality' => (string) $resultXml['quality'],
            ];
        }
        $result = $this->filterBarcodeList($result);
        return $result;
    }

    /**
     * Filters out barcodes that are part of another barcode, e.g. will skip 451008 if 451008465955 exists
     * @param array $barcodeList
     * @return array
     */
    protected function filterBarcodeList($barcodeList)
    {
        $result = [];
        foreach (array_keys($barcodeList) as $barcode1) {
            $skip = false;
            foreach (array_keys($barcodeList) as $barcode2) {
                if ($barcode1 === $barcode2) {
                    continue;
                }

                if (strpos(strval($barcode2), strval($barcode1)) !== false) {
                    $skip = true;
                }
            }

            if (!$skip) {
                $result[] = $barcodeList[$barcode1];
            }
        }

        return $result;
    }
}
