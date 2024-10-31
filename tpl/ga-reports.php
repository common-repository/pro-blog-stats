<div>
    <script type='text/javascript' src='http://www.google.com/jsapi'></script>
<h2>Google Analytics</h2>

<script type='text/javascript'>
/* <![CDATA[ */
<?php
$startTime = strtotime ( '-1 month' );
$endEnd = strtotime ( "now" );

//$startTime = strtotime ($reqStartDate);
//$endEnd = strtotime ($reqEndDate);

$records = $bprpGAObj->getAnalyticRecords ( date ( 'Y-m-d', $startTime ), date ( 'Y-m-d', $endEnd ), 'ga:date', 'ga:visitors,ga:newVisits,ga:visits,ga:pageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits' );
?>
	google.load('visualization', '1', {packages:['annotatedtimeline','piechart', 'geomap','table']});
        google.setOnLoadCallback(gaChartTimeline);
	function gaChartTimeline() {
	    var gaData = new google.visualization.DataTable();
	    gaData.addColumn('date', 'Date');
	    gaData.addColumn('number', 'Visits');
	    gaData.addColumn('number', 'Pageviews');
	    gaData.addColumn('number', 'Visitors');
	    gaData.addColumn('number', 'New Visits');
	    gaData.addRows(<?php echo count ( $records ['entry'] );?>);
		<?php
		if (! empty ( $records ['entry'] )) {
			$row = 0;
			$script = '';
			foreach ( $records ['entry'] as $record ) {
				$date = date ( 'Y,m-1,d', strtotime ( $record ['dimension'] ['ga:date'] ) );
				$script .= "gaData.setValue({$row}, 0, new Date({$date}));gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});gaData.setValue({$row}, 2, {$record['metric']['ga:pageviews']});gaData.setValue({$row}, 3, {$record['metric']['ga:visitors']});gaData.setValue({$row}, 4, {$record['metric']['ga:newVisits']});";
				$row ++;
			}
		}
		echo $script;
		?>
	    var gaVisitsPageviewsChart = new google.visualization.AnnotatedTimeLine(document.getElementById('chartTimeline'));
	    gaVisitsPageviewsChart.draw(gaData, {
	        wmode: 'transparent',
	        displayZoomButtons: false,
	        displayAnnotations: true
	    });
	}

<?php
$keywordsRecords = $bprpGAObj->getAnalyticRecords( date ( 'Y-m-d', $startTime ), date ( 'Y-m-d', $endEnd ) , 'ga:keyword' , 'ga:visits', '-ga:visits', '50');
?>
	google.setOnLoadCallback(gaTableKeywords);
	function gaTableKeywords(){
	    var gaData = new google.visualization.DataTable();
	    gaData.addColumn('string', 'Keywords');
	    gaData.addColumn('number', 'Visits');
	    gaData.addRows(<?php echo count ( $keywordsRecords ['entry'] );?>);
		<?php
		if (! empty ( $keywordsRecords ['entry'] )) {
			$row = 0;
			$script = '';
			foreach ( $keywordsRecords ['entry'] as $record ) {
				$script .= "gaData.setValue({$row}, 0, \"".esc_js($record['dimension']['ga:keyword'])."\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
				$row ++;
			}
		}
		echo $script;
		?>
		var table = new google.visualization.Table(document.getElementById('tableKeywords'));
		table.draw(gaData, {pageSize:10,page:'enable',showRowNumber: true});
	}
<?php
$sourceRecords = $bprpGAObj->getAnalyticRecords( date ( 'Y-m-d', $startTime ), date ( 'Y-m-d', $endEnd ) , 'ga:source' , 'ga:visits', '-ga:visits', '50');
?>
	google.setOnLoadCallback(gaTableSource);
	function gaTableSource(){
	    var gaData = new google.visualization.DataTable();
	    gaData.addColumn('string', 'Source');
	    gaData.addColumn('number', 'Visits');
	    gaData.addRows(<?php echo count ( $sourceRecords ['entry'] );?>);
		<?php
		if (! empty ( $sourceRecords ['entry'] )) {
			$row = 0;
			$script = '';
			foreach ( $sourceRecords ['entry'] as $record ) {
				$script .= "gaData.setValue({$row}, 0, \"".esc_js($record['dimension']['ga:source'])."\" );gaData.setValue({$row}, 1, {$record['metric']['ga:visits']});";
				$row ++;
			}
		}
		echo $script;
		?>
                var chart = new google.visualization.PieChart(document.getElementById('tableSource'));
                chart.draw(gaData, {width: 400, height: 250, is3D:true, title: 'Source Information'});

	}

