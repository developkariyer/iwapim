<?php

namespace App\Connector;

class IwabotConnector
{
    public $report = [];

    public function downloadReport()
    {
        $this->report = file_get_contents('https://iwarden.iwaconcept.com/iwabot/warehouse/report.php?csv=1');
    }


}