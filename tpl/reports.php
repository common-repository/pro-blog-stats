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

$settings = $this->getSettings();
/**
 * @var BPRPUserAccount
 */
$userInfo = $this->getUserInfo(true);

?>


<style type="text/css">
table.social_media {
	margin: 0px;
	padding: 0px;
        width: 50%;
}

table.search_engine {
	margin: 0px;
	padding: 0px;
        width: 90%;
}

.serach_head1 {
    text-align: center;
    background-color: #ccc;
    width: 30%;
    padding: 2px;
    border: 1px solid #ccc;
}

.search_head2 {
    text-align: center;
    background-color: #ccc;
    width: 60%;
    padding: 2px;
    border: 1px solid #ccc;
}

.media_head1 {
    text-align: center;
    background-color: #ccc;
    width: 30%;
    padding: 2px;
    border: 1px solid #ccc;
}
.media_head2 {
    text-align: center;
    background-color: #ccc;
    width: 20%;
    padding: 2px;
    border: 1px solid #ccc;
}

table.social_media td {
    padding: 4px;
    border: 1px solid #ddd;
}

table.search_engine td {
    padding: 4px;
    border: 1px solid #ddd;
}

table.blogstatics td {
    padding: 4px;
    border-bottom: 1px solid #ddd;
    border-top: 1px solid #fff;
    border-right: 1px solid #ddd;
    border-left: 1px solid #fff;
    background:#f1f1f1;
}

table.blogstatics {
    margin: 0px;
    padding: 0px;
    width: 50%;
    border-left: 1px solid #ddd;
}
.graphwrapper{
    border:1px solid #CCC;
    padding:5px;
    width:98%;
    overflow:hidden;
}
</style>



<?php

