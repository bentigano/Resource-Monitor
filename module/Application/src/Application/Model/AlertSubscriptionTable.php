<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class AlertSubscriptionTable
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

    public function getAlertSubscription($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getAlertSubscriptionsByPlan($monitoring_plan_id)
    {
        $resultSet = $this->tableGateway->select(array('monitoring_plan_id' => $monitoring_plan_id));
        return $resultSet;
    }

    public function saveAlertSubscription(AlertSubscription $alertSubscription)
    {
        $data = array(
            'monitoring_plan_id' => $alertSubscription->monitoring_plan_id,
            'email' => $alertSubscription->email,
        );

        $id = (int)$alertSubscription->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlertSubscription($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('AlertSubscription id does not exist');
            }
        }
    }

    public function deleteAlertSubscription($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}