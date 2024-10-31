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

class BPRPProcess {

    public $_searchEngineStats;
    public $_ranks;
    public $_socialMedia;
    public $_others;
    public $_analytics;
    public $_overallperformance;
    private $_positive_stats;
    private $_negative_stats;
    private $_same_stats;
    public $_servicecount;
    public $_blogstatics;
    public $_sitemap;
    public $_csvreport;


    public function BPRPProcess() {
        $this->_ranks = "";
        $this->_searchEngineStats = "";
        $this->_socialMedia = "";
        $this->_others = "";
        $this->_analytics = "";
        $this->_blogstatics = "";
        $this->_sitemap = "";
        $this->_csvreport = "Data Type, Start, End, Change \n";
        $this->_overallperformance = 0;
        $this->_positive_stats = 0;
        $this->_negative_stats = 0;
        $this->_same_stats = 0;
        $this->_servicecount = 0;


    }

    public function processBlogStatistics($previous, $current) {

        if(isset ($previous) && is_array($previous) ) {
            $pre_posts = trim(@$previous['posts']);
            $pre_comments = trim(@$previous['comments']);
            $pre_postwords = trim(@$previous['postwords']);
        } else {
            $pre_posts = 'N/A';
            $pre_comments = 'N/A';
            $pre_postwords = 'N/A';
        }

        if(isset ($current) && is_array($current) ) {
            $new_posts = trim(@$current['posts']);
            $new_comments = trim(@$current['comments']);
            $new_postwords = trim(@$current['postwords']);
        } else {
            $new_posts = 'N/A';
            $new_comments = 'N/A';
            $new_postwords = 'N/A';
        }

        $chg_posts = $this->getChangePercentage($pre_posts, $new_posts);
        $chg_comments = $this->getChangePercentage($pre_comments, $new_comments);
        $chg_postwords = $this->getChangePercentage($pre_postwords, $new_postwords);

        $this->_others .= "<tr><td>Blog Statistics: Posts</td><td>{$pre_posts}</td><td>{$new_posts}</td><td>{$chg_posts} </td></tr>";
        $this->_others .= "<tr><td>Blog Statistics: Comments</td><td>{$pre_comments}</td><td>{$new_comments}</td><td>{$chg_comments} </td></tr>";
        $this->_others .= "<tr><td>Blog Statistics: Words/Post</td><td>{$pre_postwords}</td><td>{$new_postwords}</td><td>{$chg_postwords} </td></tr>";

        $this->addRecordToCSV('Blog Statistics: Posts', $pre_posts, $new_posts, $chg_posts);
        $this->addRecordToCSV('Blog Statistics: Comments', $pre_comments, $new_comments, $chg_comments);
        $this->addRecordToCSV('Blog Statistics: Words/Post', $pre_postwords, $new_postwords, $chg_postwords);

    }

    public function processSitemap($start_date, $end_date) {
        global $post;
	$wp_posts = get_posts('numberposts=10000');
	$rp_stime = strtotime($start_date);
	$rp_etime = strtotime($end_date);
	$rp_start = 0 ;
	$rp_end = 0;

	if(sizeof($wp_posts>0)){
            foreach ($wp_posts as $my_rp) {
                $modifyd_time = $my_rp->post_modified;
                $rp_modifyd_time = explode(' ',$modifyd_time);
                $rp_extime = $rp_modifyd_time[0];
                $rp_extime_str = strtotime($rp_extime);

                if($rp_extime_str <= $rp_stime) {
                    $rp_start = $rp_start+1;
                }
                if($rp_extime_str <= $rp_etime) {
                    $rp_end = $rp_end+1;
                }
            }
	}

	$tot_page = get_pages();
	$pg_start = 0 ;
	$pg_end = 0 ;

	if(sizeof($tot_page>0)){
            foreach ($tot_page as $my_pg) {
                $pg_modifyd_time = $my_pg->post_modified;
                $pg_modifyd_time = explode(' ',$pg_modifyd_time);
                $pg_extime = $pg_modifyd_time[0];
                $pg_extime_str = strtotime($pg_extime);

                if($pg_extime_str <= $rp_stime) {
                    $pg_start = $pg_start+1;
                }
                if($rp_extime_str <= $rp_etime) {
                    $pg_end = $pg_end+1;
                }
            }
	}

	$net_start = $rp_start+$pg_start+1;
        $net_start = trim($net_start);
	$net_end = $rp_end+$pg_end+1;
        $net_end = trim($net_end);
        $net_change = $this->getChangePercentage($net_start, $net_end);
        //$this->_sitemap .= "<tr><td>Total Number Of Pages</td><td>{$net_start}</td><td>{$net_end}</td><td>{$net_change} </td></tr>";
        $this->_searchEngineStats .= "<tr><td>Total Number Of Pages on site</td><td>{$net_start}</td><td>{$net_end}</td><td>{$net_change} </td></tr>";
        $this->addRecordToCSV('Total Number Of Pages on site', $net_start, $net_end, $net_change);

    }

    public function processGoogleAnalyticsStats($previous, $current) {
        if(isset ($previous) && is_array($previous) ) {
            $pre_totalvisits = trim(@number_format(@$previous['ga:visits']) );
            $pre_pageviews = trim(@number_format(@$previous['ga:pageviews']) );
            $pre_newvisits = trim(@number_format(@$previous['ga:newVisits']) );
            $pre_pagevisits = trim(@number_format(@$previous['ga:pageviews'] / $previous['ga:visits'], 2) );
            $pre_bouncerate = trim(@number_format(@$previous['ga:bounces'] / $previous['ga:entrances'] * 100, 2) );
        } else {
            $pre_totalvisits = 'N/A';
            $pre_pageviews = 'N/A';
            $pre_newvisits = 'N/A';
            $pre_pagevisits = 'N/A';
            $pre_bouncerate = 'N/A';
        }

        if(isset ($current) && is_array($current) ) {
            $new_totalvisits = trim(@number_format(@$current['ga:visits']) );
            $new_pageviews = trim(@number_format(@$current['ga:pageviews']) );
            $new_newvisits = trim(@number_format(@$current['ga:newVisits']) );
            $new_pagevisits = trim(@number_format(@$current['ga:pageviews'] / $current['ga:visits'], 2) );
            $new_bouncerate = trim(@number_format(@$current['ga:bounces'] / $current['ga:entrances'] * 100, 2) );
        } else {
            $new_totalvisits = 'N/A';
            $new_pageviews = 'N/A';
            $new_newvisits = 'N/A';
            $new_pagevisits = 'N/A';
            $new_bouncerate = 'N/A';
        }

        $chg_totalvisits = $this->getChangePercentage($pre_totalvisits, $new_totalvisits);
        $chg_pageviews = $this->getChangePercentage($pre_pageviews, $new_pageviews);
        $chg_newvisits = $this->getChangePercentage($pre_newvisits, $new_newvisits);
        $chg_pagevisits = $this->getChangePercentage($pre_pagevisits, $new_pagevisits);
        // A lower bounce rate is better
        $chg_bouncerate = $this->getChangePercentage($new_bouncerate, $pre_bouncerate);

        $this->_analytics .= "<tr><td>Google Analytics: Total Visits</td><td>{$pre_totalvisits}</td><td>{$new_totalvisits}</td><td>{$chg_totalvisits} </td></tr>";
        $this->_analytics .= "<tr><td>Google Analytics: New Visits</td><td>{$pre_newvisits}</td><td>{$new_newvisits}</td><td>{$chg_newvisits} </td></tr>";
        $this->_analytics .= "<tr><td>Google Analytics: Pageviews</td><td>{$pre_pageviews}</td><td>{$new_pageviews}</td><td>{$chg_pageviews} </td></tr>";
        $this->_analytics .= "<tr><td>Google Analytics: Pages/Visit</td><td>{$pre_pagevisits}</td><td>{$new_pagevisits}</td><td>{$chg_pagevisits} </td></tr>";
        $this->_analytics .= "<tr><td>Google Analytics: Bounce Rate</td><td>{$pre_bouncerate} %</td><td>{$new_bouncerate} %</td><td>{$chg_bouncerate} </td></tr>";

        $this->addRecordToCSV('Google Analytics: Total Visits', $pre_totalvisits, $new_totalvisits, $chg_totalvisits);
        $this->addRecordToCSV('Google Analytics: New Visits', $pre_newvisits, $new_newvisits, $chg_newvisits);
        $this->addRecordToCSV('Google Analytics: Pageviews', $pre_pageviews, $new_pageviews, $chg_pageviews);
        $this->addRecordToCSV('Google Analytics: Pages/Visit', $pre_pagevisits, $new_pagevisits, $chg_pagevisits);
        $this->addRecordToCSV('Google Analytics: Bounce Rate', $pre_bouncerate, $new_bouncerate, $chg_bouncerate);

    }

