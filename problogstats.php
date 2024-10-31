<?php

/*
Plugin Name: Pro Blog Stats
Plugin URI: http://www.problogstats.com
Description: Provides a single location to monitor the performance and statistics for your blog.
Author: Pro Blog Stats
Version: 1.0.3
Author URI: http://www.problogstats.com
*/

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

define( 'BPRP_DEBUG', false );
define('BPRP_PATH', 'http://problogstats.com/');

require_once 'lib/class.user_accounts.php';
require_once 'lib/class.services.php';
require_once 'lib/class.googleanalytics.php';
require_once 'lib/class.aweber.php';
require_once 'lib/class.process.php';
require_once 'lib/class.setuserservices.php';


if (!class_exists('ProBlogStats')) {
	class ProBlogStats {

		var $version = '1.0';
		var $_option_bprpSettings = '_bprp_settings';
                var $_option_bprpServiceSettings = '_bprp_service_settings';
		var $_option_cachedUserInfo = '_bprp_cachedUserInfo';
                var $_option_cachedServiceInfo = '_bprp_cachedServiceInfo';
                var $_option_cachedCSVReport = '_bprp_cachedCSVReport';
		var $settings = null;
                var $service_settings = null;

		function ProBlogStats() {
			$this->addActions();
			wp_register_style('bprp_style', plugins_url('media/bprp_style.css', __FILE__), array(), $this->version);
		}

		function addActions() {

			add_action('admin_init', array(&$this, 'saveSettings'));
                        add_action('admin_menu', array(&$this, 'bprpMainMenu'));

			$settings = $this->getSettings();
			if ( empty($settings['api-key'])) {
				add_action('admin_notices', array(&$this, 'bprpNoticeOfAPIKey'));
			}

			// AJAX stuff
			add_action('wp_ajax_bprp_user_info', array(&$this, 'fetchUserInfo'));
		}

		function saveSettings() {
                    $settings = $this->getSettings();

                    if (isset($_POST['save_bprp-api-key']) && current_user_can('manage_options') && check_admin_referer('save_bprp-api-key')) {
                        $settings['api-key'] = trim(stripslashes($_POST['bprp-api-key']));
                        $settings['url'] = site_url();
                        $this->setSettings($settings);
                        wp_redirect(admin_url('admin.php?page=pro-blog-stats&updated=1'));
                        exit();
                    }

                    if(isset ($_GET['action']) && current_user_can('manage_options') && $_GET['action']== 'download') {
                        ob_start('ob_gzhandler');
                        header('Content-type: application/octet-stream');
                        $csvReport = get_option($this->_option_cachedCSVReport, array());
                        $fileName = $csvReport['csvfilename'];
                        header('Content-Disposition: attachment; filename="'.$fileName.'"');
                        $csvReportData = $csvReport['report'];
                        echo $csvReportData;
                        exit;
                    }

                    if (isset($_POST['save_bprp-services']) && current_user_can('manage_options') && check_admin_referer('save_bprp-services')) {
                        $serviceSettings = array();

                        /**
                         * Here goes the web service information creation for a users services.
                         */

                        $uhsSettings = array();
                        $twitter = array();
                        $clicky = array();

                        foreach ($_POST as $k => $v) {
                            switch ($k) {
                                case "feedburner_name":
                                    $serviceSettings['feedburner_name'] = trim(stripslashes($v));
                                    $feedb = array('feedburner_name' => trim(stripslashes($v) ) );
                                    $uhsSettings['feedburner'] = $feedb;
                                    break;
                                case "facebook_pageid":
                                    $serviceSettings['facebook_pageid'] = trim(stripslashes($v));
                                    $facebook = array('facebook_pageid' => trim(stripslashes($v) ) );
                                    $uhsSettings['facebook'] = $facebook;
                                    break;
                                case "twitter_username":
                                    $serviceSettings['twitter_username'] = trim(stripslashes($v));
                                    $twitter['twitter_username'] = trim(stripslashes($v));
									$twitter['twitter_password'] = '';
                                    break;
                                case "digg_username":
                                    $serviceSettings['digg_username'] = trim(stripslashes($v));
                                    $uhsSettings['digg'] = array('digg_username' => trim(stripslashes($v) ) );
                                    break;
                                case "postrank_feedHash":
                                    $serviceSettings['postrank_feedHash'] = trim(stripslashes($v));
                                    $uhsSettings['postrank'] = array('postrank_feedHash' => trim(stripslashes($v) ) );
                                    break;
                                case "clicky_siteid":
                                    $serviceSettings['clicky_siteid'] = trim(stripslashes($v));
                                    $clicky['clicky_siteid'] = trim(stripslashes($v));
                                    break;
                                case "clicky_sitekey":
                                    $serviceSettings['clicky_sitekey'] = trim(stripslashes($v));
                                    $clicky['clicky_sitekey'] = trim(stripslashes($v));
                                    break;
                                case "wp_apikey":
                                    $serviceSettings['wp_apikey'] = trim(stripslashes($v));
                                    $uhsSettings['wordpress'] = array('wp_apikey' => trim(stripslashes($v) ) );
                                    break;
                                case "klout_username":
                                    $serviceSettings['klout_username'] = trim(stripslashes($v));
                                    $uhsSettings['klout'] = array('klout_username' => trim(stripslashes($v) ) );
                                    break;
                                case "google_pagerank":
                                    $serviceSettings['google_pagerank'] = trim(stripslashes($v));
                                    $uhsSettings['googlepagerank'] = array('enabled' => 'yes');
                                     break;
                                case "google_indexed":
                                    $serviceSettings['google_indexed'] = trim(stripslashes($v));
                                    $uhsSettings['googleindexed'] = array('enabled' => 'yes');
                                    break;
                                case "alexa":
                                    $serviceSettings['alexa'] = trim(stripslashes($v));
                                    $uhsSettings['alexa'] = array('enabled' => 'yes');
                                    break;
                                case "yahoo_indexed":
                                    $serviceSettings['yahoo_indexed'] = trim(stripslashes($v));
                                    $uhsSettings['yahooindexed'] = array('enabled' => 'yes');
                                    break;
                                case "yahoo_inlinks":
                                    $serviceSettings['yahoo_inlinks'] = trim(stripslashes($v));
                                    $uhsSettings['yahooinlinks'] = array('enabled' => 'yes');
                                    break;
                                case "bing":
                                    $serviceSettings['bing'] = trim(stripslashes($v));
                                    $uhsSettings['bing'] = array('enabled' => 'yes');
                                    break;
                                case "compete":
                                    $serviceSettings['compete'] = trim(stripslashes($v));
                                    $uhsSettings['compete'] = array('enabled' => 'yes');
                                    break;
                                case "dmoz":
                                    $serviceSettings['dmoz'] = trim(stripslashes($v));
                                    $uhsSettings['dmoz'] = array('enabled' => 'yes');
                                    break;
                                case "quantcast":
                                    $serviceSettings['quantcast'] = trim(stripslashes($v));
                                    $uhsSettings['quantcast'] = array('enabled' => 'yes');
                                    break;
                                case "prweb":
                                    $serviceSettings['prweb'] = trim(stripslashes($v));
                                    $uhsSettings['prweb'] = array('enabled' => 'yes');
                                    break;
                                case "stumble":
                                    $serviceSettings['stumble'] = trim(stripslashes($v));
                                    $uhsSettings['stumble'] = array('enabled' => 'yes');
                                    break;
                            }
                        }

                        $uhsSettings['twitter'] = $twitter;
                        $uhsSettings['clicky'] = $clicky;

                        $insideProcessObj = new BPRPProcess();
                        $uhsSettingsString = $insideProcessObj->makehumanunreadable($uhsSettings);

                        $bprpServerInput = array('apikey' => $settings['api-key'],
                                                'url' => site_url(),
                                                'servicesdata' => $uhsSettingsString
                                            );

                        $setServiceObj = new BPRPUserServices($bprpServerInput);
                        $setServiceObj->requestSetServices();
                        if($setServiceObj->hasError()) {
                            wp_redirect(admin_url('admin.php?page=pro-blog-stats&error=setservice'));
                            exit();
                        }
                        $this->setServiceSettings($serviceSettings);
                        wp_redirect(admin_url('admin.php?page=pro-blog-stats&updated=2'));
                        exit();
                    }
				}

                function setCSVReport($data) {
                    update_option($this->_option_cachedCSVReport, $data);
                }

                function setSettings($settings) {
                    if (!is_array($settings)) {
                            return;
                    }
                    $this->settings = $settings;
                    update_option($this->_option_bprpSettings, $this->settings);
				}

                function setServiceSettings($settings) {
                    if (!is_array($settings)) {
                            return;
                    }
                    $this->service_settings = $settings;
                    update_option($this->_option_bprpServiceSettings, $this->service_settings);
				}

				function getSettings() {
                    if ( null === $this->settings ) {
                        $this->settings = get_option( $this->_option_bprpSettings, array() );
                        $this->settings = is_array( $this->settings ) ? $this->settings : array();
                    }
                    return $this->settings;
				}

                function getServiceSettings() {
                    if ( null === $this->service_settings ) {
                        $this->service_settings = get_option( $this->_option_bprpServiceSettings, array() );
                        $defaults = array(
							'feedburner_name' => '',
							'facebook_pageid' => '',
							'twitter_username' => '',
							'digg_username' => '',
							'postrank_feedHash' => '',
							'clicky_siteid' => '',
							'clicky_sitekey' => '',
							'wp_apikey' => '',
							'klout_username' => '',
							'google_pagerank' => '',
							'google_indexed' => '',
							'alexa' => '',
							'yahoo_indexed' => '',
							'yahoo_inlinks' => '',
							'bing' => '',
							'compete' => '',
							'dmoz' => '',
							'quantcast' => '',
							'prweb' => '',
							'stumble' => ''
						);
                        $this->service_settings = is_array( $this->service_settings ) ? $this->service_settings : array();
                        $this->service_settings = wp_parse_args( $this->service_settings, $defaults );
                    }
                    return $this->service_settings;
				}

                function bprpMainMenu() {
                    $icon = $this->bprpPluginPath().'media/logo.png';
                    add_menu_page('Pro Blog Stats', 'Pro Blog Stats', 'administrator', 'pro-blog-stats', array( $this, 'bprpSetupPage'), $icon);    // Add a submenu to the top-level menu:
                    add_submenu_page('pro-blog-stats','Pro Blog Stats Setup', 'Setup', 'administrator', 'pro-blog-stats', array( $this, 'bprpSetupPage'));
                    add_submenu_page('pro-blog-stats', 'Performance Report', 'Report', 'administrator', 'pro-blog-stats-report', array( $this, 'bprpReportPage'));
                    add_submenu_page('pro-blog-stats', 'Pro Blog Stats Help', 'Help', 'administrator', 'pro-blog-stats-help', array( $this, 'bprpHelpPage'));
                }

                function bprpPluginPath() {
                        return plugins_url('', __FILE__).'/';
                }

                function bprpSetupPage() {
                    include ('tpl/settings.php');
                }

                function bprpReportPage() {
                    include ('tpl/reports.php');
                }

                function bprpHelpPage() {
                    include ('tpl/help.php');
                }

                function bprpNoticeOfAPIKey() {
                    print '<div id="bprp-no-api-key" class="error"><p>'.sprintf(__('Your Pro Blog Stats API Key is Empty.  Please <a href="%s">configure the Pro Blog Stats plugin</a>.'), admin_url('admin.php?page=pro-blog-stats')).'</p></div>';
                }

		function sanitizeForCall($value) {
			return str_replace(array('<![CDATA[',']]>'),array('',''),trim($value));
		}

		function fetchUserInfo() {
			$userInfo = $this->getUserInfo(true);
			include('tpl/account-info.php');
			exit();
		}

                function getWordCount($statement, $attribute, $countAttribute, $avg = 0) {
                    global $wpdb;
                    $result=0;

                    $countStatement = "SELECT COUNT(".$countAttribute.") " .$statement;
                    $counter = $wpdb->get_var($countStatement);
                    $startLimit = 0;

                    $rows_at_Once=$counter;

                    $incrementStatement = "SELECT ".$attribute." ".$statement;

                    $intermedcount = 0;

                    while( $startLimit < $counter) {
                        $query = $incrementStatement." LIMIT ".$startLimit.", ".$rows_at_Once;
                        $results = $wpdb->get_col($query);
                        //count the words for each statement
                        $intermedcount += count($results);
                        for ($i=0; $i<count($results); $i++) {
                                $sum = str_word_count($results[$i]);
                                if ($avg == 0) {
                                        $result += $sum;
                                } else {
                                        $intermed += ($sum*$sum);
                                }
                        }
                        $startLimit+=$rows_at_Once;
                    }
                    if ($avg != 0) {
                        $result = sqrt($intermed/$intermedcount);
                    }
                    return $result;
                }

                function getBlogStatics($startdate, $enddate) {
                    global $wpdb;
                    $options = get_option('BlogMetricsOptions');
                    $periodquery = '';

                    $periodquery = " AND p.post_date  BETWEEN '{$startdate}' AND '{$enddate}'";


                    $postsquery = "SELECT COUNT(ID) FROM $wpdb->posts p WHERE p.post_type = 'post' AND p.post_status='publish'".$periodquery;

                    $commentfromwhere 	="FROM $wpdb->comments c, $wpdb->posts p, $wpdb->users u "
                                                ."WHERE c.comment_approved = '1'"
                                                ." AND c.comment_author_email != u.user_email"
                                                ." AND c.comment_post_ID = p.ID"
                                                ." AND c.comment_type = ''"
                                                ." AND p.post_type = 'post'"
                                                ." AND p.post_author = u.ID"
                                                .$periodquery;

                    $commentsquery = "SELECT COUNT(c.comment_ID) ".$commentfromwhere;

                    $postwordsquery = "FROM $wpdb->posts p WHERE p.post_status = 'publish' AND p.post_type = 'post'".$periodquery;

                    $stats['posts'] = $wpdb->get_var($postsquery);
                    $stats['comments'] = $wpdb->get_var($commentsquery);
                    $stats['postwords'] = $this->getWordCount($postwordsquery,"post_content","ID");
                    return $stats;
                }

                function getNextDate($given_date,$day=0,$mth=0,$yr=0) {
                    $given_timestamp = strtotime($given_date);
                    $new_date = date('Y-m-d h:i:s',
                                    mktime( date('h',$given_timestamp),
                                        date('i',$given_timestamp),
                                        date('s',$given_timestamp),
                                        date('m',$given_timestamp)+$mth,
                                        date('d',$given_timestamp)+$day,
                                        date('Y',$given_timestamp)+$yr
                                    )
                                );

                    return $new_date;
                }

		function getFormattedDateRanges($startdate, $enddate) {
                    $startD = date("d", strtotime($startdate));
                    $startM = date("m", strtotime($startdate));
                    $startY = date("Y", strtotime($startdate));
                    $endD = date("d", strtotime($enddate));
                    $endM = date("m", strtotime($enddate));
                    $endY = date("Y", strtotime($enddate));
                    $f_startdate = $startM.'/'.$startD.'/'.$startY;
                    $f_enddate = $endM.'/'.$endD.'/'.$endY;
                    $formatedDates = array('start_date' => $f_startdate, 'end_date' => $f_enddate);
                    return $formatedDates;
                }

                function getFormattedMysqlDateRanges($startdate, $enddate) {
                    $startdateArr = @explode('/', $startdate);
                    $enddateArr = @explode('/', $enddate);
                    $startD = $startdateArr[1];
                    $startM = $startdateArr[0];
                    $startY = $startdateArr[2];
                    $endD = $enddateArr[1];
                    $endM = $enddateArr[0];
                    $endY = $enddateArr[2];
                    $f_startdate = $startY.'-'.$startM.'-'.$startD;
                    $f_enddate = $endY.'-'.$endM.'-'.$endD;
                    $formatedDates = array('start_date' => $f_startdate, 'end_date' => $f_enddate);
                    return $formatedDates;
                }

		function getNumberEvaluationsRemaining() {
			$userInfo = $this->getUserInfo(false);
			if(is_wp_error($userInfo)) {
				return '...';
			} else {
				return $userInfo->getCreditsRemaining();
			}
		}

		function getUserInfo($live = false) {
			$settings = $this->getSettings();
			if ($live) {

				if ( empty($settings['api-key'])) {
                                    delete_option($this->_option_cachedUserInfo);
                                    return new WP_Error(-1, __('You must set your Pro Blog Stats API Key.'));
				} else {

                                    $userAccountAccess = new BPRPUserAccount($settings['api-key'], $settings['url']);
                                    $userAccountAccess->UserAccountStatus();

                                    if ($userAccountAccess->hasError()) {
                                        return new WP_Error($userAccountAccess->getErrorMessage());
                                    } else {
                                        update_option($this->_option_cachedUserInfo, $userAccountAccess->getRawResults());
                                        return $userAccountAccess->getRawResults();
                                    }
				}
			} else {
				$userAccountInfo = get_option($this->_option_cachedUserInfo);
				if (!$userAccountInfo) {
					return new WP_Error(-100, __('Fetching Information...'));
				} else {
					return $userAccountInfo;
				}
			}
		}

                function getUserServices($startdate, $enddate, $live = false) {
                    $settings = $this->getSettings();
                    if ($live) {
                        if ( empty($settings['api-key']) ) {
                            delete_option($this->_option_cachedServiceInfo);
                            return new WP_Error(-1, __('You must set your Pro Blog Stats API KEY.'));
                        } else {
                            $userServiceObj = new BPRPService($settings['api-key'], $settings['url'], $startdate, $enddate );
                            $userServiceObj->requestServicesReport();

                            if ($userServiceObj->hasError()) {
                                return new WP_Error($userServiceObj->getErrorMessage());
                            } else {
                                update_option($this->_option_cachedServiceInfo, $userServiceObj->getServiceResults());
                                return $userServiceObj->getServiceResults();
                            }
                        }
                    } else {
                        $userServiceInfo = get_option($this->_option_cachedServiceInfo);
                        if (!$userServiceInfo) {
                                return new WP_Error(-100, __('Fetching Information...'));
                        } else {
                                return $userServiceInfo;
                        }
                    }
		}

		function getPermissionLevel() {
			$settings = $this->getSettings();
			if(empty($settings['permissions-level'])) {
				$settings['permissions-level'] = 'administrator';
				$this->setSettings($settings);
			}
			return $settings['permissions-level'];
		}


	}

	$bprp = new ProBlogStats();

}
