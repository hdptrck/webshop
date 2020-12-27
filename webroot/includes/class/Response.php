<?php
class Response
{
    var $code = null;
    var $description;
    var $data = null;

    public function __construct(int $code = 500, string $description = 'Server Error')
    {
        $this->code = $code;
        $this->description = $description;
    }

    public function add_data($data)
    {
        $this->data = $data;
    }
}