    public function drawGoogleAnalyticsCharts($records, $sourceRecords, $keywordsRecords, $topContent, $topReferrers) {
        # Call the JSAPI to show the charts
        $content = "<tr><td colspan='4' style='border:0; padding:0;'>";
        $content .= "<script type='text/javascript' src='http://www.google.com/jsapi'></script>";

        # Add the time line graph to the analytics section
        $content .= "<script type='text/javascript'>
                    /* <![CDATA[ */
                    google.load('visualization', '1', {packages:['annotatedtimeline','piechart', 'geomap','table']});
                    google.setOnLoadCallback(gaChartTimeline);
                    function gaChartTimeline() {
                        var gaData = new google.visualization.DataTable();
                        gaData.addColumn('date', 'Date');
                        gaData.addColumn('number', 'Visits');
                        gaData.addColumn('number', 'Pageviews');
                        gaData.addColumn('number', 'Visitors');
                        gaData.addColumn('number', 'New Visits');
                        gaData.addRows(".count($records['entry']).");";

                        if (! empty ($records['entry'] )) {
                            $row = 0;
                            $script = '';
                            foreach ( $records['entry'] as $record ) {
                                $date = date ( 'Y,m-1,d', strtotime ( $record['dimension']['ga:date'] ) );
                                $script .= "gaData.setValue({$row}, 0, new Date({$date}));gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});gaData.setValue({$row}, 2, {$record['metric']['ga:pageviews']});gaData.setValue({$row}, 3, {$record['metric']['ga:visitors']});gaData.setValue({$row}, 4, {$record['metric']['ga:newVisits']});";
                                $row ++;
                            }
                        }
        $content .= $script;
        $content .= "   var gaVisitsPageviewsChart = new google.visualization.AnnotatedTimeLine(document.getElementById('chartTimeline'));
                        gaVisitsPageviewsChart.draw(gaData, {wmode: 'transparent',
                            displayZoomButtons: false,
                            displayAnnotations: true,
                            displayRangeSelector: false
                        });
                    }";

        # Add the source information to the analytics section
        $content .= "google.setOnLoadCallback(gaTableSource);
                    function gaTableSource(){
                        var gaData = new google.visualization.DataTable();
                        gaData.addColumn('string', 'Source');
                        gaData.addColumn('number', 'Visits');
                        gaData.addRows(".count($sourceRecords['entry']).");";

                        if (! empty ( $sourceRecords ['entry'] )) {
                            $row = 0;
                            $script = '';
                            foreach ( $sourceRecords ['entry'] as $record ) {
                                $script .= "gaData.setValue({$row}, 0, \"".esc_js($record['dimension']['ga:source'])."\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
                                $row ++;
                            }
                        }
        $content .= $script;
        $content .= "   var chart = new google.visualization.PieChart(document.getElementById('chartSource'));
                        chart.draw(gaData, {width: 680, height: 300,is3D:true, title: 'Top Traffic Sources (Click on the pie chart segments to show the numbers)'});
                    }";

        # Add the keyword information to the analytics section
        $content .= "google.setOnLoadCallback(gaTableKeywords);
                    function gaTableKeywords(){
                        var gaData = new google.visualization.DataTable();
                        gaData.addColumn('string', 'Keywords');
                        gaData.addColumn('number', 'Visits');
                        gaData.addColumn('number', 'Pages/Visit');
                        gaData.addRows(".count($keywordsRecords['entry']).");";

                        if (! empty ( $keywordsRecords ['entry'] )) {
                            $row = 0;
                            $script = '';
                            foreach ( $keywordsRecords ['entry'] as $record ) {
                                $script .= "gaData.setValue({$row}, 0, \"".esc_js(urldecode($record['dimension']['ga:keyword']) )."\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
                                $p_v  = @number_format(@$record['metric']['ga:pageviews'] / $record['metric']['ga:visits'], 2);
                                $script .= "gaData.setValue({$row}, 2, {$p_v});";
                                $row ++;
                            }
                        }
        $content .= $script;
        $content .= "   var table = new google.visualization.Table(document.getElementById('chartKeywords'));
                        table.draw(gaData, {width: 680, height: 320, pageSize:10,page:'disable',showRowNumber: true});
                    }";

        # Add the top content information to the analytics section
        $content .= "google.setOnLoadCallback(gaTableTopContent);
                    function gaTableTopContent(){
                        var gaData = new google.visualization.DataTable();
                        gaData.addColumn('string', 'Top Content');
                        gaData.addColumn('number', 'Unique Pageviews');
                        gaData.addColumn('number', 'Pageviews');
                        gaData.addRows(".count($topContent['entry']).");";

                        if (! empty ( $topContent['entry'] )) {
                            $row = 0;
                            $script = '';
                            foreach ( $topContent['entry'] as $record ) {
                                $script .= "gaData.setValue({$row}, 0, \"".esc_js($record['dimension']['ga:pagePath'])."\" );gaData.setValue({$row}, 1, {$record['metric']['ga:uniquePageviews']});";
                                $script .= "gaData.setValue({$row}, 2, {$record['metric']['ga:pageviews']});";
                                $row ++;
                            }
                        }
         $content .= $script;
         $content .= "	var table = new google.visualization.Table(document.getElementById('chartTopContent'));
                        table.draw(gaData, {width: 680, height: 320, pageSize:10,page:'disable',showRowNumber: true});
                    }";

         # Add the top referrals information to the analytics section
        $content .= "google.setOnLoadCallback(gaTableTopReferrals);
                    function gaTableTopReferrals(){
                        var gaData = new google.visualization.DataTable();
                        gaData.addColumn('string', 'Top Referrers');
                        gaData.addColumn('number', 'Visits');
                        gaData.addColumn('number', 'Pages/Visit');
                        gaData.addRows(".count($topReferrers['entry']).");";

                        if (! empty ( $topReferrers ['entry'] )) {
                            $row = 0;
                            $script = '';
                            foreach ( $topReferrers['entry'] as $record ) {
                                $script .= "gaData.setValue({$row}, 0, \"".esc_js($record['dimension']['ga:source'])."\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
				$p_v  = @number_format(@$record['metric']['ga:pageviews'] / $record['metric']['ga:visits'], 2);
				$script .= "gaData.setValue({$row}, 2, {$p_v});";
                                $row ++;
                            }
                        }

         $content .= $script;
         $content .= "	var table = new google.visualization.Table(document.getElementById('chartTopReferral'));
                        table.draw(gaData, {width: 680, height: 320, pageSize:10,page:'disable',showRowNumber: true});
                    }";


        $content .= " /* ]]> */
                    </script></td></tr>";
        # Add the corresponding divs with its ids to implement the jsapi
        $content .= "<tr><td colspan='4'><div id=\"chartTimeline\" style=\"height:320px; width:680px;\"></div><br></td></tr>";
        $content .= "<tr><td colspan='4'><div id=\"chartSource\" style=\"height:300px;\"></div><br></td></tr>";
        $content .= "<tr><td colspan='4'><div id=\"chartKeywords\" style=\"height:320px;\"></div><br></td></tr>";
        $content .= "<tr><td colspan='4'><div id=\"chartTopContent\" style=\"height:320px;\"></div><br></td></tr>";
	$content .= "<tr><td colspan='4'><div id=\"chartTopReferral\" style=\"height:320px;\"></div><br></td></tr>";

        $this->_analytics .= $content;
    }

