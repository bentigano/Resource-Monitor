<?php
namespace Application\Model\Resource;

use Zend\Db\TableGateway\TableGateway;

class DbConnectionTable
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

    public function getDbConnection($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveDbConnection(DbConnection $dbConnection)
    {
        $data = array(
            'name' => $dbConnection->name,
            'dsn'  => $dbConnection->dsn,
            'username'  => $dbConnection->username,
            'password'  => $dbConnection->password,
        );

        $id = (int)$dbConnection->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getDbConnection($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('DbConnection id does not exist');
            }
        }
    }

    public function deleteDbConnection($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    public function testDbConnection($dbConnection)
    {
        $dbConnection->test();
        $data = array(
            'last_checked' => $dbConnection->last_checked->format('Y-m-d H:i:s'),
            'last_result'  => $dbConnection->last_result,
            'last_error'   => $dbConnection->last_error,
        );
        
        if (!$data['last_result']) {
            $data['last_result'] = 0;
        }

        $id = (int)$dbConnection->id;
        if ($this->getDbConnection($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('DbConnection id does not exist');
        }
    }
}