if (isset($_POST['gen_report']) && current_user_can('manage_options') && check_admin_referer('gen_report')) {
    $sel_start_date = $_POST['start-date'];
    $sel_end_date = $_POST['end-date'];
    $selDateRange = $this->getFormattedMysqlDateRanges($sel_start_date, $sel_end_date);
    $reqStartDate = $selDateRange['start_date'];
    $reqEndDate = $selDateRange['end_date'];

    $blogstatisticsEnd1 = $this->getNextDate( date($reqStartDate), 1, 0, 0);
    $blogstatisticsEnd1  = date('Y-m-d',strtotime($blogstatisticsEnd1));

    $blogstatisticsEnd2 = $this->getNextDate( date($reqEndDate), 1, 0, 0);
    $blogstatisticsEnd2 = date('Y-m-d',strtotime($blogstatisticsEnd2));

    /**
     * Set the initial date to a long past date of 2000-01-01
     */
    $longPastDate = '2000-01-01';
    $blogStatics1 = $this->getBlogStatics($longPastDate, $blogstatisticsEnd1);
    $blogStatics2 = $this->getBlogStatics($longPastDate, $blogstatisticsEnd2);


?>
<div class="wrap">
    <?php screen_icon(); ?><h2><?php _e( "Pro Blog Stats :- Report (From: {$reqStartDate}  To: {$reqEndDate})" ); ?></h2>


<?php
    /* -------------------- Old report section starts -------------------- */

    $userServicesInfo  = $this->getUserServices($reqStartDate, $reqEndDate, true );

    # Object to process the services information and to display it in the proper format in the
    # plug-in end
    $bprpProcessObj = new BPRPProcess();


    if(isset ($userServicesInfo['status']) && $userServicesInfo['status']=='SUCCESS') {
        // Get all services of plugin user plan
        $problogstatsUserServices = explode(',', $userInfo['services']);
        $servicelist = array();
        foreach ($problogstatsUserServices as $service) {
            $serviceName = trim($service);
            $servicelist[] = $serviceName;
        }

        $preService = $userServicesInfo['record1'];
        $currService = $userServicesInfo['record2'];

        $bprpProcessObj->processBlogStatistics($blogStatics1, $blogStatics2);

        // Google Analtics object
        $bprpGAObj = new BPRPGoogleAnalytics();
        if ( empty( $bprpGAObj->profile ) || empty( $bprpGAObj->authToken ) ) {
            if ( empty( $bprpGAObj->profile ) && ! empty( $bprpGAObj->authToken ) ) {
        ?>
                <div class="wrap">
                    <table class="form-table" id="bprp-user-account-info">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e( 'Error' ); ?></th>
                                <td>
                                    <?php
                                    printf( __( 'Your Google Analytics Configuration is not correct:<strong class="bprp-error">Choose your host</strong>' ) );
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        <?php

            } elseif ( ! empty( $bprpGAObj->profile) && empty( $bprpGAObj->authToken ) ) {
        ?>
                <table class="form-table" id="bprp-user-account-info">
                    <tbody>
                        <tr>
                            <th scope="row"><?php _e( 'Error' ); ?></th>
                            <td>
                                <?php
                                printf( __( 'Your Google Analytics Configuration is not correct:<strong class="bprp-error">Set your username/password</strong>' ) );
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
        <?php

            }

        } else {

            $gaStartDate = strtotime ($reqStartDate);
            $gaEndDate = strtotime ($reqEndDate);

            $googleAnalyticsRecord1 = $bprpGAObj->getAnalyticRecords ( date ( 'Y-m-d', $gaStartDate ), date ( 'Y-m-d', $gaStartDate ), 'ga:date', 'ga:visitors,ga:newVisits,ga:visits,ga:pageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits' );
            $googleAnalyticsRecord2 = $bprpGAObj->getAnalyticRecords ( date ( 'Y-m-d', $gaEndDate ), date ( 'Y-m-d', $gaEndDate ), 'ga:date', 'ga:visitors,ga:newVisits,ga:visits,ga:pageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits' );

            $googleAnalyticsPrev = @$googleAnalyticsRecord1['aggregates']['metric'];
            $googleAnalyticsCurr = @$googleAnalyticsRecord2['aggregates']['metric'];

            $bprpProcessObj->processGoogleAnalyticsStats($googleAnalyticsPrev, $googleAnalyticsCurr);

            $gaTimeLine = $bprpGAObj->getAnalyticRecords ( date ( 'Y-m-d', $gaStartDate), date ( 'Y-m-d',  $gaEndDate), 'ga:date', 'ga:visitors,ga:newVisits,ga:visits,ga:pageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits' );
            $gaSource = $bprpGAObj->getAnalyticRecords( date ( 'Y-m-d', $gaStartDate), date ( 'Y-m-d', $gaEndDate) , 'ga:source' , 'ga:visits,ga:pageviews', '-ga:visits', '10');
            $gaKeywords = $bprpGAObj->getAnalyticRecords( date ( 'Y-m-d', $gaStartDate), date ( 'Y-m-d', $gaEndDate), 'ga:keyword' , 'ga:visits,ga:pageviews', '-ga:visits', '10');
            $gaTopcontent = $bprpGAObj->getAnalyticRecords ( date ( 'Y-m-d', $gaStartDate), date ( 'Y-m-d', $gaEndDate), 'ga:pagePath', 'ga:pageviews,ga:uniquePageviews', '-ga:uniquePageviews', '10' );
            $gaTopReferrers = $bprpGAObj->getAnalyticRecords ( date ( 'Y-m-d', $gaStartDate), date ( 'Y-m-d', $gaEndDate), 'ga:source,ga:medium', 'ga:visits,ga:pageviews', '-ga:visits', '10', 'ga:medium%3D%3Dreferral');
            $bprpProcessObj->drawGoogleAnalyticsCharts($gaTimeLine, $gaSource, $gaKeywords, $gaTopcontent, $gaTopReferrers);
            //include_once ( dirname( __FILE__ ) . '/ga-reports.php');
        }

        $bprpProcessObj->processSitemap($reqStartDate, $reqEndDate);
        $bprpProcessObj->processServices($preService, $currService, $servicelist);

        $bprpProcessObj->drawOverallPerformaceChart();
        $bprpProcessObj->showSearchEngineStats();
        $bprpProcessObj->showSocialMediaStats();
        $bprpProcessObj->showAnalyticsSection();
        $bprpProcessObj->showOtherResults();

        $csvfilename = "report_".$reqStartDate."_to_".$reqEndDate.".csv";

        $csvArr = array('csvfilename' => $csvfilename, 'report' => $bprpProcessObj->_csvreport);
        $this->setCSVReport($csvArr);

        $reportRedirectUrl = esc_url( admin_url( 'admin.php?page=pro-blog-stats&action=download' ) );

        echo '<a class="button-primary" href="'.$reportRedirectUrl.'">Export Data to CSV</a> <br><br>';

    } elseif ($userServicesInfo['status']=='NOTALLOWED') {
        ?>

        <table class="form-table" id="bprp-user-account-info">
            <tbody>
                <tr>
                    <th scope="row"><?php _e( 'Error' ); ?></th>
                    <td>
                        <?php
                        printf( __( '<strong class="bprp-error">FAILED TO RETURN YOUR BLOG PERFORMANCE REPORT.<br> YOU DONT HAVE ANY EVALUATIONS LEFT THIS MONTH.</strong>' ) );
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    } else {
        ?>

        <table class="form-table" id="bprp-user-account-info">
            <tbody>
                <tr>
                    <th scope="row"><?php _e( 'Error' ); ?></th>
                    <td>
                        <?php
                        printf( __( '<strong class="bprp-error">FAILED TO RETURN YOUR BLOG PERFORMANCE REPORT.</strong>' ) );
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    /* -------------------- End Of Services Report  -------------------- */
} else {
?>
<div class="wrap">
    <?php screen_icon(); ?><h2><?php _e( 'Pro Blog Stats :- Generate Report' ); ?></h2>
<?php

    if( is_wp_error( $userInfo ) ) {
    ?>

        <table class="form-table" id="bprp-user-account-info">
            <tbody>
                <tr>
                    <th scope="row"><?php _e( 'Error' ); ?></th>
                    <td>
                        <?php
                        printf( __( 'There is a problem with the plugin information: <strong class="bprp-error">%s</strong>' ), $userInfo->get_error_code() );
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

    <?php
    } elseif ($userInfo['maxcalls'] != 'Unlimited') {
        $report_start_date = $userInfo['report_start_date'];
        $report_end_date = $userInfo['report_end_date'];
        $datePickerDates = $this->getFormattedDateRanges($report_start_date, $report_end_date);
        $dpStartDate = $datePickerDates['start_date'];
        $dpEndDate = $datePickerDates['end_date'];

        # check the evaluations count
        $evaluation_left  = (int) $userInfo['evals'];
        if($evaluation_left) {
            # at this level user has atleast 1 evaluation left with him, so show the date picker

            if($report_start_date == '0' || $report_end_date == '0') {
     ?>
            <table class="form-table" id="bprp-user-account-info">
                <tbody>
                    <tr>
                        <th scope="row"><?php _e( 'Error :' ); ?></th>
                        <td>
                            <?php
                            printf( __( '<strong class="bprp-error">FAILED TO RETURN YOUR BLOG PERFORMANCE REPORT.<br>MAKE SURE YOU HAVE CONFIGURED YOUR SERVICES, <br>IF YOU HAVE ALREADY CONFIGURED PLEASE WAIT NEARLY <br>20 MINUTES TO GATHER FULL AMOUNT OF DATA</strong>' ) );
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
     <?php
            } else {
     ?>
            <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
            <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
            <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
		    <script type="text/javascript">
	    	        jQuery(document).ready(function() {
	                    $("#start-date").datepicker({minDate: '<?php echo $dpStartDate; ?>', maxDate: '<?php echo $dpEndDate; ?>'});
	                    $("#start-date").datepicker( "setDate" , '<?php echo $dpStartDate; ?>' );
	                    $("#end-date").datepicker({minDate: '<?php echo $dpStartDate; ?>', maxDate: '<?php echo $dpEndDate; ?>'});
			    		$("#end-date").datepicker( "setDate" , '<?php echo $dpEndDate; ?>' );
	        		});
	        </script>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-report' ) ); ?>">
		<p><?php _e( 'Select the date range for which you would like to get the report' ); ?></p>
                <p><?php _e( '(If you use today\'s date you will only receive partial data for the End figures that may skew your results)' ); ?></p>
		<div>
			<label for="start-date">Start date:</label>
                        <input readonly type="text" id="start-date" name="start-date">
			<label for="end-date">End date:</label>
			<input readonly type="text" id="end-date" name="end-date">
		</div>
		<p class="submit">
			<?php wp_nonce_field( 'gen_report' ); ?>
			<input type="submit" class="button-primary" name="gen_report" id="gen_report" value="<?php _e( 'Generate Report' ); ?>" />
		</p>
	</form>
     <?php
            } /* End of service date availability checking if($report_start_date == '0' || $report_end_date == '0') */

            /* End of evaluations left checking for a true case */
        } else {
            # at this level user has '0' evaluations left to use
     ?>
        <table class="form-table" id="bprp-user-account-info">
            <tbody>
                <tr>
                    <th scope="row"><?php _e( 'Error :' ); ?></th>
                    <td>
                        <?php
                        printf( __( '<strong class="bprp-error">YOUR EVALUATION COUNT EXCEEDED THE MONTHLY LIMIT OF YOUR PLAN </strong>' ) );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e( 'Like to upgrade your plan ?' ); ?></th>
                    <td>
                        <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'Upgrade' ); ?></a>
                    </td>
                </tr>
            </tbody>
        </table>
     <?php
        }

    } else {
        # in this case the user has the unlimited evaluations left, so show the date picker
    }
}

?>
</div>