    public function drawOverallPerformaceChart() {
        $value = $this->getOverallPerformaceAvg();
        $showedVal = $value;
        $value = @number_format($value, 2);

        $content = '<div class="wrap"><h2>Blog\'s Overall Performance Chart</h2>';
        $content.= "<table align='center' cellpadding=\"0\" cellspacing=\"1\" border=\"0\">
			<tr>
                            <td width='150'>&nbsp;</td>
                            <td><div ><img alt=\"Overall Performance Chart\" src=\"http://chart.apis.google.com/chart?chs=675x200&amp;chma=0,0,20,0|80,20&amp;chxt=y&amp;chxr=0,-100,100&amp;chf=bg,s,00000000&amp;cht=gom&amp;chd=t:{$showedVal}&amp;chds=-100,100&amp;chls=3,5,5|10&amp;chl={$value}% + Overall Performance\"></div></td>
			</tr>
                    </table></div>";

        echo $content;
    }


    public function processServices($prev, $curr, $allservices) {
        $previous = $this->makehumanreadable($prev);
        $current = $this->makehumanreadable($curr);

        if(in_array('google_page_rank', $allservices)) {
            if(isset ($previous['googlepagerank']) && is_array($previous['googlepagerank']) ) {
                $pre_googlepagerank = trim(@$previous['googlepagerank']['googlerank']);
            } else {
                $pre_googlepagerank = 'N/A';
            }
            if(isset ($current['googlepagerank']) && is_array($current['googlepagerank']) ) {
                $new_googlepagerank = trim(@$current['googlepagerank']['googlerank']);
            } else {
                $new_googlepagerank = 'N/A';
            }
            $chg_googlepagerank = $this->getChangePercentage($pre_googlepagerank, $new_googlepagerank);
            $this->_searchEngineStats .= "<tr><td>Google: PageRank</td><td>{$pre_googlepagerank}</td><td>{$new_googlepagerank}</td><td>{$chg_googlepagerank} </td></tr>";
            $this->addRecordToCSV('Google: PageRank', $pre_googlepagerank, $new_googlepagerank, $chg_googlepagerank);
        }

        if(in_array('google_indexed', $allservices)) {
            if(isset ($previous['googleindexed']) && is_array($previous['googleindexed']) ) {
                $pre_googleindexed = trim(@$previous['googleindexed']['googleindex_pages']);
            } else {
                $pre_googleindexed = 'N/A';
            }
            if(isset ($current['googleindexed']) && is_array($current['googleindexed']) ) {
                $new_googleindexed = trim(@$current['googleindexed']['googleindex_pages']);
            } else {
                $new_googleindexed = 'N/A';
            }
            $chg_googleindexed = $this->getChangePercentage($pre_googleindexed, $new_googleindexed );
            $this->_searchEngineStats .= "<tr><td>Google: Pages Indexed</td><td>{$this->getFormatedNumber($pre_googleindexed)}</td><td>{$this->getFormatedNumber($new_googleindexed)}</td><td>{$chg_googleindexed} </td></tr>";
            $this->addRecordToCSV('Google: Pages Indexed', $pre_googleindexed, $new_googleindexed, $chg_googleindexed);
        }

        if(in_array('yahoo_indexed', $allservices)) {
            if(isset ($previous['yahooindexed']) && is_array($previous['yahooindexed']) ) {
                $pre_yahooindexed = trim(@$previous['yahooindexed']['yahooindex_pages']);
            } else {
                $pre_yahooindexed = 'N/A';
            }
            if(isset ($current['yahooindexed']) && is_array($current['yahooindexed']) ) {
                $new_yahooindexed = trim(@$current['yahooindexed']['yahooindex_pages']);
            } else {
                $new_yahooindexed = 'N/A';
            }
            $chg_yahooindexed = $this->getChangePercentage($pre_yahooindexed, $new_yahooindexed);
            $this->_searchEngineStats .= "<tr><td>Yahoo: Pages Indexed</td><td>{$this->getFormatedNumber($pre_yahooindexed)}</td><td>{$this->getFormatedNumber($new_yahooindexed)}</td><td>{$chg_yahooindexed} </td></tr>";
            $this->addRecordToCSV('Yahoo: Pages Indexed', $pre_yahooindexed, $new_yahooindexed, $chg_yahooindexed);
        }

        if(in_array('yahoo_inlink', $allservices)) {
            if(isset ($previous['yahooinlink']) && is_array($previous['yahooinlink']) ) {
                $pre_yahooinlink = trim(@$previous['yahooinlink']['yahooinlinks']);
            } else {
                $pre_yahooinlink = 'N/A';
            }
            if(isset ($current['yahooinlink']) && is_array($current['yahooinlink']) ) {
                $new_yahooinlink = trim(@$current['yahooinlink']['yahooinlinks']);
            } else {
                $new_yahooinlink = 'N/A';
            }
            $chg_yahooinlink = $this->getChangePercentage($pre_yahooinlink, $new_yahooinlink);
            $this->_searchEngineStats .= "<tr><td>Yahoo: InLinks</td><td>{$this->getFormatedNumber($pre_yahooinlink)}</td><td>{$this->getFormatedNumber($new_yahooinlink)}</td><td>{$chg_yahooinlink} </td></tr>";
            $this->addRecordToCSV('Yahoo: InLinks', $pre_yahooinlink, $new_yahooinlink, $chg_yahooinlink);

        }

        if(in_array('bing', $allservices)) {
            if(isset ($previous['bing']) && is_array($previous['bing']) ) {
                $pre_bing = trim(@$previous['bing']['bingreslt']);
            } else {
                $pre_bing = 'N/A';
            }
            if(isset ($current['bing']) && is_array($current['bing']) ) {
                $new_bing = trim(@$current['bing']['bingreslt']);
            } else {
                $new_bing = 'N/A';
            }
            $chg_bing = $this->getChangePercentage($pre_bing, $new_bing);
            $this->_searchEngineStats .= "<tr><td>Bing: Number Of Results</td><td>{$this->getFormatedNumber($pre_bing)}</td><td>{$this->getFormatedNumber($new_bing)}</td><td>{$chg_bing} </td></tr>";
            $this->addRecordToCSV('Bing: Number Of Results', $pre_bing, $new_bing, $chg_bing);
        }

        if(in_array('dmoz', $allservices)) {
            if(isset ($previous['dmoz']) && is_array($previous['dmoz']) ) {
                $pre_dmoz = trim(@$previous['dmoz']['dmozresult']);
            } else {
                $pre_dmoz = 'N/A';
            }
            if(isset ($current['dmoz']) && is_array($current['dmoz']) ) {
                $new_dmoz = trim(@$current['dmoz']['dmozresult']);
            } else {
                $new_dmoz = 'N/A';
            }
            $chg_dmoz = $this->getChangePercentage($pre_dmoz, $new_dmoz);
            $this->_searchEngineStats .= "<tr><td>Dmoz: Indexed Items</td><td>{$this->getFormatedNumber($pre_dmoz)}</td><td>{$this->getFormatedNumber($new_dmoz)}</td><td>{$chg_dmoz} </td></tr>";
            $this->addRecordToCSV('Dmoz: Indexed Items', $pre_dmoz, $new_dmoz, $chg_dmoz);
        }

        if(in_array('compete', $allservices)) {
            if(isset ($previous['compete']) && is_array($previous['compete']) ) {
                $pre_compete = trim(@$previous['compete']['competerank']);
            } else {
                $pre_compete = 'N/A';
            }
            if(isset ($current['compete']) && is_array($current['compete']) ) {
                $new_compete = trim(@$current['compete']['competerank']);
            } else {
                $new_compete = 'N/A';
            }
            $chg_compete = $this->getChangePercentage($pre_compete, $new_compete);
            $this->_searchEngineStats .= "<tr><td>Compete: Rank</td><td>{$this->getFormatedNumber($pre_compete)}</td><td>{$this->getFormatedNumber($new_compete)}</td><td>{$chg_compete} </td></tr>";
            $this->addRecordToCSV('Compete: Rank', $pre_compete, $new_compete, $chg_compete);
        }

        if(in_array('quantcast', $allservices)) {

            if(isset ($previous['quantcast']) && is_array($previous['quantcast']) ) {
                $pre_quantcast = trim(@$previous['quantcast']['quantcast_rank']);
                if(isset ($previous['quantcast']['global_uniques'])) {
                    $pre_quantcastglobal = trim(@$previous['quantcast']['global_uniques']);
                } else {
                    $pre_quantcastglobal = 'N/A';
                }
                if(isset ($previous['quantcast']['us_uniques'])) {
                    $pre_quantcastus = trim(@$previous['quantcast']['us_uniques']);
                } else {
                    $pre_quantcastus = 'N/A';
                }

            } else {
                $pre_quantcast = 'N/A';
                $pre_quantcastglobal = 'N/A';
                $pre_quantcastus = 'N/A';
            }
            if(isset ($current['quantcast']) && is_array($current['quantcast']) ) {
                $new_quantcast = trim(@$current['quantcast']['quantcast_rank']);
                if(isset ($current['quantcast']['global_uniques'])) {
                    $new_quantcastglobal = trim(@$current['quantcast']['global_uniques']);
                } else {
                    $new_quantcastglobal = 'N/A';
                }
                if(isset ($current['quantcast']['us_uniques'])) {
                    $new_quantcastus = trim(@$current['quantcast']['us_uniques']);
                } else {
                    $new_quantcastus = 'N/A';
                }
            } else {
                $new_quantcast = 'N/A';
                $new_quantcastglobal = 'N/A';
                $new_quantcastus = 'N/A';
            }

            $chg_quantcast = $this->getChangePercentage($new_quantcast, $pre_quantcast);
            $chg_quantcastglobal = $this->getChangePercentage($new_quantcastglobal, $pre_quantcastglobal);
            $chg_quantcastus = $this->getChangePercentage($new_quantcastus, $pre_quantcastus);

            $this->_searchEngineStats .= "<tr><td>Quantcast: Rank</td><td>{$pre_quantcast}</td><td>{$new_quantcast}</td><td>{$chg_quantcast} </td></tr>";
            $this->_searchEngineStats .= "<tr><td>Quantcast: Global Uniques</td><td>{$pre_quantcastglobal}</td><td>{$new_quantcastglobal}</td><td>{$chg_quantcastglobal} </td></tr>";
            $this->_searchEngineStats .= "<tr><td>Quantcast: US Uniques</td><td>{$pre_quantcastus}</td><td>{$new_quantcastus}</td><td>{$chg_quantcastus} </td></tr>";
            $this->addRecordToCSV('Quantcast: Rank', $pre_quantcast, $new_quantcast, $chg_quantcast);
            $this->addRecordToCSV('Quantcast: Global Uniques', $pre_quantcastglobal, $new_quantcastglobal, $chg_quantcastglobal);
            $this->addRecordToCSV('Quantcast: US Uniques', $pre_quantcastus, $new_quantcastus, $chg_quantcastus);
        }

        if(in_array('alexa', $allservices)) {
            if(isset ($previous['alexa']) && is_array($previous['alexa']) ) {
                if(isset ($previous['alexa']['rank']) && !empty ($previous['alexa']['rank'])) {
                    $pre_alexarank = trim(@$previous['alexa']['rank']);
                } else {
                    $pre_alexarank = 'N/A';
                }
                if(isset ($previous['alexa']['backlinks']) ){
                    $pre_alexalinks = trim(@$previous['alexa']['backlinks']);
                } else {
                    $pre_alexalinks = 'N/A';
                }
            } else {
                $pre_alexarank = 'N/A';
                $pre_alexalinks = 'N/A';
            }
            if(isset ($current['alexa']) && is_array($current['alexa']) ) {
                if(isset ($current['alexa']['rank'])) {
                    $new_alexarank = trim(@$current['alexa']['rank']);
                } else {
                    $new_alexarank = 'N/A';
                }
                if(isset ($current['alexa']['backlinks'])) {
                    $new_alexalinks = trim(@$current['alexa']['backlinks']);
                } else {
                    $new_alexalinks = 'N/A';
                }
            } else {
                $new_alexarank = 'N/A';
                $new_alexalinks = 'N/A';
            }
            // For Alexa Rank a smaller number means top. So its inversly proportional.
            // So make the current as previous and previous as current to get the change.
            $chg_alexarank = $this->getChangePercentage($new_alexarank, $pre_alexarank);
            $chg_alexalinks = $this->getChangePercentage($pre_alexalinks, $new_alexalinks);
            $this->_searchEngineStats .= "<tr><td>Alexa: Traffic Rank</td><td>{$this->getFormatedNumber($pre_alexarank)}</td><td>{$this->getFormatedNumber($new_alexarank)}</td><td>{$chg_alexarank} </td></tr>";
            $this->_searchEngineStats .= "<tr><td>Alexa: Number Of Sites Linking In</td><td>{$this->getFormatedNumber($pre_alexalinks)}</td><td>{$this->getFormatedNumber($new_alexalinks)}</td><td>{$chg_alexalinks} </td></tr>";
            $this->addRecordToCSV('Alexa: Traffic Rank', $pre_alexarank, $new_alexarank, $chg_alexarank);
            $this->addRecordToCSV('Alexa: Number Of Sites Linking In', $pre_alexalinks, $new_alexalinks, $chg_alexalinks);
        }

        if(in_array('twitter', $allservices)) {
            if(isset ($previous['twitter']) && is_array($previous['twitter']) ) {
                $pre_followerscount = trim(@$previous['twitter']['followerscount']);
                $pre_friendscount = trim(@$previous['twitter']['friendscount']);
                $pre_favouritscount = trim(@$previous['twitter']['favouritscount']);
            } else {
                $pre_followerscount = 'N/A';
                $pre_friendscount = 'N/A';
                $pre_favouritscount = 'N/A';
            }
            if(isset ($current['twitter']) && is_array($current['twitter']) ) {
                $new_followerscount = trim(@$current['twitter']['followerscount']);
                $new_friendscount = trim(@$current['twitter']['friendscount']);
                $new_favouritscount = trim(@$current['twitter']['favouritscount']);
            } else {
                $new_followerscount = 'N/A';
                $new_friendscount = 'N/A';
                $new_favouritscount = 'N/A';
            }
            $chg_followerscount = $this->getChangePercentage($pre_followerscount, $new_followerscount);
            $chg_friendscount = $this->getChangePercentage($pre_friendscount, $new_friendscount);
            $chg_favouritscount = $this->getChangePercentage($pre_favouritscount, $new_favouritscount);
            $this->_socialMedia .= "<tr><td>Twitter: Followers</td><td>{$this->getFormatedNumber($pre_followerscount)}</td><td>{$this->getFormatedNumber($new_followerscount)}</td><td>{$chg_followerscount} </td></tr>";
            $this->_socialMedia .= "<tr><td>Twitter: Following</td><td>{$this->getFormatedNumber($pre_friendscount)}</td><td>{$this->getFormatedNumber($new_friendscount)}</td><td>{$chg_friendscount} </td></tr>";
            //$this->_socialMedia .= "<tr><td>Twitter: Favourites</td><td>{$this->getFormatedNumber($pre_favouritscount)}</td><td>{$this->getFormatedNumber($new_favouritscount)}</td><td>{$chg_favouritscount} </td></tr>";
            $this->addRecordToCSV('Twitter: Followers', $pre_followerscount, $new_followerscount, $chg_followerscount);
            $this->addRecordToCSV('Twitter: Following', $pre_friendscount, $new_friendscount, $chg_friendscount);
        }

        if(in_array('facebook', $allservices)) {
            if(isset ($previous['facebook']) && is_array($previous['facebook']) ) {
                $pre_facebookfans = trim(@$previous['facebook']['facebook_fan']);
            } else {
                $pre_facebookfans = 'N/A';
            }
            if(isset ($current['facebook']) && is_array($current['facebook']) ) {
                $new_facebookfans = trim(@$current['facebook']['facebook_fan']);
            } else {
                $new_facebookfans = 'N/A';
            }
            $chg_facebookfans = $this->getChangePercentage($pre_facebookfans, $new_facebookfans);
            $this->_socialMedia .= "<tr><td>Facebook: Fans</td><td>{$this->getFormatedNumber($pre_facebookfans)}</td><td>{$this->getFormatedNumber($new_facebookfans)}</td><td>{$chg_facebookfans} </td></tr>";
            $this->addRecordToCSV('Facebook: Fans', $pre_facebookfans, $new_facebookfans, $chg_facebookfans);
        }

        if(in_array('digg', $allservices)) {
            if(isset ($previous['digg']) && is_array($previous['digg']) ) {
                $pre_digg = trim(@$previous['digg']['diggcount']);
            } else {
                $pre_digg = 'N/A';
            }
            if(isset ($current['digg']) && is_array($current['digg']) ) {
                $new_digg = trim(@$current['digg']['diggcount']);
            } else {
                $new_digg = 'N/A';
            }
            $chg_digg = $this->getChangePercentage($pre_digg, $new_digg);
            $this->_socialMedia .= "<tr><td>Digg: Number Of Submissions</td><td>{$this->getFormatedNumber($pre_digg)}</td><td>{$this->getFormatedNumber($new_digg)}</td><td>{$chg_digg} </td></tr>";
            $this->addRecordToCSV('Digg: Number Of Submissions', $pre_digg, $new_digg, $chg_digg);
        }

        if(in_array('stumble', $allservices)) {
            if(isset ($previous['stumble']) && is_array($previous['stumble']) ) {
                $pre_stumble = trim(@$previous['stumble']['stumble_result']);
            } else {
                $pre_stumble = 'N/A';
            }
            if(isset ($current['stumble']) && is_array($current['stumble']) ) {
                $new_stumble = trim(@$current['stumble']['stumble_result']);
            } else {
                $new_stumble = 'N/A';
            }
            $chg_stumble = $this->getChangePercentage($pre_stumble, $new_stumble);
            $this->_socialMedia .= "<tr><td>StumbleUpon: Articles</td><td>{$pre_stumble}</td><td>{$new_stumble}</td><td>{$chg_stumble} </td></tr>";
            $this->addRecordToCSV('StumbleUpon: Articles', $pre_stumble, $new_stumble, $chg_stumble);
        }

        if(in_array('klout', $allservices)) {
            if(isset ($previous['klout']) && is_array($previous['klout']) ) {
                $pre_kloutscore = trim(@$previous['klout']['kscore']);
                $pre_kloutslope = trim(@$previous['klout']['slope']);
                $pre_kloutnetwork = trim(@$previous['klout']['network_score']);
                $pre_kloutamp = trim(@$previous['klout']['amplification_score']);
                $pre_kloutreach = trim(@$previous['klout']['true_reach']);
            } else {
                $pre_kloutscore = 'N/A';
                $pre_kloutslope = 'N/A';
                $pre_kloutnetwork = 'N/A';
                $pre_kloutamp = 'N/A';
                $pre_kloutreach = 'N/A';
            }
            if(isset ($current['klout']) && is_array($current['klout']) ) {
                $new_kloutscore = trim(@$current['klout']['kscore']);
                $new_kloutslope = trim(@$current['klout']['slope']);
                $new_kloutnetwork = trim(@$current['klout']['network_score']);
                $new_kloutamp = trim(@$current['klout']['amplification_score']);
                $new_kloutreach = trim(@$current['klout']['true_reach']);
            } else {
                $new_kloutscore = 'N/A';
                $new_kloutslope = 'N/A';
                $new_kloutnetwork = 'N/A';
                $new_kloutamp = 'N/A';
                $new_kloutreach = 'N/A';
            }
            $chg_kloutscore = $this->getChangePercentage($pre_kloutscore, $new_kloutscore);
            $chg_kloutslope = $this->getChangePercentage($pre_kloutslope, $new_kloutslope);
            $chg_kloutnetwork = $this->getChangePercentage($pre_kloutnetwork, $new_kloutnetwork);
            $chg_kloutamp = $this->getChangePercentage($pre_kloutamp, $new_kloutamp);
            $chg_kloutreach = $this->getChangePercentage($pre_kloutreach, $new_kloutreach);
            $this->_socialMedia .= "<tr><td>Klout: Score</td><td>{$pre_kloutscore}</td><td>{$new_kloutscore}</td><td>{$chg_kloutscore} </td></tr>";
            $this->_socialMedia .= "<tr><td>Klout: Slope</td><td>{$pre_kloutslope}</td><td>{$new_kloutslope}</td><td>{$chg_kloutslope} </td></tr>";
            $this->_socialMedia .= "<tr><td>Klout: Network Score</td><td>{$pre_kloutnetwork}</td><td>{$new_kloutnetwork}</td><td>{$chg_kloutnetwork} </td></tr>";
            $this->_socialMedia .= "<tr><td>Klout: Amplification Probability</td><td>{$pre_kloutamp}</td><td>{$new_kloutamp}</td><td>{$chg_kloutamp} </td></tr>";
            $this->_socialMedia .= "<tr><td>Klout: True Reach</td><td>{$pre_kloutreach}</td><td>{$new_kloutreach}</td><td>{$chg_kloutreach} </td></tr>";
            $this->addRecordToCSV('Klout: Score', $pre_kloutscore, $new_kloutscore, $chg_kloutscore);
            $this->addRecordToCSV('Klout: Slope', $pre_kloutslope, $new_kloutslope, $chg_kloutslope);
            $this->addRecordToCSV('Klout: Network Score', $pre_kloutnetwork, $new_kloutnetwork, $chg_kloutnetwork);
            $this->addRecordToCSV('Klout: Amplification Probability', $pre_kloutamp, $new_kloutamp, $chg_kloutamp);
            $this->addRecordToCSV('Klout: True Reach', $pre_kloutreach, $new_kloutreach, $chg_kloutreach);
        }

        if(in_array('postrank', $allservices)) {
            if(isset ($previous['postrank']) && is_array($previous['postrank']) ) {
                $pre_postranktags = trim(@$previous['postrank']['tags']);
                $pre_postranktopics = trim(@$previous['postrank']['topics']);
            } else {
                $pre_postranktags = 'N/A';
                $pre_postranktopics = 'N/A';
            }
            if(isset ($current['postrank']) && is_array($current['postrank']) ) {
                $new_postranktags = trim(@$current['postrank']['tags']);
                $new_postranktopics = trim(@$current['postrank']['topics']);
            } else {
                $new_postranktags = 'N/A';
                $new_postranktopics = 'N/A';
            }
            $chg_postranktags = $this->getChangePercentage($pre_postranktags, $new_postranktags);
            $chg_postranktopics = $this->getChangePercentage($pre_postranktopics, $new_postranktopics);
            $this->_analytics .= "<tr><td>PostRank: Tags</td><td>{$pre_postranktags}</td><td>{$new_postranktags}</td><td>{$chg_postranktags} </td></tr>";
            $this->_analytics .= "<tr><td>PostRank: Topics</td><td>{$pre_postranktopics}</td><td>{$new_postranktopics}</td><td>{$chg_postranktopics} </td></tr>";
            $this->addRecordToCSV('PostRank: Tags', $pre_postranktags, $new_postranktags, $chg_postranktags);
            $this->addRecordToCSV('PostRank: Topics', $pre_postranktopics, $new_postranktopics, $chg_postranktopics);

        }

        if(in_array('clicky', $allservices)) {
            if(isset ($previous['clicky']) && is_array($previous['clicky']) ) {
                $pre_clickybookmark = trim(@$previous['clicky']['direct_bookmark']);
                $pre_clickysearches = trim(@$previous['clicky']['searches']);
                $pre_clickysocialmedia = trim(@$previous['clicky']['socialmedia']);
                $pre_clickylinks = trim(@$previous['clicky']['links']);
            } else {
                $pre_clickybookmark = 'N/A';
                $pre_clickysearches = 'N/A';
                $pre_clickysocialmedia = 'N/A';
                $pre_clickylinks = 'N/A';
            }
            if(isset ($current['clicky']) && is_array($current['clicky']) ) {
                $new_clickybookmark = trim(@$current['clicky']['direct_bookmark']);
                $new_clickysearches = trim(@$current['clicky']['searches']);
                $new_clickysocialmedia = trim(@$current['clicky']['socialmedia']);
                $new_clickylinks = trim(@$current['clicky']['links']);
            } else {
                $new_clickybookmark = 'N/A';
                $new_clickysearches = 'N/A';
                $new_clickysocialmedia = 'N/A';
                $new_clickylinks = 'N/A';
            }
            $chg_clickybookmark = $this->getChangePercentage($pre_clickybookmark, $new_clickybookmark);
            $chg_clickysearches = $this->getChangePercentage($pre_clickysearches, $new_clickysearches);
            $chg_clickysocialmedia = $this->getChangePercentage($pre_clickysocialmedia, $new_clickysocialmedia);
            $chg_clickylinks = $this->getChangePercentage($pre_clickylinks, $new_clickylinks);
            $this->_analytics .= "<tr><td>Clicky: Directly Bookmarked</td><td>{$pre_clickybookmark}</td><td>{$new_clickybookmark}</td><td>{$chg_clickybookmark} </td></tr>";
            $this->_analytics .= "<tr><td>Clicky: Searches</td><td>{$pre_clickysearches}</td><td>{$new_clickysearches}</td><td>{$chg_clickysearches} </td></tr>";
            $this->_analytics .= "<tr><td>Clicky: Social Media</td><td>{$pre_clickysocialmedia}</td><td>{$new_clickysocialmedia}</td><td>{$chg_clickysocialmedia} </td></tr>";
            $this->_analytics .= "<tr><td>Clicky: Links</td><td>{$pre_clickylinks}</td><td>{$new_clickylinks}</td><td>{$chg_clickylinks} </td></tr>";
            $this->addRecordToCSV('Clicky: Directly Bookmarked', $pre_clickybookmark, $new_clickybookmark, $chg_clickybookmark);
            $this->addRecordToCSV('Clicky: Searches', $pre_clickysearches, $new_clickysearches, $chg_clickysearches);
            $this->addRecordToCSV('Clicky: Social Media', $pre_clickysocialmedia, $new_clickysocialmedia, $chg_clickysocialmedia);
            $this->addRecordToCSV('Clicky: Links', $pre_clickylinks, $new_clickylinks, $chg_clickylinks);
        }

        if(in_array('wordpress', $allservices)) {
            if(isset ($previous['wordpress']) && is_array($previous['wordpress']) ) {
                if(isset ($previous['wordpress']['clicks']) && !empty ($previous['wordpress']['clicks'])) {
                    $pre_wpclicks = trim(@$previous['wordpress']['clicks']);
                } else {
                    $pre_wpclicks = 'N/A';
                }
                if(isset ($previous['wordpress']['totalviews']) && !empty ($previous['wordpress']['totalviews'])) {
                    $pre_wptotalviews = trim(@$previous['wordpress']['totalviews']);
                } else {
                    $pre_wptotalviews = 'N/A';
                }
                if(isset ($previous['wordpress']['tot_referrers']) && !empty ($previous['wordpress']['tot_referrers'])) {
                    $pre_wptot_referrers = trim(@$previous['wordpress']['tot_referrers']);
                } else {
                    $pre_wptot_referrers = 'N/A';
                }
                if(isset ($previous['wordpress']['tot_searchterms_view']) && !empty ($previous['wordpress']['tot_searchterms_view'])) {
                    $pre_wptot_searchterms_view = trim(@$previous['wordpress']['tot_searchterms_view']);
                } else {
                    $pre_wptot_searchterms_view = 'N/A';
                }
            } else {
                $pre_wpclicks = 'N/A';
                $pre_wptotalviews = 'N/A';
                $pre_wptot_referrers = 'N/A';
                $pre_wptot_searchterms_view = 'N/A';
            }
            if(isset ($current['wordpress']) && is_array($current['wordpress']) ) {
                if(isset ($current['wordpress']['clicks']) && !empty ($current['wordpress']['clicks'])) {
                    $new_wpclicks = trim(@$current['wordpress']['clicks']);
                } else {
                    $new_wpclicks = 'N/A';
                }
                if(isset ($current['wordpress']['totalviews']) && !empty ($current['wordpress']['totalviews'])) {
                    $new_wptotalviews = trim(@$current['wordpress']['totalviews']);
                } else {
                    $new_wptotalviews = 'N/A';
                }
                if(isset ($current['wordpress']['tot_referrers']) && !empty ($current['wordpress']['tot_referrers'])) {
                    $new_wptot_referrers = trim(@$current['wordpress']['tot_referrers']);
                } else {
                    $new_wptot_referrers = 'N/A';
                }
                if(isset ($current['wordpress']['tot_searchterms_view']) && !empty ($current['wordpress']['tot_searchterms_view'])) {
                    $new_wptot_searchterms_view = trim(@$current['wordpress']['tot_searchterms_view']);
                } else {
                    $new_wptot_searchterms_view = 'N/A';
                }
            } else {
                $new_wpclicks = 'N/A';
                $new_wptotalviews = 'N/A';
                $new_wptot_referrers = 'N/A';
                $new_wptot_searchterms_view = 'N/A';
            }
            $chg_wpclicks = $this->getChangePercentage($pre_wpclicks, $new_wpclicks);
            $chg_wptotalviews = $this->getChangePercentage($pre_wptotalviews, $new_wptotalviews);
            $chg_wptot_referrers = $this->getChangePercentage($pre_wptot_referrers, $new_wptot_referrers);
            $chg_wptot_searchterms_view = $this->getChangePercentage($pre_wptot_searchterms_view, $new_wptot_searchterms_view);

            $this->_analytics .= "<tr><td>WordPress.com: Clicks</td><td>{$pre_wpclicks}</td><td>{$new_wpclicks}</td><td>{$chg_wpclicks} </td></tr>";
            $this->_analytics .= "<tr><td>WordPress.com: Total Views</td><td>{$pre_wptotalviews}</td><td>{$new_wptotalviews}</td><td>{$chg_wptotalviews} </td></tr>";
            $this->_analytics .= "<tr><td>WordPress.com: Total Referrers</td><td>{$pre_wptot_referrers}</td><td>{$new_wptot_referrers}</td><td>{$chg_wptot_referrers} </td></tr>";
            $this->_analytics .= "<tr><td>WordPress.com: Searchterms Views</td><td>{$pre_wptot_searchterms_view}</td><td>{$new_wptot_searchterms_view}</td><td>{$chg_wptot_searchterms_view} </td></tr>";
            $this->addRecordToCSV('WordPress.com: Clicks', $pre_wpclicks, $new_wpclicks, $chg_wpclicks);
            $this->addRecordToCSV('WordPress.com: Total Views', $pre_wptotalviews, $new_wptotalviews, $chg_wptotalviews);
            $this->addRecordToCSV('WordPress.com: Total Referrers', $pre_wptot_referrers, $new_wptot_referrers, $chg_wptot_referrers);
            $this->addRecordToCSV('WordPress.com: Searchterms Views', $pre_wptot_searchterms_view, $new_wptot_searchterms_view, $chg_wptot_searchterms_view);
        }

        if(in_array('feedburner', $allservices)) {
            if(isset ($previous['feedburner']) && is_array($previous['feedburner']) ) {
                $pre_feedsubscr = trim(@$previous['feedburner']['subscr_count']);
                $pre_feedhits = trim(@$previous['feedburner']['hits']);
                $pre_feedreach = trim(@$previous['feedburner']['reach']);
            } else {
                $pre_feedsubscr = 'N/A';
                $pre_feedhits = 'N/A';
                $pre_feedreach = 'N/A';
            }
            if(isset ($current['feedburner']) && is_array($current['feedburner']) ) {
                $new_feedsubscr = trim(@$current['feedburner']['subscr_count']);
                $new_feedhits = trim(@$current['feedburner']['hits']);
                $new_feedreach = trim(@$current['feedburner']['reach']);
            } else {
                $new_feedsubscr = 'N/A';
                $new_feedhits = 'N/A';
                $new_feedreach = 'N/A';
            }
            $chg_feedsubscr = $this->getChangePercentage($pre_feedsubscr, $new_feedsubscr);
            $chg_feedhits = $this->getChangePercentage($pre_feedhits, $new_feedhits);
            $chg_feedreach = $this->getChangePercentage($pre_feedreach, $new_feedreach);
            $this->_others .= "<tr><td>Feedburner: Subscribers</td><td>{$pre_feedsubscr}</td><td>{$new_feedsubscr}</td><td>{$chg_feedsubscr} </td></tr>";
            $this->_others .= "<tr><td>Feedburner: Hits</td><td>{$pre_feedhits}</td><td>{$new_feedhits}</td><td>{$chg_feedhits} </td></tr>";
            $this->_others .= "<tr><td>Feedburner: Sites Linking In</td><td>{$pre_feedreach}</td><td>{$new_feedreach}</td><td>{$chg_feedreach} </td></tr>";
            $this->addRecordToCSV('Feedburner: Subscribers', $pre_feedsubscr, $new_feedsubscr, $chg_feedsubscr);
            $this->addRecordToCSV('Feedburner: Hits', $pre_feedhits, $new_feedhits, $chg_feedhits);
            $this->addRecordToCSV('Feedburner: Sites Linking In', $pre_feedreach, $new_feedreach, $chg_feedreach);
        }

        if(in_array('prweb', $allservices)) {
            if(isset ($previous['prweb']) && is_array($previous['prweb']) ) {
                $pre_prweb = trim(@$previous['prweb']['prweb_result']);
            } else {
                $pre_prweb = 'N/A';
            }
            if(isset ($current['prweb']) && is_array($current['prweb']) ) {
                $new_prweb = trim(@$current['prweb']['prweb_result']);
            } else {
                $new_prweb = 'N/A';
            }
            $chg_prweb = $this->getChangePercentage($pre_prweb, $new_prweb);
            $this->_others .= "<tr><td>PRWeb: Results</td><td>{$pre_prweb}</td><td>{$new_prweb}</td><td>{$chg_prweb} </td></tr>";
            $this->addRecordToCSV('PRWeb: Results', $pre_prweb, $new_prweb, $chg_prweb);
        }

		$aweber = new BPRPAweber();
		// Get lists from aWeber account
		$lists = $aweber->get_lists();
		// Get the id of lists the user wants to add to the report
		$user_lists = maybe_unserialize( get_option('bprp-awb-lists') );
		
		// If lists were fetched from aWeber API and the user has lists setup for the reports
		if( $lists && is_array($user_lists) ){
			// Cycle through each list
			foreach ( $lists as $list ){
				// If the list is one the user setup for reporting
				if( in_array($list->id, $user_lists) ){
					// Fetch subscriber data for this list from aWeber API
					$subscribers = $list->subscribers->data['entries'];
					// Filter subscribers, retain only active subscribers
					$active_subscribers = array_filter( $subscribers, array( $this, 'filterSubscribersByStatus' ) );
					// Count the total active subscribers
					$current_list_size = count( $active_subscribers );
					// Count the total active subscribers who subscribed before the report starting date
					$starting_list_size = count( array_filter( $active_subscribers, array( $this, 'filterSubscribersByDate' ) ) );
					// Calculate the percentage change in the list size for the reporting period
					$change_in_list_size = $this->getChangePercentage( $starting_list_size, $current_list_size );
					// Add table row for report
					$this->_others .= "<tr><td>Aweber List: $list->name</td><td>{$this->getFormatedNumber($starting_list_size)}</td><td>{$this->getFormatedNumber($current_list_size)}</td><td>{$change_in_list_size} </td></tr>";
					// Add data to the CSV
					$this->addRecordToCSV( "Aweber List: $list->name", $starting_list_size, $current_list_size, $change_in_list_size );
				}
			}
		}
    }

