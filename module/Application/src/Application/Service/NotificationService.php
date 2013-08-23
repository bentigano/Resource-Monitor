<?php
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
// mailing
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

use Application\Model\MonitoringPlan;
use Application\Model\MonitoringPlanTable;
use Application\Model\ResourceType;
use Application\Model\FrequencyUnit;

class NotificationService
{
    protected $sm;
    protected $mailTransport;
    
    protected $alertSubscriptionTable;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
        
        // Setup SMTP transport using LOGIN authentication
        $this->mailTransport = new SmtpTransport();
        $options   = new SmtpOptions($this->sm->get('Config')['alerts']['mail']);
        $this->mailTransport->setOptions($options);
    }
    
    public function sendFailureAlert($monitoringPlan, $resource)
    {
        $subjectFormat = 'Test Failed: %s';
        
        $message = new Message();
        $message->addFrom($this->sm->get('Config')['alerts']['send_from'])
            ->setSubject(sprintf($subjectFormat, $monitoringPlan->name));
        
        $alertSubscriptions = $this->getAlertSubscriptionTable()->getAlertSubscriptionsByPlan($monitoringPlan->id);
        foreach ($alertSubscriptions as $alertSubscription) {
            $message->addTo($alertSubscription->email);
        }
        
        $format = '<strong>Resource Name:</strong> %s
        
        <strong>Last Exception:</strong> %s';
        $html = new MimePart(nl2br(sprintf($format, $resource->name, $resource->last_error)));
        $html->type = "text/html";
         
        $body = new MimeMessage();
        $body->addPart($html);
         
        $message->setBody($body);
         
        $this->mailTransport->send($message);
        
    }
    
    public function getAlertSubscriptionTable()
    {
        if (!$this->alertSubscriptionTable) {
            $this->alertSubscriptionTable = $this->sm->get('Application\Model\AlertSubscriptionTable');
        }
        return $this->alertSubscriptionTable;
    }
}