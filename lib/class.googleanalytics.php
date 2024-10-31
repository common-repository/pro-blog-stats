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

class BPRPGoogleAnalytics {

	var $profile;

	var $authToken;

	function BPRPGoogleAnalytics() {
            $this->path = dirname(__FILE__)."/";
            $this->enabled = false;
            $this->authToken = get_option ( 'bprp-ga-token' );
            $this->profile = get_option ( 'bprp-ga-profile' );
	}

	function reportsPage() {
		//include_once ('analytics-report.php');
	}

	function GASetupPage() {
		if ( ! $this->getRequirements() ) {
			print "Plugin requires curl and xml modules to be enabled in PHP. Please enable them.";
			return;
		}
		if ( isset ( $_POST ['save_ga'] ) ) {
			if (function_exists ( 'current_user_can' ) && ! current_user_can ( 'manage_options' )) {
				die ( __ ( 'Cheatin&#8217; uh?' ) );
			}
			update_option ( 'bprp-ga-profile', $_POST ['bprp-ga-profile'] );
			$this->profile = get_option ( 'bprp-ga-profile' );
		}
		if ( isset( $_REQUEST['token'] ) ) {
			if (function_exists ( 'current_user_can' ) && ! current_user_can ( 'manage_options' )) {
				die ( __ ( 'Cheatin&#8217; uh?' ) );
			}
            $this->authToken = $this->getAuthToken();
		}

		include_once ($this->path.'../tpl/ga-setup.php');
	}

	function getRequirements() {
		if (! defined ( 'XML_ERROR_NONE' )) {
			return FALSE;
		}
		if (! function_exists ( 'curl_init' )) {
			return FALSE;
		}
		return TRUE;
	}

	function getAuthToken() {
		if ( isset( $_REQUEST['token'] ) ) {
			$output = $this->fetchFeed( 'https://www.google.com/accounts/AuthSubSessionToken', $_REQUEST['token'] );

			if ( preg_match( '/Token=(.*)/', $output, $matches ) ) {
				if ( isset( $matches[1] ) )
					$this->authToken = $matches[1];
					update_option( 'bprp-ga-token', $this->authToken );
			}
			return $this->authToken;
		}

		if ( ! empty( $this->authToken ) )
			return $this->authToken;
	}

	function fetchFeed( $url, $token = '' ) {

		if( empty( $token ) ) {
			if ( empty( $this->authToken ) ) {
				return false;
			}

			$token = $this->authToken;
		}

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$curlheader[0] = sprintf("Authorization: AuthSub token=\"%s\"/n", $token );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $curlheader );
		$output = curl_exec ( $ch );
		$info = curl_getinfo ( $ch );

		if ( $info['http_code'] == 200 ) {
			return $output;
		} else {
			return false;
		}
	}

	function getProfiles() {
		$feedUrl = 'https://www.google.com/analytics/feeds/accounts/default';
		if ( ( $feedData = $this->fetchFeed( $feedUrl ) ) === false ) {
			return array ();
		}
		$doc = new DOMDocument ( );
		$doc->loadXML ( $feedData );
		$entries = $doc->getElementsByTagName( 'entry' );
		$profiles = array ();
		foreach ( $entries as $entry ) {
			$tableId = $entry->getElementsByTagName ( 'tableId' )->item ( 0 )->nodeValue;
			$profiles [$tableId] = array ();
			$profiles [$tableId] ["tableId"] = $tableId;
			$profiles [$tableId] ["title"] = $entry->getElementsByTagName ( 'title' )->item ( 0 )->nodeValue;
			$profiles [$tableId] ["entryid"] = $entry->getElementsByTagName ( 'id' )->item ( 0 )->nodeValue;
			$properties = $entry->getElementsByTagName ( 'property' );
			foreach ( $properties as $property ) {
				$profiles [$tableId] ['property'] [$property->getAttribute ( 'name' )] = $property->getAttribute ( 'value' );
			}
		}
		return $profiles;
	}

	function getAnalyticRecords($startDate, $endDate, $dimensions, $metrics, $sort = '', $maxResults = '', $filter = false) {

		$url = 'https://www.google.com/analytics/feeds/data';
		$url .= "?ids=" . $this->profile;
		$url .= "&start-date=" . $startDate;
		$url .= "&end-date=" . $endDate;
		$url .= "&dimensions=" . $dimensions;
		$url .= "&metrics=" . $metrics;

		if (! empty ( $sort )) {
			$url .= "&sort=" . $sort;
		}
                if ( $filter ) {
                    $url .= "&filters=$filter";
                }
		if (! empty ( $maxResults )) {
			$url .= "&max-results=" . $maxResults;
		}
		if (($feedData = $this->fetchFeed ( $url )) === FALSE) {
			return array ();
		}
		$doc = new DOMDocument ( );
		$doc->loadXML ( $feedData );
		$results = array ();

		$aggregates = $doc->getElementsByTagName ( 'aggregates' );
		foreach ( $aggregates as $aggregate ) {
			$metrics = $aggregate->getElementsByTagName ( 'metric' );
			foreach ( $metrics as $metric ) {
				$results ['aggregates'] ['metric'] [$metric->getAttribute ( 'name' )] = $metric->getAttribute ( 'value' );
			}
		}

		$entries = $doc->getElementsByTagName ( 'entry' );
		foreach ( $entries as $entry ) {
			$record = array ();
			$record ["title"] = $entry->getElementsByTagName ( 'title' )->item ( 0 )->nodeValue;
			$dimensions = $entry->getElementsByTagName ( 'dimension' );
			foreach ( $dimensions as $dimension ) {
				$record ['dimension'] [$dimension->getAttribute ( 'name' )] = $dimension->getAttribute ( 'value' );
			}
			$metrics = $entry->getElementsByTagName ( 'metric' );
			foreach ( $metrics as $metric ) {
				$record ['metric'] [$metric->getAttribute ( 'name' )] = $metric->getAttribute ( 'value' );
			}
			$results ['entry'] [] = $record;
		}
		return $results;
	}

}