    public function filterSubscribersByStatus( $subscriber ) {
		if ( isset( $subscriber['status'] ) )
			return ( 'subscribed' == $subscriber['status'] );
	}

    public function filterSubscribersByDate( $subscriber ) {
		if ( isset( $subscriber['subscribed_at'] ) )
			return ( strtotime( $subscriber['subscribed_at'] ) < strtotime( $_POST['start-date'] ) );
	}

    public function getFormatedNumber($num) {
        if($num == 'N/A') {
            return $num;
        } elseif (is_numeric($num)) {
            return @number_format($num);
        } else {
            return $num;
        }
    }

    public function addRecordToCSV($type, $old, $new, $change) {
        $type = str_replace(',', '', $type);
        $old = str_replace(',', '', $old);
        $new = str_replace(',', '', $new);
        $change = str_replace(',', '', $change);

        $this->_csvreport .= "{$type}, {$old}, {$new}, {$change} \n";
    }

    public function getChangePercentage($old, $new) {

        /**
         * Make the global filtering of input data. Some inputs may have leading and trailing
         * spaces. Similarly some inputs found to be already thousand seperated. So I need to remove
         * all these unwanted characters from the input data
         */

        $old = trim($old);
        $old = str_replace(',', '', $old);
        $new = trim($new);
        $new = str_replace(',', '', $new);

        if( ($old == 'N/A') || ($new == 'N/A') ) {
            $change = 'N/A';
            return $change;
        } elseif ( (empty($old) ) || ($old == '0') || ($old == '') || ($old == 0) ) {
            if((empty($new) ) || ($new == '0') || ($new == '') || ($new == 0)) {
                $old = 1;
                $new = 1;
                $change = ( ($new - $old) * 100 )/$old;
                if($change > 0) {
                    $this->_positive_stats =  $this->_positive_stats + 1;
                } elseif($change < 0) {
                    $this->_negative_stats = $this->_negative_stats + 1;
                }
                // $this->setChangePercentage($change);
                $change = number_format($change, 2);
                $this->_servicecount = $this->_servicecount + 1;
                $change = $change . '%';
            } else {
                $old = 1;
                $new = $new +1;
                $change = ( ($new - $old) * 100 )/$old;
                if($change > 0) {
                    $this->_positive_stats =  $this->_positive_stats + 1;
                } elseif($change < 0) {
                    $this->_negative_stats = $this->_negative_stats + 1;
                }
                // $this->setChangePercentage($change);
                $change = number_format($change, 2);
                $this->_servicecount = $this->_servicecount + 1;
                $change = $change . '%';
            }

            return $change;

	} else {
            $change = @($new - $old)*100/$old;
            if($change > 0) {
                $this->_positive_stats =  $this->_positive_stats + 1;
            } elseif($change < 0) {
                $this->_negative_stats = $this->_negative_stats + 1;
            }
            // $this->setChangePercentage($change);
            $change = number_format($change, 2);
            $this->_servicecount = $this->_servicecount + 1;
            $change = $change . '%';
            return $change;
        }
    }

