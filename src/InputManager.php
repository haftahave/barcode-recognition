<?php
namespace Hth;

/**
 * Class InputManager
 * @package Hth
 */
class InputManager
{
    const DEFAULT_PARAM_VALUE = '-';
    const PARAM_AWS_KEY = 'AWS_KEY';
    const PARAM_AWS_REGION = 'AWS_REGION';
    const PARAM_AWS_SECRET = 'AWS_SECRET';
    const PARAM_FUNCTION_NAME = 'FUNCTION_NAME';
    const PARAM_FUNCTION_QUALIFIER = 'FUNCTION_QUALIFIER';
    const PARAM_FUNCTION_TRACE_ID = 'FUNCTION_TRACE_ID';
    const PARAM_IMAGE_URL = 'IMAGE_URL';

    /**
     * @var array
     */
    protected $errorList = [];

    /**
     * @var array
     */
    protected $optionsList = [];

    /**
     * InputManager constructor.
     * @param array $argv
     */
    public function __construct($argv)
    {
        $requiredArguments = [
            self::PARAM_IMAGE_URL,
            self::PARAM_FUNCTION_NAME,
            self::PARAM_FUNCTION_QUALIFIER,
            self::PARAM_AWS_REGION,
            self::PARAM_AWS_KEY,
            self::PARAM_AWS_SECRET,
        ];

        foreach ($argv as $argument) {
            $argParts = explode('=', $argument);
            if (count($argParts) !== 2) {
                continue;
            }
            $this->optionsList[$argParts[0]] = $argParts[1];
        }

        foreach ($requiredArguments as $argumentName) {
            if (empty($this->optionsList[$argumentName])
                && $this->optionsList[$argumentName] ==! self::DEFAULT_PARAM_VALUE
            ) {
                $this->errorList[] = sprintf('Parameter `%s` has been missed', $argumentName);
            }
        }
    }

    /**
     * @return bool
     */
    public function hasInputErrors()
    {
        return !empty($this->errorList);
    }

    /**
     * @return array
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return $this->optionsList[self::PARAM_IMAGE_URL];
    }

    /**
     * @return string
     */
    public function getLambdaFunctionName()
    {
        return $this->optionsList[self::PARAM_FUNCTION_NAME];
    }

    /**
     * @return string
     */
    public function getLambdaFunctionQualifier()
    {
        return $this->optionsList[self::PARAM_FUNCTION_QUALIFIER];
    }

    /**
     * @return string
     */
    public function getAwsRegion()
    {
        return $this->optionsList[self::PARAM_AWS_REGION];
    }

    /**
     * @return string
     */
    public function getAwsKey()
    {
        return $this->optionsList[self::PARAM_AWS_KEY];
    }

    /**
     * @return string
     */
    public function getAwsSecret()
    {
        return $this->optionsList[self::PARAM_AWS_SECRET];
    }

    /**
     * @return string|null
     */
    public function getFunctionTraceId()
    {
        if (isset($this->optionsList[self::PARAM_FUNCTION_TRACE_ID])) {
            return $this->optionsList[self::PARAM_FUNCTION_TRACE_ID];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getOptionList()
    {
        return $this->optionsList;
    }
}
