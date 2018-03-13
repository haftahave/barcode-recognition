<?php
namespace Hth;

/**
 * Class StdoutLogger
 * @package Hth
 */
class StdoutLogger
{
    /**
     * @param string $text
     */
    public function error($text)
    {
        echo $text;
    }
}
