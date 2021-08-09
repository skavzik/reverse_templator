<?php declare(strict_types = 1);

namespace App;

class InvalidTemplateException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid template.');
    }
}