/* ]]> */
</script>
<style type="text/css">
.summary {
      background-color: #FFFFFF;
}
table.social_media {
    margin: 0px;
    padding: 0px;
    width: 50%;
}

table.blogstatics {
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
    text-align: left;
    background-color: #ccc;
    width: 30%;
    padding: 2px;
    border: 1px solid #ccc;
}
.media_head2 {
    text-align: left;
    background-color: #ccc;
    width: 20%;
    padding: 2px;
    border: 1px solid #ccc;
}
table.social_media
{
    border-left: 1px solid #ddd;
}
table.blogstatics
{
    border-left: 1px solid #ddd;
}
table.social_media td {
    padding: 4px;
    border-bottom: 1px solid #ddd;
    border-top: 1px solid #fff;
    border-right: 1px solid #ddd;
    border-left: 1px solid #fff;
    background:#f1f1f1;
}
table.blogstatics td {
    padding: 4px;
    border-bottom: 1px solid #ddd;
    border-top: 1px solid #fff;
    border-right: 1px solid #ddd;
    border-left: 1px solid #fff;
    background:#f1f1f1;
}

table.search_engine td {
    padding: 4px;
    border: 1px solid #ddd;
}
.chartholder {
    border: 0px;
    padding: 0px;
    width: 50% !important;
    height: 200px;
}
.icon32
{
display:none;
}
h2
{
border-bottom:1px solid #dddddd !important;
padding:5px 0px !important;
margin-bottom:5px !important;
}
.social_media
{
 width:100% !important;
}
</style>
<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" >
    <tr valign="top">
        <td width="50%" valign="top" align="center">
            <!-- Set the static reports  -->
            <table class="social_media" cellpadding="0" cellspacing="1" border="0">
                <tr>
                    <th width="50%" align="left" valign="middle" class="media_head1"><h3>Data Type</h3></th>
                    <th width="50%" align="left" valign="middle" class="media_head2"><h3>Result</h3></th>
                </tr>
                <tr>
                    <td width="50%" align="left" valign="middle">Total Visits</td>
                    <td width="50%" align="left" valign="middle"><?php echo @number_format ( $records ['aggregates'] ['metric'] ['ga:visits'] ); ?></td>
                </tr>
                <tr>
                    <td width="50%" align="left" valign="middle">Pageviews</td>
                    <td width="50%" align="left" valign="middle"><?php echo @number_format ( $records ['aggregates'] ['metric'] ['ga:pageviews'] ); ?></td>
                </tr>
                <tr>
                    <td width="50%" align="left" valign="middle">New Visits</td>
                    <td width="50%" align="left" valign="middle"><?php echo @number_format ( $records ['aggregates'] ['metric'] ['ga:newVisits'] ); ?></td>
                </tr>
                <tr>
                    <td width="50%" align="left" valign="middle">Pages/Visit</td>
                    <td width="50%" align="left" valign="middle"><?php echo @number_format ( $records ['aggregates'] ['metric'] ['ga:pageviews'] / $records ['aggregates'] ['metric'] ['ga:visits'], 2 ); ?></td>
                </tr>
                <tr>
                    <td width="50%" align="left" valign="middle">Bounce Rate</td>
                    <td width="50%" align="left" valign="middle"><?php echo @number_format ( $records ['aggregates'] ['metric'] ['ga:bounces'] / $records ['aggregates'] ['metric'] ['ga:entrances'] * 100, 2 ); ?> % </td>
                </tr>
            </table>
      </td>
        <td width="50%" valign="top" align="left">
            <div id="tableSource" class="chartholder"></div>
        </td>
    </tr>
    <tr valign="top">
        <td width="40%" valign="top" colspan="2">
            <div id="tableKeywords" class="chartholder"></div>
        </td>
    </tr>
    <tr valign="top">
        <td width="70%" valign="top" colspan="2">
            <div id="chartTimeline" class="chartholder" style="height:300px;"></div>
        </td>
    </tr>
</table>
</div>

