<?php
/*
Plugin Name: O3World Members-Only Categories
Plugin URI: http://wordpress.org/extend/plugins/o3world-members-only-categories/
Description: Designate certain categories as "members-only" via 'Privacy Settings.' An administrator may then assign a user to them via 'Profile.' Only content belonging to categories assigned to the logged-in user (if applicable), and the public ones, will be shown.
Version: 1.03
Author: Kris Gale (kris@o3world.com)
Author URI: http://o3world.com
License: GPLv2
*/


/*-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --*/


function o3_moc_get_categories( $all_top_cats = false ) {

	$top_cats = array( );

	$members_only = array( );
	if( !$all_top_cats ) {
		$option_val = get_option( 'o3_moc_cats' );
		$members_only = ( $option_val != "" ? explode( ",", $option_val ) : array( ) );
	}

	foreach( get_categories( array( 'parent' => 0, 'hide_empty' => false ) ) as $top_cat ) {
		if( $all_top_cats || in_array( $top_cat->term_id, $members_only ) ) {
			$top_cats[ ] = $top_cat->term_id;
		}
	}

	return $top_cats;

}


function o3_moc_edit_categories( ) {

	/*-- this space left intentionally blank --*/

}


function o3_moc_category_input( $cat_args ) {

	$cat_key = ( "o3_moc_cat_" . $cat_args[ 'id' ] );
	$cat_checked = ( $cat_args[ 'active' ] ? ' checked="checked"' : '' );
	if( $cat_args[ 'first' ] ) {
		echo '<input type="hidden" name="o3_moc_cats" id="o3_moc_cats" value="1" />';
	}
	echo '<input type="checkbox" name="' . $cat_key . '" id="' . $cat_key . '" value="1"' . $cat_checked . ' />';

}


function o3_moc_update_categories( ) {

	$members_only = array( );

	foreach( o3_moc_get_categories( true ) as $top_cat_id ) {
		if( array_key_exists( "o3_moc_cat_" . $top_cat_id, $_POST ) ) {
			$members_only[ ] = $top_cat_id;
		}
	}

	$option_key = "o3_moc_cats";
	$option_val = ( sizeof( $members_only ) != 0 ? implode( ",", $members_only ) : "" );

	return $option_val;

}


function o3_moc_admin_init( ) {

	register_setting(
		'privacy',
		'o3_moc_cats',
		'o3_moc_update_categories'
	);

	add_settings_section(
		'o3_moc_cats',
		"Members-Only Categories",
		'o3_moc_edit_categories',
		'privacy'
	);

	$members_only = o3_moc_get_categories( );

	$cat_first = true;
	foreach( o3_moc_get_categories( true ) as $top_cat_id ) {
		add_settings_field(
			( 'o3_moc_cat_' . $top_cat_id ),
			get_cat_name( $top_cat_id ),
			'o3_moc_category_input',
			'privacy',
			'o3_moc_cats',
			array(
				'first' => $cat_first,
				'id' => $top_cat_id,
				'active' => in_array( $top_cat_id, $members_only )
			)
		);
		$cat_first = false;
	}

}


function o3_moc_get_user_categories( $user_id ) {

	$user_meta_val = get_user_meta( $user_id, "o3_moc_cats", true );

	return ( $user_meta_val != "" ? explode( ",", $user_meta_val ) : array( ) );

}


function o3_moc_edit_user( $user ) {

	if( current_user_can( 'edit_users' ) ) {

		$members_only = o3_moc_get_categories( );

		if( sizeof( $members_only ) != 0 ) {

			$user_categories = o3_moc_get_user_categories( $user->ID );

			echo '
				<h3>Members-Only Categories</h3>
				<table class="form-table">
			';
			foreach( $members_only as $user_cat_id ) {
				$cat_key = ( "o3_moc_cat_" . $user_cat_id );
				$cat_checked = ( in_array( $user_cat_id, $user_categories ) ? ' checked="checked"' : '' );
				echo '
					<tr>
						<th>' . get_cat_name( $user_cat_id ) . '</th>
						<td><input type="checkbox" name="' . $cat_key . '" id="' . $cat_key . '" value="1"' . $cat_checked . ' /></td>
					</tr>
				';
			}
			echo '
				</table>
			';

		}

	}

}


function o3_moc_update_user( $user_id ) {

	if( current_user_can( 'edit_users' ) ) {

		$user_categories = array( );

		foreach( o3_moc_get_categories( ) as $user_cat_id ) {
			if( array_key_exists( "o3_moc_cat_" . $user_cat_id, $_POST ) ) {
				$user_categories[ ] = $user_cat_id;
			}
		}

		$user_meta_key = "o3_moc_cats";
		$user_meta_val = ( sizeof( $user_categories ) != 0 ? implode( ",", $user_categories ) : "" );

		if( $user_meta_val != "" ) {
			update_user_meta( $user_id, $user_meta_key, $user_meta_val );
		} else {
			delete_user_meta( $user_id, $user_meta_key );
		}

	}

}


function o3_moc_set_query( $query ) {

	global $current_user;

	if( array_key_exists( "suppress_filters", $query->query_vars ) ) {
		if( $query->query_vars[ 'suppress_filters' ] ) {
			return $query;
		}
	}

	if( !is_admin( ) ) {

		$members_only = o3_moc_get_categories( );
		$user_cats = o3_moc_get_user_categories( $current_user->ID );

		$top_cats = array( );

		foreach( o3_moc_get_categories( true ) as $top_cat_id ) {
			if( !in_array( $top_cat_id, $members_only ) || in_array( $top_cat_id, $user_cats ) ) {
				$top_cats[ ] = $top_cat_id;
			}
		}

		$include_cats = array( );

		foreach( get_all_category_ids( ) as $cat_id ) {
			if( in_array( $cat_id, $top_cats ) ) {
				$include_cats[ ] = $cat_id;
			} else {
				foreach( $top_cats as $top_cat_id ) {
					if( cat_is_ancestor_of( $top_cat_id, $cat_id ) ) {
						if( !in_array( $cat_id, $include_cats ) ) {
							$include_cats[ ] = $cat_id;
						}
					}
				}
			}
		}

		if( sizeof( $include_cats ) != 0 ) {
			$query->set( 'category__in', $include_cats );
		}

	}

}


/*-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --*/


function o3_moc_uninstall( ) {

	delete_option( 'o3_moc_cats' );

	foreach( get_users( ) as $user ) {
		delete_user_meta( $user->ID, 'o3_moc_cats' );
	}

}


/*-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --*/


add_action( 'admin_init', 'o3_moc_admin_init' );

add_action( 'show_user_profile', 'o3_moc_edit_user' );
add_action( 'edit_user_profile', 'o3_moc_edit_user' );

add_action( 'personal_options_update', 'o3_moc_update_user' );
add_action( 'edit_user_profile_update', 'o3_moc_update_user' );

add_action( 'pre_get_posts', 'o3_moc_set_query' );


/*-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% --*/


register_uninstall_hook( __FILE__, 'o3_moc_uninstall' );


?>
