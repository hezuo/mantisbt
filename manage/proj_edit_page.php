<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit Project Page
 *
 * @package MantisBT
 * @copyright Copyright 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses core.php
 * @uses access_api.php
 * @uses authentication_api.php
 * @uses category_api.php
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses custom_field_api.php
 * @uses date_api.php
 * @uses event_api.php
 * @uses file_api.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses helper_api.php
 * @uses html_api.php

 * @uses print_api.php
 * @uses project_api.php
 * @uses project_hierarchy_api.php
 * @uses string_api.php
 * @uses user_api.php
 * @uses utility_api.php
 * @uses version_api.php
 */

require_once( '../core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'category_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'custom_field_api.php' );
require_api( 'date_api.php' );
require_api( 'event_api.php' );
require_api( 'file_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'print_api.php' );
require_api( 'project_api.php' );
require_api( 'project_hierarchy_api.php' );
require_api( 'string_api.php' );
require_api( 'user_api.php' );
require_api( 'utility_api.php' );
require_api( 'version_api.php' );

auth_reauthenticate();

$f_project_id = gpc_get_int( 'project_id' );
$f_show_global_users = gpc_get_bool( 'show_global_users' );

project_ensure_exists( $f_project_id );
access_ensure_project_level( config_get( 'manage_project_threshold' ), $f_project_id );

$row = project_get_row( $f_project_id );

$t_can_manage_users = access_has_project_level( config_get( 'project_user_threshold' ), $f_project_id );

html_page_top( project_get_field( $f_project_id, 'name' ) );

print_manage_menu( 'proj_edit_page.php' );
?>

<!-- PROJECT PROPERTIES -->
<div id="manage-proj-update-div" class="form-container">
	<form id="manage-proj-update-form" method="post" action="proj_update.php">
		<fieldset>
			<legend><span><?php echo _( 'Edit Project' ) ?></span></legend>
			<?php echo form_security_field( 'manage_proj_update' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<div class="field-container">
				<label for="project-name"><span><?php echo _( 'Project Name' ) ?></span></label>
				<span class="input"><input type="text" id="project-name" name="name" size="50" maxlength="128" value="<?php echo string_attribute( $row['name'] ) ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="project-status"><span><?php echo _( 'Status' ) ?></span></label>
				<span class="select">
					<select id="project-status" name="status">
						<?php print_enum_string_option_list( 'project_status', $row['status'] ) ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="project-enabled"><span><?php echo _('Enabled') ?></span></label>
				<span class="checkbox"><input type="checkbox" id="project-enabled" name="enabled" <?php check_checked( $row['enabled'], ON ); ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="project-inherit-global"><span><?php echo _( 'Inherit Global Categories' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="project-inherit-global" name="inherit_global" <?php check_checked( $row['inherit_global'], ON ); ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="project-view-state"><span><?php echo _( 'View Status' ) ?></span></label>
				<span class="select">
					<select id="project-view-state" name="view_state">
						<?php print_enum_string_option_list( 'view_state', $row['view_state']) ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<?php
			if ( file_is_uploading_enabled() ) { ?>
			<div class="field-container">
				<label for="project-file-path"><span><?php echo _( 'Upload File Path' ) ?></span></label>
				<span class="input"><input type="text" id="project-file-path" name="file_path" size="50" maxlength="250" value="<?php echo string_attribute( $row['file_path'] ) ?>" /></span>
				<span class="label-style"></span>
			</div><?php
			} ?>
			<div class="field-container">
				<label for="project-description"><span><?php echo _( 'Description' ) ?></span></label>
				<span class="textarea"><textarea id="project-description" name="description" cols="60" rows="5"><?php echo string_textarea( $row['description'] ) ?></textarea></span>
				<span class="label-style"></span>
			</div>

			<?php event_signal( 'EVENT_MANAGE_PROJECT_UPDATE_FORM', array( $f_project_id ) ); ?>

			<span class="submit-button"><input type="submit" class="button" value="<?php echo _( 'Update Project' ) ?>" /></span>
		</fieldset>
	</form>
</div>

<!-- PROJECT DELETE -->
<?php
# You must have global permissions to delete projects
if ( access_has_global_level ( config_get( 'delete_project_threshold' ) ) ) { ?>
<div id="project-delete-div" class="form-container">
	<form id="project-delete-form" method="post" action="proj_delete.php" class="action-button">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_delete' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<input type="submit" class="button" value="<?php echo _( 'Delete Project' ) ?>" />
		</fieldset>
	</form>
</div>
<?php } ?>

<!-- SUBPROJECTS -->
<div id="manage-project-update-subprojects-div" class="form-container">
	<h2><?php echo _( 'Subprojects' ); ?></h2>
	<?php
		# Check the user's global access level before allowing project creation
		if ( access_has_global_level ( config_get( 'create_project_threshold' ) ) ) {
			print_button( 'proj_create_page.php?parent_id=' . $f_project_id, _( 'Create New Subproject' ) );
		} ?>
		<form id="manage-project-subproject-add-form" method="post" action="proj_subproj_add.php">
			<fieldset>
				<?php echo form_security_field( 'manage_proj_subproj_add' ) ?>
				<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
				<select name="subproject_id"><?php
				$t_all_subprojects = project_hierarchy_get_subprojects( $f_project_id, /* $p_show_disabled */ true );
				$t_all_subprojects[] = $f_project_id;
				$t_manage_access = config_get( 'manage_project_threshold' );
				$t_projects = project_get_all_rows();
				$t_projects = multi_sort( $t_projects, 'name', ASCENDING );
				foreach ( $t_projects as $t_project ) {
					if ( in_array( $t_project['id'], $t_all_subprojects ) ||
						in_array( $f_project_id, project_hierarchy_get_all_subprojects( $t_project['id'] ) ) ||
						!access_has_project_level( $t_manage_access, $t_project['id'] ) ) {
						continue;
					} ?>
					<option value="<?php echo $t_project['id'] ?>"><?php echo string_attribute( $t_project['name'] ) ?></option><?php
				} # End looping over projects ?>
				</select>
				<input type="submit" value="<?php echo _( 'Add as Subproject' ); ?>" />
			</fieldset>
		</form>
	<?php

	$t_subproject_ids = user_get_accessible_subprojects( auth_get_current_user_id(), $f_project_id, /* show_disabled */ true );
	if ( array() != $t_subproject_ids ) { ?>
	<form id="manage-project-update-subprojects-form" action="proj_update_children.php" method="post">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_update_children' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<table cellspacing="1" cellpadding="5" border="1">
				<tr class="row-category">
					<th><?php echo _( 'Name' ) ?></th>
					<th><?php echo _( 'Status' ) ?></th>
					<th><?php echo _('Enabled') ?></th>
					<th><?php echo _('Inherit Categories') ?></th>
					<th><?php echo _( 'View Status' ) ?></th>
					<th><?php echo _( 'Description' ) ?></th>
					<th colspan="2"><?php echo _( 'Actions' ) ?></th>
				</tr><?php
				foreach ( $t_subproject_ids as $t_subproject_id ) {
					$t_subproject = project_get_row( $t_subproject_id );
					$t_inherit_parent = project_hierarchy_inherit_parent( $t_subproject_id, $f_project_id, true ); ?>
				<tr>
					<td>
						<a href="proj_edit_page.php?project_id=<?php echo $t_subproject['id'] ?>"><?php echo string_display( $t_subproject['name'] ) ?></a>
					</td>
					<td class="center">
						<?php echo get_enum_element( 'project_status', $t_subproject['status'] ) ?>
					</td>
					<td class="center">
						<?php echo trans_bool( $t_subproject['enabled'] ) ?>
					</td>
					<td class="center">
						<input type="checkbox" name="inherit_child_<?php echo $t_subproject_id ?>" <?php echo ( $t_inherit_parent ? 'checked="checked"' : '' ) ?> />
					</td>
					<td class="center">
						<?php echo get_enum_element( 'project_view_state', $t_subproject['view_state'] ) ?>
					</td>
					<td>
						<?php echo string_display_links( $t_subproject['description'] ) ?>
					</td>
					<td class="center"><?php
					print_bracket_link( 'proj_edit_page.php?project_id=' . $t_subproject['id'], _( 'Edit' ) ); ?>
					</td>
					<td class="center"><?php
					print_bracket_link( "proj_subproj_delete.php?project_id=$f_project_id&subproject_id=" . $t_subproject['id'] . form_security_param( 'manage_proj_subproj_delete' ), _( 'Unlink' ) );
				?>
					</td>
				</tr><?php
				} # End of foreach loop over subprojects ?>
			</table>
			<span class="submit-button"><input type="submit" value="<?php echo _( 'Update Subproject Inheritance' ) ?>" /></span>
		</fieldset>
	</form><?php
	} # End of hiding subproject listing if there are no subprojects ?>

</div>

<div id="categories" class="form-container">
	<h2><?php echo _( 'Categories' ); ?></h2>
	<form id="manage-project-category-copy-form" method="post" action="proj_cat_copy.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_cat_copy' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<select name="other_project_id">
				<?php print_project_option_list( null, false, $f_project_id ); ?>
			</select>
			<input type="submit" name="copy_from" class="button" value="<?php echo _( 'Copy Categories From' ) ?>" />
			<input type="submit" name="copy_to" class="button" value="<?php echo _( 'Copy Categories To' ) ?>" />
		</fieldset>
	</form><?php
	$t_categories = category_get_all_rows( $f_project_id );
	if ( count( $t_categories ) > 0 ) { ?>
	<table cellspacing="1" cellpadding="5" border="1">
		<tr class="row-category">
			<th><?php echo _( 'Category' ) ?></th>
			<th><?php echo _( 'Assign To' ) ?></th>
			<th colspan="2" class="center"><?php echo _( 'Actions' ) ?></th>
		</tr><?php

		foreach ( $t_categories as $t_category ) {
			$t_id = $t_category['id'];
			$t_inherited = ( $t_category['project_id'] != $f_project_id ?  true : false );

			$t_name = $t_category['name'];
			if ( NO_USER != $t_category['user_id'] && user_exists( $t_category['user_id'] )) {
				$t_user_name = user_get_name( $t_category['user_id'] );
			} else {
				$t_user_name = '';
			} ?>

		<tr>
			<td><?php echo string_display( category_full_name( $t_category['id'] , /* showProject */ $t_inherited, $f_project_id ) )  ?></td>
			<td><?php echo string_display_line( $t_user_name ) ?></td>
			<td class="center">
				<?php if ( !$t_inherited ) {
					$t_id = urlencode( $t_id );
					$t_project_id = urlencode( $f_project_id );

					print_button( 'proj_cat_edit_page.php?id=' . $t_id . '&project_id=' . $t_project_id, _( 'Edit' ) );
				} ?>
			</td>
			<td class="center">
				<?php if ( !$t_inherited ) {
					print_button( 'proj_cat_delete.php?id=' . $t_id . '&project_id=' . $t_project_id, _( 'Delete' ) );
				} ?>
			</td>
		</tr><?php
		} # end for loop ?>
	</table><?php
	} ?>

	<form id="project-add-category-form" method="post" action="proj_cat_add.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_cat_add' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<input type="text" name="name" size="32" maxlength="128" />
			<input type="submit" class="button" value="<?php echo _( 'Add Category' ) ?>" />
		</fieldset>
	</form>

</div>

<div id="project-versions-div" class="form-container">
	<h2><?php echo _( 'Versions' ); ?></h2>
	<form id="manage-project-version-copy-form" method="post" action="proj_ver_copy.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_ver_copy' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<select name="other_project_id">
				<?php print_project_option_list( null, false, $f_project_id ); ?>
			</select>
			<input type="submit" name="copy_from" class="button" value="<?php echo _( 'Copy Versions From' ) ?>" />
			<input type="submit" name="copy_to" class="button" value="<?php echo _( 'Copy Versions To' ) ?>" />
		</fieldset>
	</form><?php

	$t_versions = version_get_all_rows( $f_project_id, /* released = */ null, /* obsolete = */ null );
	if ( count( $t_versions ) > 0 ) { ?>
	<table id="versions" cellspacing="1" cellpadding="5" border="1">
		<tr class="row-category">
			<th><?php echo _( 'Version' ) ?></th>
			<th><?php echo _( 'Released' ) ?></th>
			<th><?php echo _( 'Obsolete' ) ?></th>
			<th><?php echo _( 'Timestamp' ) ?></th>
			<th colspan="2"><?php echo _( 'Actions' ) ?></th>
		</tr><?php

		foreach ( $t_versions as $t_version ) {
			$t_inherited = ( $t_version['project_id'] != $f_project_id ?  true : false );
			$t_name = version_full_name( $t_version['id'], /* showProject */ $t_inherited, $f_project_id );
			$t_released = $t_version['released'];
			$t_obsolete = $t_version['obsolete'];
			if( !date_is_null( $t_version['date_order'] ) ) {
				$t_date_formatted = date( config_get( 'complete_date_format' ), $t_version['date_order'] );
			} else {
				$t_date_formatted = ' ';
			} ?>

		<tr>
			<td><?php echo string_display( $t_name ) ?></td>
			<td><?php echo trans_bool( $t_released ) ?></td>
			<td><?php echo trans_bool( $t_obsolete ) ?></td>
			<td><?php echo $t_date_formatted ?></td>
			<td><?php
				$t_version_id = version_get_id( $t_name, $f_project_id );
				if ( !$t_inherited ) {
					print_button( 'proj_ver_edit_page.php?version_id=' . $t_version_id, _( 'Edit' ) );
				} ?>
			</td>
			<td><?php
				if ( !$t_inherited ) {
					print_button( 'proj_ver_delete.php?version_id=' . $t_version_id, _( 'Delete' ) );
				} ?>
			</td>
		</tr><?php
		} # end for loop ?>
	</table><?php
	} ?>
	<form id="manage-project-add-version-form" method="post" action="proj_ver_add.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_ver_add' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<input type="text" name="version" size="32" maxlength="64" />
			<input type="submit" name="add_version" class="button" value="<?php echo _( 'Add Version' ) ?>" />
			<input type="submit" name="add_and_edit_version" class="button" value="<?php echo _( 'Add and Edit Version' ) ?>" />
		</fieldset>
	</form>
</div><?php

# You need either global permissions or project-specific permissions to link
#  custom fields
$t_custom_field_count = count( custom_field_get_ids() );
if ( access_has_project_level( config_get( 'custom_field_link_threshold' ), $f_project_id ) &&
	( $t_custom_field_count > 0 ) ) {
?>
<div id="customfields" class="form-container">
	<h2><?php echo _( 'Custom Fields' ) ?></h2>
	<form id="manage-project-custom-field-copy-form" method="post" action="proj_custom_field_copy.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_custom_field_copy' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<select name="other_project_id">
				<?php print_project_option_list( null, false, $f_project_id ); ?>
			</select>
			<input type="submit" name="copy_from" class="button" value="<?php echo _( 'Copy From' ) ?>" />
			<input type="submit" name="copy_to" class="button" value="<?php echo _( 'Copy To' ) ?>" />
		</fieldset>
	</form><?php
	$t_custom_fields = custom_field_get_linked_ids( $f_project_id );
	$t_linked_count = count( $t_custom_fields );
	if ( $t_linked_count > 0 ) { ?>
	<table cellspacing="1" cellpadding="5" border="1">
		<tr class="row-category">
			<th><?php echo _( 'Field' ) ?></th>
			<th><?php echo _( 'Sequence' ) ?></th>
			<th><?php echo _( 'Actions' ); ?></th>
		</tr><?php
		foreach( $t_custom_fields as $t_field_id ) {
			$t_desc = custom_field_get_definition( $t_field_id ); ?>
			<tr>
				<td><?php echo string_display( $t_desc['name'] ) ?></td>
				<td>
					<form method="post" action="proj_custom_field_update.php">
						<fieldset>
							<?php echo form_security_field( 'manage_proj_custom_field_update' ) ?>
							<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
							<input type="hidden" name="field_id" value="<?php echo $t_field_id ?>" />
							<input type="text" name="sequence" value="<?php echo custom_field_get_sequence( $t_field_id, $f_project_id ) ?>" size="2" />
							<input type="submit" class="button-small" value="<?php echo _( 'Update' ) ?>" />
						</fieldset>
					</form>
				</td>
				<td><?php
					# You need global permissions to edit custom field defs
					print_button( "proj_custom_field_remove.php?field_id=$t_field_id&project_id=$f_project_id", _( 'Remove' ) ); ?>
				</td>
			</tr><?php
		} # end for loop ?>
	</table><?php
	}

	if( $t_custom_field_count > $t_linked_count ) { ?>
	<form method="post" action="proj_custom_field_add_existing.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_custom_field_add_existing' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<select name="field_id">
				<?php
					$t_custom_fields = custom_field_get_ids();

					foreach( $t_custom_fields as $t_field_id )
					{
						if( !custom_field_is_linked( $t_field_id, $f_project_id ) ) {
							$t_desc = custom_field_get_definition( $t_field_id );
							echo "<option value=\"$t_field_id\">" . string_attribute( $t_desc['name'] ) . '</option>' ;
						}
					}
				?>
			</select>
			<input type="submit" class="button" value="<?php echo _( 'Add This Existing Custom Field' ) ?>" />
		</fieldset>
	</form><?php
	} ?>
</div><?php
}

event_signal( 'EVENT_MANAGE_PROJECT_PAGE', array( $f_project_id ) );
?>

<div class="important-msg"><?php
	if ( VS_PUBLIC == project_get_field( $f_project_id, 'view_state' ) ) {
		echo _( 'This project is public. All users have access.' );
	} else {
		echo _( 'This project is private. Only administrators and manually added users have access.' );
	} ?>
</div>

<div id="manage-project-users-div" class="form-container">
	<h2><?php echo _( 'Manage Accounts' ) ?></h2>
	<form id="manage-project-users-copy-form" method="post" action="proj_user_copy.php">
		<fieldset>
			<?php echo form_security_field( 'manage_proj_user_copy' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<select name="other_project_id">
				<?php print_project_option_list( null, false, $f_project_id ); ?>
			</select>
			<span class="action-button">
				<input type="submit" name="copy_from" class="button" value="<?php echo _( 'Copy Users From' ) ?>" />
				<input type="submit" name="copy_to" class="button" value="<?php echo _( 'Copy Users To' ) ?>" />
			</span>
		</fieldset>
	</form>
	<table cellspacing="1" cellpadding="5" border="1">
		<tr class="row-category">
			<th><?php echo _( 'Username' ) ?></th>
			<th><?php echo _( 'E-mail' ) ?></th>
			<th><?php echo _( 'Access Level' ) ?></th>
			<th><?php echo _( 'Actions' ) ?></th>
		</tr><?php
	$t_users = project_get_all_user_rows( $f_project_id, ANYBODY, $f_show_global_users );
	$t_display = array();
	$t_sort = array();
	foreach ( $t_users as $t_user ) {
		$t_user_name = string_attribute( $t_user['username'] );
		$t_sort_name = mb_strtolower( $t_user_name );
		if ( ( isset( $t_user['realname'] ) ) && ( $t_user['realname'] > "" ) && ( access_has_project_level( config_get( 'show_user_realname_threshold', null, null, $f_project_id ), $f_project_id ) ) ) {
			$t_user_name = string_attribute( $t_user['realname'] ) . " (" . $t_user_name . ")";
			if ( ON == config_get( 'sort_by_last_name') ) {
				$t_sort_name_bits = explode( ' ', mb_strtolower( $t_user_name ), 2 );
				$t_sort_name = $t_sort_name_bits[1] . ', ' . $t_sort_name_bits[1];
			} else {
				$t_sort_name = mb_strtolower( $t_user_name );
			}
		}
		$t_display[] = $t_user_name;
		$t_sort[] = $t_sort_name;
	}

	array_multisort( $t_sort, SORT_ASC, SORT_STRING, $t_users, $t_display );


	$t_users_count = count( $t_sort );
	$t_removable_users_exist = false;

	for ( $i = 0; $i < $t_users_count; $i++ ) {
		$t_user = $t_users[$i];
?>
		<tr>
			<td><?php echo $t_display[$i] ?></td>
			<td>
			<?php
				$t_email = user_get_email( $t_user['id'] );
				print_email_link( $t_email, $t_email );
			?>
			</td>
			<td><?php echo get_enum_element( 'access_levels', $t_user['access_level'] ) ?></td>
			<td class="center"><?php
				# You need global or project-specific permissions to remove users
				#  from this project
				if ( $t_can_manage_users && access_has_project_level( $t_user['access_level'], $f_project_id ) ) {
					if ( project_includes_user( $f_project_id, $t_user['id'] )  ) {
						print_button( "proj_user_remove.php?project_id=$f_project_id&user_id=" . $t_user['id'], _( 'Remove' ) );
						$t_removable_users_exist = true;
					}
				} ?>
			</td>
		</tr><?php
	}  # end for ?>
	</table>
	<?php
	# You need global or project-specific permissions to remove users
	#  from this project
	if ( !$f_show_global_users ) {
		print_button( "proj_edit_page.php?project_id=$f_project_id&show_global_users=true", _( 'Show Users with Global Access' ) );
	} else {
		print_button( "proj_edit_page.php?project_id=$f_project_id", _( 'Hide Users with Global Access' ) );
	}

	if ( $t_removable_users_exist ) {
		echo '&#160;';
		print_button( "proj_user_remove.php?project_id=$f_project_id", _( 'Remove all' ) );
	}

# We want to allow people with global permissions and people with high enough
#  permissions on the project we are editing
if ( $t_can_manage_users ) {
	$t_users = user_get_unassigned_by_project_id( $f_project_id );
	if( count( $t_users ) > 0 ) { ?>
	<form id="manage-project-add-user-form" method="post" action="proj_user_add.php">
		<fieldset>
			<legend><span><?php echo _( 'Add user to project' ) ?></span></legend>
			<?php echo form_security_field( 'manage_proj_user_add' ) ?>
			<input type="hidden" name="project_id" value="<?php echo $f_project_id ?>" />
			<div class="field-container">
				<label for="project-add-users-username"><span><?php echo _( 'Username' ) ?></span></label>
				<span class="select">
					<select id="project-add-users-username" name="user_id[]" multiple="multiple" size="10"><?php
						foreach( $t_users AS $t_user_id=>$t_display_name ) {
							echo '<option value="', $t_user_id, '">', $t_display_name, '</option>';
						} ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="project-add-users-access-level"><span><?php echo _( 'Access Level' ) ?></span></label>
				<span class="select">
					<select id="project-add-users-access-level" name="access_level"><?php
						# only access levels that are less than or equal current user access level for current project
						print_project_access_levels_option_list( config_get( 'default_new_account_access_level' ), $f_project_id ); ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<span class="submit-button"><input type="submit" class="button" value="<?php echo _( 'Add User' ) ?>" /></span>
		</fieldset>
	</form>
<?php
	}
}
?>
</div><?php

html_page_bottom();