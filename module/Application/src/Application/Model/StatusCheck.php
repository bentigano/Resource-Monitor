<?php
namespace Application\Model;

class StatusCheck
{
    public $id;
    public $resource_id;
    public $datetime_checked;
    public $success;
    public $error_details;

    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id'])) ? $data['id'] : null;
        $this->resource_id          = (!empty($data['resource_id'])) ? $data['resource_id'] : null;
        $this->datetime_checked     = (!empty($data['datetime_checked'])) ? $data['datetime_checked'] : null;
        $this->success              = $data['success'];
        $this->error_details        = (!empty($data['error_details'])) ? $data['error_details'] : null;
    }
}