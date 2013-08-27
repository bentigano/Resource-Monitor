<?php
namespace Application\Model\Resource;

use Zend\Db\TableGateway\TableGateway;

class SoapServiceTable
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

    public function getSoapService($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveSoapService(SoapService $soapService)
    {
        $data = array(
            'name' => $soapService->name,
            'wsdl_location'  => $soapService->wsdl_location,
        );

        $id = (int)$soapService->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSoapService($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('SoapService id does not exist');
            }
        }
    }

    public function deleteSoapService($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    public function testSoapService($soapService)
    {
        $soapService->test();
        $data = array(
            'last_checked' => $soapService->last_checked->format('Y-m-d H:i:s'),
            'last_result'  => $soapService->last_result,
            'last_error'   => $soapService->last_error,
        );
        
        if (!$data['last_result']) {
            $data['last_result'] = 0;
        }

        $id = (int)$soapService->id;
        if ($this->getSoapService($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('SoapService id does not exist');
        }
    }
}