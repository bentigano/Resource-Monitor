<?php
namespace Application\Model\Resource;

use Zend\Db\TableGateway\TableGateway;

class QueryCountTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getQueryCount($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveQueryCount(QueryCount $queryCount)
    {
        $data = array(
            'name' => $queryCount->name,
            'which_db'  => $queryCount->which_db,
            'query'  => $queryCount->query,
            'fail_count'  => $queryCount->fail_count,
        );

        $id = (int)$queryCount->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getQueryCount($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('QueryCount id does not exist');
            }
        }
    }

    public function deleteQueryCount($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    public function testQueryCount($queryCount, $dbAdapter)
    {
        $queryCount->test($dbAdapter);
        $data = array(
            'last_checked' => $queryCount->last_checked->format('Y-m-d H:i:s'),
            'last_result'  => $queryCount->last_result,
            'last_error'   => $queryCount->last_error,
        );
        
        if (!$data['last_result']) {
            $data['last_result'] = 0;
        }

        $id = (int)$queryCount->id;
        if ($this->getQueryCount($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('QueryCount id does not exist');
        }
    }
}