<?php
namespace Application\Model\Resource;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Application\Model\ResourceType;

class ResourceTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $select = new Select($this->tableGateway->table);
        $select->order('type');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    
    public function fetchDBConnections()
    {
        $resultSet = $this->tableGateway->select(array('type' => ResourceType::DB));
        return $resultSet;
    }
    
    public function fetchQueryCounts()
    {
        $resultSet = $this->tableGateway->select(array('type' => ResourceType::QUERY));
        return $resultSet;
    }
    
    public function fetchSoapServices()
    {
        $resultSet = $this->tableGateway->select(array('type' => ResourceType::SOAP));
        return $resultSet;
    }

    public function getResource($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getResourceByType($type, $id)
    {
        $id  = (int) $id;
        $type = (int) $type;
        $rowset = $this->tableGateway->select(array('source_id' => $id, 'type' => $type));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
}