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
$serviceSettings = $this->getServiceSettings();

/**
 * @var BPRPUserAccount
 */
$userInfo = $this->getUserInfo(true);

/**
 * @var BPRPGoogleAnalytics
 */
$bprpGAObj = new BPRPGoogleAnalytics();

/**
 * @var BPRPAweber
 */
$bprpAWBObj = new BPRPAweber();

$current_path = dirname( __FILE__ ) . '/';
?>

<script type="text/javascript">

      $(document).ready( function() {
          $('.tooltip').tTips(); // tooltipize elements with classname "tooltip"
      });

</script>


<div class="wrap">
	<?php screen_icon(); ?><h2><?php _e( 'Pro Blog Stats Plugin Settings' ); ?></h2>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats' ) ); ?>">
             <input type="hidden" name="do" value="bprp" />
		<p><?php _e( 'Configure Your Pro Blog Stats Plugin Settings.' ); ?></p>
		<table class="form-table">
			<tbody>
				<?php if( is_wp_error( $userInfo ) && $userInfo->get_error_code() == 'ApiKeyInvalid' ) { ?>
				<tr>
					<td colspan="2">
						<strong class="bprp-error"><?php _e( 'Your API Key is invalid.' ); ?></strong>
						<?php printf( __( 'Re-enter your API Key or <a target="_blank" href="%s">log into your account to retrieve your API Key</a>.' ), 'https://my.scribeseo.com' ); ?>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th scope="row"><label for="bprp-api-key"><?php _e( 'Pro Blog Stats Plugin API Key' ); ?></label></th>
					<td>
						<input type="text" class="regular-text" name="bprp-api-key" id="bprp-api-key" value="<?php echo esc_attr( $settings[ 'api-key' ] ); ?>" />
						&nbsp; <img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="apikey" title="Enter Your API Key of Pro Blog Stats Plugin" class="tooltip" />
					</td>
				</tr>

			</tbody>
		</table>
		<p class="submit">
			<?php wp_nonce_field( 'save_bprp-api-key' ); ?>
			<input type="submit" class="button-primary" name="save_bprp-api-key" id="save_bprp-api-key" value="<?php _e( 'Save' ); ?>" />
		</p>
	</form>


	<h3><?php _e( 'Account Information' ); ?></h3>
	<div id="bprp-account-information">
		<?php
                include( dirname( __FILE__ ) . '/account-info.php' );
                ?>
	</div>
<?php

if(!is_wp_error( $userInfo ) ) {
    if(isset ($userInfo['services']) && !empty ($userInfo['services']) ) {
        $problogstatsUserServices = explode(',', $userInfo['services']);
        $servicelist = array();
        foreach ($problogstatsUserServices as $service) {
            $serviceName = trim($service);
            $servicelist[] = $serviceName;
        }
?>

        <h3><?php _e( 'Setup Services' ); ?></h3>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats' ) ); ?>">
             <input type="hidden" name="do" value="services" />
		<table class="form-table">
                    <tbody>
                    <?php
                        if(in_array('feedburner', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="feedburner_name"><?php _e( 'FeedBurner Name:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="feedburner_name" id="feedburner_name" value="<?php echo esc_attr( $serviceSettings[ 'feedburner_name' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#feedburner' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="FeedBurner Name" title="Enter Your Feedburner Name" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="feedburner_name"><?php _e( 'FeedBurner:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('twitter', $servicelist)) {
                    ?>
                         <tr>
                            <th scope="row"><label for="twitter_username"><?php _e( 'Twitter Username:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="twitter_username" id="twitter_username" value="<?php echo esc_attr( $serviceSettings[ 'twitter_username' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#twitter' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Twitter Username" title="Enter Your Twitter Username" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="twitter"><?php _e( 'Twitter:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('facebook', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="facebook"><?php _e( 'Facebook Page ID:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="facebook_pageid" id="facebook_pageid" value="<?php echo esc_attr( $serviceSettings[ 'facebook_pageid' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#facebook' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Facebook Page ID" title="Enter Your Facebook Page ID" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="facebook"><?php _e( 'Facebook:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('digg', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="digg_username"><?php _e( 'Digg Username:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="digg_username" id="digg_username" value="<?php echo esc_attr( $serviceSettings[ 'digg_username' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#digg' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Digg Username" title="Enter Your Digg Username" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="digg"><?php _e( 'Digg:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('postrank', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="postrank"><?php _e( 'PostRank Feed Hash:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="postrank_feedHash" id="postrank_feedHash" value="<?php echo esc_attr( $serviceSettings[ 'postrank_feedHash' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#postrank' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Clicky Site Id" title="Enter Your PostRank Feed Hash" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="postrank"><?php _e( 'PostRank:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('clicky', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="clicky_siteid"><?php _e( 'Clicky Site Id:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="clicky_siteid" id="clicky_siteid" value="<?php echo esc_attr( $serviceSettings[ 'clicky_siteid' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#clcky' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Clicky Site Id" title="Enter Your Clicky Site Id" class="tooltip" /></a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="clicky_sitekey"><?php _e( 'Clicky Site Key:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="clicky_sitekey" id="clicky_sitekey" value="<?php echo esc_attr( $serviceSettings[ 'clicky_sitekey' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#cliky_key' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Clicky Site Key" title="Enter Your Clicky Site Key" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="clicky"><?php _e( 'Clicky:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('wordpress', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="wordpress"><?php _e( 'WordPress.com API Key:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="wp_apikey" id="wp_apikey" value="<?php echo esc_attr( $serviceSettings[ 'wp_apikey' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#wordpress_api' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Clicky Site Id" title="Enter Your WordPress.com API Key" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="wordpress"><?php _e( 'WordPress.com Stats:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('klout', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="klout_username"><?php _e( 'Klout Username:' ); ?></label></th>
                            <td>
                                <input type="text" class="regular-text" name="klout_username" id="klout_username" value="<?php echo esc_attr( $serviceSettings[ 'klout_username' ] ); ?>" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#klout' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Klout Username" title="Enter Your Klout Username" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="klout"><?php _e( 'Klout:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('google_page_rank', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="google_pr"><?php _e( 'Google Page Rank:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="google_pagerank" id="google_pagerank" <?php if($serviceSettings[ 'google_pagerank' ] ) echo 'checked'; ?> value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Google Page Rank" title="Tick to get Google Page Rank in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="google_pr"><?php _e( 'Google Page Rank:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('google_indexed', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="google_indexed"><?php _e( 'Google Pages Indexed:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="google_indexed" id="google_indexed" <?php if($serviceSettings[ 'google_indexed' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Google Pages Indexed" title="Tick to get Google Pages Indexed in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="google_indexed"><?php _e( 'Google Pages Indexed:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('alexa', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="alexa"><?php _e( 'Alexa (Traffic Rank):' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="alexa" id="alexa" <?php if($serviceSettings[ 'alexa' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Alexa Traffic Rank" title="Tick to get Alexa Traffic Rank in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="alexa"><?php _e( 'Alexa (Traffic Rank):' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('yahoo_indexed', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="yahoo_indexed"><?php _e( 'Yahoo Pages Indexed:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="yahoo_indexed" id="yahoo_indexed" <?php if($serviceSettings[ 'yahoo_indexed' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Yahoo Pages Indexed" title="Tick to get Yahoo Pages Indexed in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="yahoo_indexed"><?php _e( 'Yahoo Pages Indexed:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                         if(in_array('yahoo_inlink', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="yahoo_inlinks"><?php _e( 'Yahoo InLinks:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="yahoo_inlinks" id="yahoo_inlinks" <?php if($serviceSettings[ 'yahoo_inlinks' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Yahoo InLinks" title="Tick to get Yahoo InLinks in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="yahoo_inlink"><?php _e( 'Yahoo InLinks:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('bing', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="bing"><?php _e( 'Bing Results:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="bing" id="bing" <?php if($serviceSettings[ 'bing' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Bing Results" title="Tick to get Bing Results in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="bing"><?php _e( 'Bing Results:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('compete', $servicelist)) {
                    ?>
                         <tr>
                            <th scope="row"><label for="compete"><?php _e( 'Compete Rank:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="compete" id="compete" <?php if($serviceSettings[ 'compete' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Compete" title="Tick to get Compete Rank in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="compete"><?php _e( 'Compete Rank:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('dmoz', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="dmoz"><?php _e( 'Dmoz (No. Of Entries):' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="dmoz" id="dmoz" <?php if($serviceSettings[ 'dmoz' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Dmoz" title="Tick to get count of Dmoz indexed entries in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="dmoz"><?php _e( 'Dmoz (No. Of Entries):' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('quantcast', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="quantcast"><?php _e( 'Quantcast Rank:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="quantcast" id="quantcast" <?php if($serviceSettings[ 'quantcast' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="Quantcast Rank" title="Tick to get count of Quantcast Rank in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="quantcast"><?php _e( 'Quantcast Rank:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('prweb', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="prweb"><?php _e( 'PRWeb Results:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="prweb" id="prweb" <?php if($serviceSettings[ 'prweb' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#othr_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="PRWeb" title="Tick to get count of PRWeb results in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="prweb"><?php _e( 'PRWeb:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }

                        if(in_array('stumble', $servicelist)) {
                    ?>
                        <tr>
                            <th scope="row"><label for="stumble"><?php _e( 'StumbleUpon Articles:' ); ?></label></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="stumble" id="stumble" <?php if($serviceSettings[ 'stumble' ] ) echo 'checked'; ?>  value="1" />
                                &nbsp;<a href="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats-help#other_stat' ) ) ?>" target="_blank"><img src="<?php echo $this->bprpPluginPath().'media/help_icon.gif'; ?>" alt="StumbleUpon Articles" title="Tick to get count of StumbleUpon Articles in Reports" class="tooltip" /></a>
                            </td>
                        </tr>
                    <?php
                        } else {
                    ?>
                        <tr>
                            <th scope="row"><label for="stumble"><?php _e( 'StumbleUpon Articles:' ); ?></label></th>
                            <td>
                                <a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'ADD' ); ?></a>
                            </td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
		</table>
		<p class="submit">
                    <?php wp_nonce_field( 'save_bprp-services' ); ?>
                    <input type="submit" class="button-primary" name="save_bprp-services" id="save_bprp-services" value="<?php _e( 'Save' ); ?>" />
		</p>
	</form>

	<h3><?php _e( 'Google Analytics Setup' ); ?></h3>
	<div id="bprp-account-information">
	<?php $bprpGAObj->GASetupPage(); ?>
	</div>

	<?php /*  DISABLE AWEBER SETUP
	<h3><?php _e( 'AWeber Setup' ); ?></h3>
	<div id="bprp-aweber-information">
	<?php $bprpAWBObj->AWBSetupPage(); ?>
	</div> */ ?>

<?php

    } // End of service information existance checking...
} // End of WP_Error Object checking

?>


	<form action="<?php echo esc_url( BPRP_PATH.'login.php'); ?>" method="post">
		<h3><?php _e( 'Account Login &amp; Support' ); ?></h3>
		<p><?php _e( 'Our online account center will help you manage your account and billing information.' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for=""><?php _e( 'Email ID' ); ?></label></th>
					<td>
                                            <input type="text" class="regular-text" name="email" id="email" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for=""><?php _e( 'Password' ); ?></label></th>
					<td>
                                            <input type="password" class="regular-text" name="pass" id="pass" />
						<a href="<?php echo BPRP_PATH.'forgot.php'; ?>">Forgot Password ?</a>
					</td>
				</tr>
			</tbody>
		</table>
                <input type="hidden" name="do" value="auth">
		<p class="submit">
			<input class="button-primary" type="submit" name="login_button" id="login_button" value="<?php _e( 'Login' ); ?>" />
		</p>
	</form>
</div>
