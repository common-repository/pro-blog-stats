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

class BPRPAweber {

	var $lists;

	var $authToken;

	function BPRPAweber() {
		$this->path = dirname(__FILE__)."/";
		$this->enabled = false;
		$this->authToken = get_option( 'bprp-awb-token' );
		$this->lists = unserialize( get_option( 'bprp-awb-lists' ) );
		if ( ! is_array( $this->lists ) )
			$this->lists = array();
		$this->application_id = '4be1c6a5'; //'38c5c09a';

		include_once ( $this->path . 'aweber_api/aweber_api.php' );
	}

	function AWBSetupPage() {
		if ( isset ( $_POST ['save_awb'] ) ) {
			if (function_exists ( 'current_user_can' ) && ! current_user_can ( 'manage_options' )) {
				die ( __ ( 'Cheatin&#8217; uh?' ) );
			}
			delete_transient( 'aweber_lists' );
			update_option ( 'bprp-awb-token', stripslashes( $_POST['bprp-awb-token'] ) );
			if ( isset( $_POST['bprp-awb-lists'] ) )
				update_option ( 'bprp-awb-lists', serialize( $_POST['bprp-awb-lists'] ) );
			$this->authToken = get_option ( 'bprp-awb-token' );
			$this->lists = maybe_unserialize( get_option( 'bprp-awb-lists' ) );
			if ( ! is_array( $this->lists ) )
				$this->lists = array();
		}

		include_once ($this->path.'../tpl/awb-setup.php');
	}

	function get_account() {
		if ( ! empty( $this->account ) )
			return $this->account;

		if ( ! empty( $this->authToken ) )
            $this->authToken = get_option( 'bprp-awb-token' );

        $accesstoken = get_option( 'bprp-awb-accesstoken' );
        $accesstokensecret = get_option( 'bprp-awb-accesstokensecret' );

        if ( ! empty( $this->authToken ) && ! empty( $accesstoken ) && ! empty( $accesstokensecret ) ) {
			$data = split('\|', $this->authToken );
			try {
				$aweber = new AWeberAPI( $data[0], $data[1] );
				$this->account = $aweber->getAccount( $accesstoken, $accesstokensecret );
			} catch ( Exception $e ) {
				_e( '<div id="message" class="error"><p><strong>The Aweber authorization code is not valid. Please enter a new one.</strong></p></div>' );
				delete_option( 'bprp-awb-token' );
				delete_option( 'bprp-awb-accesstoken' );
				delete_option( 'bprp-awb-accesstokensecret' );
			}

			return $this->account;
		}

		if ( ! empty( $this->authToken ) ) {
			try {
				$data = AWeberAPI::getDataFromAweberID( $this->authToken );

				update_option( 'bprp-awb-accesstoken', $data[2] );
				update_option( 'bprp-awb-accesstokensecret', $data[3] );

				$aweber = new AWeberAPI( $data[0], $data[1] );
				$this->account = $aweber->getAccount( get_option ( 'bprp-awb-accesstoken' ), get_option ( 'bprp-awb-accesstokensecret' ) );

				return $this->account;
			} catch ( Exception $e ) {
				_e( '<div id="message" class="error"><p><strong>The Aweber authorization code is not valid. Please enter a new one.</strong></p></div>' );

				delete_option( 'bprp-awb-accesstoken' );
				delete_option( 'bprp-awb-accesstokensecret' );
				delete_option( 'bprp-awb-lists' );
				delete_transient( 'aweber_lists' );
			}
		}
	}

	function get_lists() {
		if ( false === ( $aweber_lists = get_transient('aweber_lists') ) ) {
			if ( $account = $this->get_account() ) {
				$aweber_lists = $account->lists;
				set_transient( 'aweber_lists', $aweber_lists, 60 * 15 );
			}
		}
		
		return $aweber_lists;
	}
	
}

