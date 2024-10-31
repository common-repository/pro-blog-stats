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

class BPRPUserAccount {
    var $apiKey;    
    var $client;
    var $results = null;
    var $requestHasBeenExecuted = false;
    var $siteurl = '';

    public $userAccountInfo;
    
    public function BPRPUserAccount($apiKey, $url = '') {
        $this->apiKey = $apiKey;
        $this->siteurl = $url;
        $loc = BPRP_PATH.'ws/AUTH_USER.php?wsdl';
        if(!class_exists('nusoap_client')) {
            require_once dirname(__FILE__).'/nusoap/nusoap.php';
        }
        $this->client = new nusoap_client($loc, TRUE);
        $this->client->soap_defencoding = 'utf-8';
        $this->client->use_curl = true;
    }
    
    public function UserAccountStatus() {
        $bprpAuthInput = array('apikey' => $this->apiKey, 'url' => $this->siteurl);
        $result = $this->client->call('getUserInfo', array('authinput' => $bprpAuthInput) );
        $this->userAccountInfo = $result;
        $this->requestHasBeenExecuted = true;
    }   
    
    function getRawResults() {
        if (!$this->requestHasBeenExecuted) {
            return array();
        } else {
            return $this->userAccountInfo;
        }
    }
    
    function hasError() {
        return $this->requestHasBeenExecuted && (!empty($this->userAccountInfo['faultcode']) || ($error = $this->client->getError()) );
    }
    
    function getError() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } elseif (!empty($this->userAccountInfo['faultcode'])) {
            return array('Message'=>$this->userAccountInfo['faultstring'], 'Type'=>$this->userAccountInfo['faultcode']);
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
    
    function getAccountStatus() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            print_r($this->userAccountInfo->client->return);
            return $this->userAccountInfo->client->return['return']['status'];
        }
    }
    
    function getAccountType() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return $this->userAccountInfo['plan'];
        }
    }

    function getAccountServices() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return $this->userAccountInfo['services'];
        }
    }
    
    function getApiKey() {
        return $this->apiKey;
    }
    
    function getCreditsRemaining() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return $this->userAccountInfo['GetAccountStatusResult']['AccountStatus']['CreditsRemaining'];
        }
    }
    
    function getCreditsTotal() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return $this->userAccountInfo['GetAccountStatusResult']['AccountStatus']['CreditsTotal'];
        }
    }
    
    function getLastBilledAmount() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return $this->userAccountInfo['GetAccountStatusResult']['AccountStatus']['LastBilledAmount'];
        }
    }
    
    function getLastBilledDate($format = 'n/j/Y') {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return date($format, strtotime(str_replace('T', ' ', $this->userAccountInfo['GetAccountStatusResult']['AccountStatus']['LastBilledDate'])));
        }
    }
    
    function isInvalidApiKey() {
        if (!$this->requestHasBeenExecuted) {
            return false;
        } else {
            return $this->userAccountInfo['GetAccountStatusResult']['Exception']['Type'] == 'InvalidApiKey';
        }
    }
    
}
