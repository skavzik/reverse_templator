<?php declare(strict_types = 1);

namespace App;

class ResultTemplateMismatchException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Result not matches original template.');
    }
}
