<?php
if( is_wp_error( $userInfo ) ) {
	$code = $userInfo->get_error_code();
	if( -100 == $code ) {
	?>
<table class="form-table" id="bprp-user-account-info">
	<tbody>
		<tr>
			<th scope="row"><?php _e( 'Retrieving' ); ?></th>
			<td>
				<?php
				printf( __( 'Retrieving your current info... Please wait.' ) ); ?><?php
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
				printf( __( 'There was a problem retrieving your account details: <strong class="bprp-error">%s</strong>' ), $userInfo->get_error_code() );
				?>
			</td>
		</tr>
	</tbody>
</table>
	<?php
	}
} else {
	?>
<table class="form-table" id="bprp-user-account-info">
	<tbody>		
		<tr>
			<th scope="row"><?php _e( 'Your Status' ); ?></th>
			<td><strong>
				<?php echo $userInfo['status']; ?>
			</strong></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Your Plan' ); ?></th>
			<td><strong>
				<?php echo $userInfo['plan']; ?>
			</strong></td>
		</tr>
                <tr>
			<th scope="row"><?php _e( 'Reports Left This Month' ); ?></th>
			<td><strong>
				<?php echo $userInfo['evals']." / {$userInfo['maxcalls']}"; ?>
			</strong></td>
		</tr>		
	</tbody>
</table>	
	<?php
}
?>
<p class="submit">
	<a class="button-primary" href="<?php echo esc_url(BPRP_PATH).'account/'; ?>"><?php _e( 'Upgrade Account' ); ?></a>
</p>
