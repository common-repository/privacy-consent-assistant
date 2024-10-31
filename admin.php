<?php
	if ( ! defined( 'ABSPATH' ) ) exit;

	wp_enqueue_style( 'trm-gdpr-admin' );
	wp_enqueue_script( 'trm-gdpr-admin' );

	$pages = get_pages();

	if( isset( $_POST['_update_trm_gdpr'] ) ){
		if( $_POST['_update_trm_gdpr'] == true ){
			if( wp_verify_nonce( $_POST['_wpnonce'], 'update-trm-gdpr-options' ) ){
				foreach( $this::$option_fields as $field => $type ){
					if( !empty( $_POST[$field] ) ){
						update_option( $field, $this->validate_option( $_POST[$field], $type ) );
					} else {
						update_option( $field, '' );
					}
				}
			}
		}
	}
?>
<div id="gdpr" class="wrap metabox-holder">
	<h1>Privacy & Consent Assistant Settings</h1>
	<div class="grid">
		<div id="menu" col-span="1">
			<br />
			<a href="#options" <?= isset( $_POST['gdpr_needle'] ) ? '' : 'class="current"'; ?>>Options</a>
			<a href="#database" <?= isset( $_POST['gdpr_needle'] ) ? 'class="current"' : ''; ?>>User Data</a>
			<a href="<?= admin_url( 'edit.php?post_type=gdpr-policy' ); ?>">Policies</a>
			<br />
		</div>
		<form method="post" id="options" class="admin-panel <?= isset( $_POST['gdpr_needle'] ) ? 'hide' : 'show'; ?>" col-span="11">
			<?php wp_nonce_field( 'update-trm-gdpr-options' ); ?>
			<h2>Options - <input class="wp-core-ui button-primary" value="Save Options" name="submit" type="submit" /></h2>
			<div class="postbox" id="policy-variables">
				<h2 class="hndle"><span>Policy Variables</span></h2>
				<div class="grid" gap columns="4">
					<?php foreach( ['company_name', 'company_address', 'company_email', 'company_phone'] as $field ){ ?>
						<label>
							<strong><?= $this->stringify( $field ); ?></strong>
							<input class="widefat" type="text" name="<?= "trm_gdpr_$field" ?>" value="<?= stripslashes( get_option( "trm_gdpr_$field" ) ); ?>" />
						</label>
					<?php } ?>
				</div>
				<div class="grid" gap columns="4" style="margin-top: 10px;">
					<?php foreach( ['governing_state', 'governing_country'] as $field ){ ?>
						<label>
							<strong><?= $this->stringify( $field ); ?></strong>
							<input class="widefat" type="text" name="<?= "trm_gdpr_$field" ?>" value="<?= stripslashes( get_option( "trm_gdpr_$field" ) ); ?>" />
						</label>
					<?php } ?>
				</div>
			</div>
			<div class="postbox" id="policy-pages">
				<h2 class="hndle"><span>Policy Pages</span></h2>
				<div class="grid" gap columns="4">
					<?php foreach( $this::$policies as $policy ){ ?>
						<?php
							$option = $this->optionize( $policy );
							$existing_value = ( $existing = get_option( "trm_gdpr_overwrite_$option" ) ) ? $existing : '';
						?>
						<label>
							<strong><?= $policy; ?> Page</strong>
							<select name="<?= "trm_gdpr_overwrite_$option"; ?>">
								<option value="default" <?= $existing_value == 'default' ? 'selected' : ''; ?>>Plugin Default</option>
								<option value="custom" <?= $existing_value == 'custom' ? 'selected' : ''; ?>>Custom URL</option>
								<?php foreach( $pages as $page ){
									$selected = ( $existing_value == $page->ID ) ? 'selected' : '';
									echo "<option $selected value='{$page->ID}'>{$page->post_title}</option>";
								} ?>
							</select>
							<a class="wp-core-ui button" href="<?= $this->policy_pages()->$option['url']; ?>" target="_blank">View Current Page</a>
							<br /><input type="text" name="<?= "trm_gdpr_custom_$option"; ?>" value="<?= get_option( "trm_gdpr_custom_$option" ); ?>" placeholder="Custom URL" class="widefat <?= $existing_value == 'custom' ? 'show' : 'hide'; ?>" />
						</label>
					<?php } ?>
				</div>
			</div>
			<div class="postbox">
				<h2 class="hndle"><span>Consent Messages</span></h2>
				<div class="grid" columns="2" gap>
					<div>
						<label>
							<strong>Overwrite Form Consent Message:</strong>
							<?php
								wp_editor(
									stripslashes( get_option('trm_gdpr_overwrite_notice_form_consent') ),
									'trm_gdpr_overwrite_notice_form_consent',
									[
										'wpautop' => false,
										'media_buttons' => false,
										'textarea_rows' => 5,
									]
								);
							?>
						</label><br/>
						<span class="description"><strong>Default:</strong> <?= stripslashes( get_option( 'trm_gdpr_default_notice_form_consent' ) ); ?></span>
					</div>
					<div>
					<label>
						<strong>Overwrite Consent Bar Message:</strong>
						<?php
							wp_editor(
								stripslashes( get_option('trm_gdpr_overwrite_notice_consent_bar') ),
								'trm_gdpr_overwrite_notice_consent_bar',
								[
									'wpautop' => false,
									'media_buttons' => false,
									'textarea_rows' => 5,
								]
							);
						?>
					</label><br/>
					<span class="description"><strong>Default:</strong> <?= stripslashes( get_option( 'trm_gdpr_default_notice_consent_bar' ) ); ?></span>
					</div>
				</div>
			</div>
			<h2>Advanced</h2>
			<div class="postbox">
				<h2 class="hndle"><span>Disable Features</span></h2>
				<div class="grid" columns="4" gap>
					<?php
						$features = [
							'Disable Form Consent',
							'Disable Consent bar',
							'Disable Subfooter',
							'Disable Hide Existing Links',
						];

						foreach( $features as $feature ){ $option = $this->optionize( "trm_gdpr_$feature" ); ?>
							<label>
								<input type="checkbox" name="<?= $option; ?>" <?= checked( get_option( $option ), 1 ); ?> /><strong><?= $feature; ?></strong>
							</label>
						<?php }
					?>
				</div>
			</div>
			<div class="postbox">
				<h2 class="hndle"><span>Additional Functions when Consent Bar is Closed</span></h2>
				<span class="descrtiption">The <i style="width: 24px; transform: translateY(7px); display: inline-block;"><?= $this->icon('close'); ?></i> icon runs a native function on click and then returns false. You may insert additional functions between those here. Treat this as a "confirmed consent" if using the default or other appropriate text.</span>
				<pre><textarea name="trm_gdpr_close_consent_functions" class="widefat wp-core-ui" rows="5"><?= stripslashes( get_option( 'trm_gdpr_close_consent_functions' ) ); ?></textarea></pre>
			</div>
			<div class="postbox">
				<h2 class="hndle"><span>Removed Form Notices (One Per Line)</span></h2>
				<pre><textarea name="trm_gdpr_dynamic_style" class="widefat wp-core-ui" rows="5"><?= stripslashes( get_option( 'trm_gdpr_dynamic_style' ) ); ?></textarea></pre>
			</div>
			<input type="hidden" name="_update_trm_gdpr" value="true" />
			<div style="text-align: right;"><input class="wp-core-ui button-primary" value="Save Options" name="submit" type="submit" /></div>
		</form>
		<div id="database" class="admin-panel <?= isset( $_POST['gdpr_needle'] ) ? 'show' : 'hide'; ?>" col-span="11">
			<h2>User Data</h2>
			<form class="search-form" method="post">
				<input type="search" placeholder="Search Database…" name="gdpr_needle" />
				<i class="admin-search"><?= $this->icon( 'search' ); ?></i>
				<input type="submit" class="wp-core-ui button" value="Search" />
			</form>
			<?php
				if( isset( $_POST['gdpr_needle'] ) ){
					if( $needle = filter_var( $_POST['gdpr_needle'], FILTER_SANITIZE_STRING ) ){
						echo "<h2>Search Results for: <strong style='color: #0095ee; font-weight: 700;'>$needle</strong></h2>";
						echo '<div style="border-left: 4px solid #0095ee; padding: 8px 14px; box-shadow: 0 2px 6px -2px rgba(0,0,0,.3); margin-bottom: 18px; background: #fff;"><strong>Note:</strong> This is for reference only. Deletion and Reporting will come out in a future release.</div>';
						global $wpdb;
						// Get All Relevant Tables
						if( $tables = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}%'", OBJECT ) ){
							foreach( $tables as $table_object ){
								// Get Table Name w/o Query
								$table_object_vars = get_object_vars( $table_object );
								$table = array_shift( $table_object_vars );

								// Get All Columns From Current Table
								$columns = $wpdb->get_results( "SELECT column_name FROM information_schema.columns WHERE table_name = '$table'", OBJECT );

								foreach( $columns as $column_object ){
									// Get Column Name w/o Info
									$column_object_vars = get_object_vars( $column_object );
									$column = array_shift( $column_object_vars );

									if( $rows = $wpdb->get_results( "SELECT * FROM `$table` WHERE `$column` LIKE '%{$needle}%'", OBJECT ) ){
										$row_count = 0;
										foreach( $rows as $row ){
											if( $row_count == 0 ){
												echo '<div class="postbox"><h2 class="hndle"><span>'. $table .'</span></h2>';
												echo '<table><thead><tr>';
													foreach( $row as $col => $val ) echo "<th>$col</th>";
												echo '</tr></thead><tbody>';
											}
											echo '<tr>';
												foreach( $row as $col => $val ){
													$style = ( strpos( $val, "\n" ) !== false ) ? 'style="min-height: 120px;"' : '';

													if( stripos( $val, $needle ) !== false ) {
														$class = 'class="editable"';
														//$edit  = '<button><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit" color="#3a4049" data-reactid="487"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg></button>';
														$edit = ''; // Temporarily Removed
													} else {
														$class = $edit = '';
													}

													$val = ( $unserialize = @unserialize( $val ) ) ? "<textarea $style>$val</textarea>" : '<div>'. esc_attr( $val ).'</div>';
													echo ( $col == 'user_pass' ) ? "<td $class>[REDACTED]</td>" : "<td $class>$val $edit</td>";
												}
											echo '</tr>';
											$row_count++;
										}
										echo '</tbody></table></div>';
									}
								}
							}
						}
					} else {
						echo 'Please search for valid contact details.';
					}
				}
			?>
		</div>
	</div>
</div>
