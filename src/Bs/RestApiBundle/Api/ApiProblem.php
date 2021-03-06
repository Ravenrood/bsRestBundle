<?php

namespace RestApiBundle\Api;

use Symfony\Component\HttpFoundation\Response;
/**
 * A wrapper for holding data to be used for a application/problem+json response
 */
class ApiProblem
{
    const TYPE_VALIADTION_ERROR = 'validation_error';
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';
    
    private static $titles = array (
        self::TYPE_VALIADTION_ERROR => 'Invalid Form Data',
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid Json format'
    );


    private $statusCode;

    private $type;

    private $title;

    private $extraData = array();

    public function __construct($statusCode, $type = null)
    {
        $this->statusCode = $statusCode;
        if (null === $type) {
            $type = 'about:blank';
            $title = isset(Response::$statusTexts[$statusCode])
                    ? Response::$statusTexts[$statusCode]
                    : 'Unknown status code';
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException ('No title for type ' . $type);
            } 
            $title = self::$titles[$type];
        }
        
        
        $this->type = $type;
        $this->title = $title;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            array(
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            )
        );
    }

    public function set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    public function getTitle ()
    {
        return $this->title;
    }
}
