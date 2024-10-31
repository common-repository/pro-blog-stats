<?php 

/***************************************************************************
 *   Copyright (C) 2010-2011 by Pro Blog Stats (www.problogstats.com/)     *
 *   admin@problogstats.com                                                *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   You should have received a copy of the GNU General Public License     *
 *   along with this program; if not, write to the                         *
 *   Free Software Foundation, Inc.,                                       *
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
 ***************************************************************************/

class BPRPService {
    var $apiKey;    
    var $client;
    var $results = null;
    var $requestedService = false;
    var $siteurl = '';
    var $startDate;
    var $endDate;

    public $serviceResults;
    
    public function BPRPService($apiKey, $url = '', $startdate = '', $enddate = '') {
        $this->apiKey = $apiKey;
        $this->siteurl = $url;
        $this->startDate = $startdate;
        $this->endDate = $enddate;
        $loc = BPRP_PATH.'ws/GET_SERVICES_INFO.php?wsdl';
        if(!class_exists('nusoap_client')) {
            require_once dirname(__FILE__).'/nusoap/nusoap.php';
        }
        $this->client = new nusoap_client($loc, TRUE);
        $this->client->soap_defencoding = 'utf-8';
        $this->client->use_curl = true;
    }
    
    public function requestServicesReport() {
        $serviceCallInput = array('apikey' => $this->apiKey,
                                'url' => $this->siteurl,
                                'startdate' => $this->startDate,
                                'enddate' => $this->endDate
                            );
        $result = $this->client->call('getServicesInfo', array('sericecallinput' => $serviceCallInput) );        
        $this->serviceResults = $result;
        $this->requestedService = true;
    }   
    
    function getServiceResults() {
        if (!$this->requestedService) {
            return array();
        } else {
            return $this->serviceResults;
        }
    }
    
    function hasError() {
        return $this->requestedService && (!empty($this->serviceResults['faultcode']) || ($error = $this->client->getError()) );
    }
    
    function getError() {
        if (!$this->requestedService) {
            return false;
        } elseif (!empty($this->serviceResults['faultcode'])) {
            return array('Message'=>$this->serviceResults['faultstring'], 'Type'=>$this->serviceResults['faultcode']);
        } elseif ($this->client->getError()) {             
            return array('Message'=> 'Could not connect to Host', 'Type'=>1);
        } else {
            return array('Message'=>'Unknown Error', 'Type'=>3);
        }
    }
    
    function getErrorMessage() {
        $error = $this->getError();
        if (is_array($error)) {
            return $error['Message'];
        } else {
            return false;
        }
    }
    
    function getErrorType() {
        $error = $this->getError();
        if (is_array($error)) {
            return $error['Type'];
        } else {
            return false;
        }
    }
    
    
    
}