    public function setChangePercentage($change) {
        $this->_overallperformance = $this->_overallperformance + $change;
    }

    public function getOverallPerformaceAvg() {
        // $avg_performace = ($this->_overallperformance)/($this->_servicecount);
        $avg_performace = ( ($this->_positive_stats - $this->_negative_stats ) / $this->_servicecount ) * 100;
        return $avg_performace;
    }

    public function makehumanreadable($data) {
        $data = trim($data);
        $readableArr = @unserialize($data);
        return $readableArr;
    }

    public function makehumanunreadable($data) {
        $unreadableStr  = @serialize($data);
        return $unreadableStr;
    }

    public function showSearchEngineStats() {
        if($this->_searchEngineStats != "") {

            $content = '<div class="wrap"><h2>Search Engine Stats</h2>';
            $content.=<<<EOF
                 <table class="search_engine" cellpadding="0" cellspacing="1" border="0">
			<tr>
				<th class="media_head1"><h3>Data Type</h3></th>
                                <th class="media_head2"><h3>Start</h3></th>
                                <th class="media_head2"><h3>End</h3></th>
                                <th class="media_head2"><h3>Change</h3></th>
			</tr>
                        {$this->_searchEngineStats}
		</table>

EOF;

            $content .= '<br><br></div>';
            echo $content;
        }

    }

