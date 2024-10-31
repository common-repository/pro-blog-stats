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


$accountLists = array();
$listMessage = '';
if ( ! empty( $this->authToken ) ) {
	if ( empty( $this->lists ) ) {
		$listMessage = __( ' Please choose your AWeber lists.' );
	}
	$accountLists = $this->get_lists();
}

if ( ! empty( $_POST ) ) {
?>
<div id="message" class="updated"><p><strong><?php echo __( 'AWeber Lists Saved.' ) . $listMessage; ?></strong></p></div>
<?php } ?>
<div class="wrap">

	<p  style="color:red;"><strong>Note:</strong> Selecting multiple or large AWeber lists can cause reports to take a few minutes or time out altogether.  If you encounter such issues, unselecting one or more of your AWeber lists here should solve the problem.  This issue will be resolved once AWeber updates their API.</p>
	
	<p>Visit <a href="https://auth.aweber.com/1.0/oauth/authorize_app/<?php echo $this->application_id ?>" target="_blank">https://auth.aweber.com/1.0/oauth/authorize_app/<?php echo $this->application_id ?></a> to get your authorization code.</p>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=pro-blog-stats' ) ); ?>">
        <input type="hidden" name="do" value="GA" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="bprp-awb-token"><?php _e( 'Authorization Code' ); ?></label></th>
                    <td>
						<input type="text" name="bprp-awb-token" id="bprp-awb-token" value="<?php echo $this->authToken; ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="key"><?php _e('Lists'); ?></label></th>
                    <td>
						<?php
						if ( ! empty( $accountLists ) ) {
							foreach ( $accountLists as $list ) {
						?>
							<label><input type="checkbox" name="bprp-awb-lists[]" id="bprp-awb-lists-<?php echo $list->id;?>" value="<?php echo $list->id;?>" <?php if ( in_array( $list->id, $this->lists ) ) :?> checked="checked" <?php endif; ?>> <?php echo $list->name ?></label><br />
						<?php
							}
						} else {
							_e( '<p>No list retrieved.</p>' );
						}
						?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
        <?php wp_nonce_field( 'save_awb_info' ); ?>
            <input class="button-primary" type="submit" name="save_awb" value="<?php _e('Save'); ?>" />
        </p>
    </form>
</div>
