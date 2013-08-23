<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class MonitoringPlanTable
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

    public function getMonitoringPlan($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveMonitoringPlan(MonitoringPlan $monitoringPlan)
    {
        $data = array(
            'resource_id' => $monitoringPlan->resource_id,
            'name' => $monitoringPlan->name,
            'enabled'  => $monitoringPlan->enabled,
            'run_mondays'  => $monitoringPlan->run_mondays,
            'run_tuesdays'  => $monitoringPlan->run_tuesdays,
            'run_wednesdays'  => $monitoringPlan->run_wednesdays,
            'run_thursdays'  => $monitoringPlan->run_thursdays,
            'run_fridays'  => $monitoringPlan->run_fridays,
            'run_saturdays'  => $monitoringPlan->run_saturdays,
            'run_sundays'  => $monitoringPlan->run_sundays,
            'frequency'  => $monitoringPlan->frequency,
            'frequency_unit'  => $monitoringPlan->frequency_unit,
            'starting_at'  => $monitoringPlan->starting_at,
            'ending_at'  => $monitoringPlan->ending_at,
        );

        $id = (int)$monitoringPlan->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getMonitoringPlan($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('MonitoringPlan id does not exist');
            }
        }
    }

    public function deleteMonitoringPlan($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    public function touchMonitoringPlan($monitoringPlan)
    {
        $data = array(
            'last_checked' => $monitoringPlan->last_checked->format('Y-m-d H:i:s'),
        );

        $id = (int)$monitoringPlan->id;
        if ($this->getMonitoringPlan($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('MonitoringPlan id does not exist');
        }

    }
}