    public function showSocialMediaStats() {
        if($this->_socialMedia != "") {
            $content = '<div class="wrap"><h2>Social Media Stats</h2>';

            $content.=<<<EOF
                 <table class="search_engine" cellpadding="0" cellspacing="1" border="0">
			<tr>
				<th class="media_head1"><h3>Social Media</h3></th>
				<th class="media_head2"><h3>Start</h3></th>
                                <th class="media_head2"><h3>End</h3></th>
                                <th class="media_head2"><h3>Change</h3></th>
			</tr>
                        {$this->_socialMedia}
		</table>

EOF;

            $content .= '<br><br></div>';
            echo $content;
        }
    }

    public function showAnalyticsSection() {
        if($this->_analytics != "") {
            $content = '<div class="wrap"><h2>Analytics Stats</h2>';

            $content.=<<<EOF
                 <table class="search_engine" cellpadding="0" cellspacing="1" border="0">
			<tr>
				<th class="media_head1"><h3>Data Type</h3></th>
				<th class="media_head2"><h3>Start</h3></th>
                                <th class="media_head2"><h3>End</h3></th>
                                <th class="media_head2"><h3>Change</h3></th>
			</tr>
                        {$this->_analytics}
		</table>

EOF;

            $content .= '<br><br></div>';
            echo $content;
        }
    }

