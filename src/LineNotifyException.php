<?php
namespace WithKao;

class LineNotifyException extends \Exception
{
    public function __construct(...$messages)
    {
        parent::__construct(implode(PHP_EOL, $messages), 0, null);
    }
}
