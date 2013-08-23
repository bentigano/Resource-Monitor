<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class StatusCheckTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($resource_id)
    {
        $result  = $this->tableGateway->select(function (Select $select) use ($resource_id){
            $select->where(array('resource_id' => $resource_id));
            $select->order('datetime_checked DESC');
        });
   
        return $result;
    }

    public function getStatusCheck($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function addStatusCheck(StatusCheck $statusCheck)
    {
        $data = array(
            'resource_id' => $statusCheck->resource_id,
            'datetime_checked'  => $statusCheck->datetime_checked->format('Y-m-d H:i:s'),
            'success'  => $statusCheck->success,
            'error_details'  => $statusCheck->error_details,
        );

        $this->tableGateway->insert($data);
    }
}