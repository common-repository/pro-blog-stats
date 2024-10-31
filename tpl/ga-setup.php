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


$accountProfiles = array();
$profileMessage = '';
if ( ! empty( $this->authToken ) ) {
	if ( empty( $this->profile ) ) {
		$profileMessage = __( ' Please choose your google analytics domain.' );
	}
	$accountProfiles = $this->getProfiles();
}
$accountProfiles = array( '' => array( 'title' => __( 'Choose your domain' ) ) ) + $accountProfiles;
if ( ! empty( $_POST ) ) {
?>
<div id="message" class="updated"><p><strong><?php echo __( 'Google Analytics Profile Saved.' ) . $profileMessage ;?></strong></p></div>
<?php } ?>
<div class="wrap">
    <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats' ) ); ?>">
        <input type="hidden" name="do" value="GA" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="key"><?php _e( 'Authentication Token' ); ?></label></th>
                    <td><p>
						<?php
						if ( get_option('bprp-ga-token') ) {
							echo '<code><b>' . get_option('bprp-ga-token') . '</b></code>';
							$add_or_change = __( 'Change' );
                        } else {
							$add_or_change = __( 'Add' );
						}
                        ?>
                        <a class="button-primary" href="https://www.google.com/accounts/AuthSubRequest?next=<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats' ) ) ?>
&amp;scope=https://www.google.com/analytics/feeds/&amp;secure=0&amp;session=1"><?php echo $add_or_change ?></a>
                    </p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="key"><?php _e('Analytics Profile'); ?></label></th>
                    <td>
                        <select name="bprp-ga-profile">
                        <?php if (!empty($accountProfiles) ) {
                                    foreach ($accountProfiles as $profileId => $profileData) {
                        ?>
                                        <option value="<?php echo $profileId;?>" <?php if($this->profile==$profileId):?> selected="selected" <?php endif;?>><?php echo $profileData['title']?></option>
                        <?php       }
                                }
                        ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
        <?php wp_nonce_field( 'save_ga_info' ); ?>
            <input class="button-primary" type="submit" name="save_ga" value="<?php _e('Save'); ?>" />
        </p>
    </form>
</div>