    public function showBlogStatisticsSection() {
        if($this->_blogstatics != "") {
            $content = '<div class="wrap"><h2>Blog Statistics</h2>';

            $content.=<<<EOF
                 <table class="search_engine" cellpadding="0" cellspacing="1" border="0">
			<tr>
				<th class="media_head1"><h3>Data Type</h3></th>
				<th class="media_head2"><h3>Start</h3></th>
                                <th class="media_head2"><h3>End</h3></th>
                                <th class="media_head2"><h3>Change</h3></th>
			</tr>
                        {$this->_blogstatics}
		</table>

EOF;

            $content .= '<br><br></div>';
            echo $content;
        }
    }

    public function showOtherResults() {
        if($this->_others != "") {
            $content = '<div class="wrap"><h2>Other Stats</h2>';

            $content.=<<<EOF
                 <table class="search_engine" cellpadding="0" cellspacing="1" border="0">
			<tr>
				<th class="media_head1"><h3>Data Type</h3></th>
                                <th class="media_head2"><h3>Start</h3></th>
				<th class="media_head2"><h3>End</h3></th>
                                <th class="media_head2"><h3>Change</h3></th>
			</tr>
                        {$this->_others}
		</table>

EOF;

            $content .= '<br><br></div>';
            echo $content;
        }
    }

     public function showSitemapSection() {
        if($this->_sitemap != "") {
            $content = '<div class="wrap"><h2>Sitemap Statistics</h2>';

            $content.=<<<EOF
                 <table class="search_engine" cellpadding="0" cellspacing="1" border="0">
			<tr>
				<th class="media_head1"><h3>Data Type</h3></th>
                                <th class="media_head2"><h3>Start</h3></th>
				<th class="media_head2"><h3>End</h3></th>
                                <th class="media_head2"><h3>Change</h3></th>
			</tr>
                        {$this->_sitemap}
		</table>

EOF;

            $content .= '<br><br></div>';
            echo $content;
        }
    }
}
