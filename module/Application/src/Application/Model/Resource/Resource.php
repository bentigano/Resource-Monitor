<?php
namespace Application\Model\Resource;

class Resource
{
    public $id;
    public $type;
    public $source_id;

    public function exchangeArray($data)
    {
        $this->id               = (!empty($data['id'])) ? $data['id'] : null;
        $this->type             = (!empty($data['type'])) ? $data['type'] : null;
        $this->source_id         = (!empty($data['source_id'])) ? $data['source_id'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}