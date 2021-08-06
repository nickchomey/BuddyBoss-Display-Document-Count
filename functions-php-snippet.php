function bp_nouveau_nav_has_count_override() {
	$bp_nouveau = bp_nouveau();
	$nav_item   = $bp_nouveau->current_nav_item;
	$count      = false;

	if ( 'directory' === $bp_nouveau->displayed_nav && isset( $nav_item->count ) ) {
		$count = $nav_item->count;
  } elseif ( 'groups' === $bp_nouveau->displayed_nav && 'members' === $nav_item->slug ) {
		$count = 0 !== (int) groups_get_current_group()->total_member_count;
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_media_support_enabled() && 'photos' === $nav_item->slug ) {
		$count = 0 !== (int) bp_media_get_total_group_media_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_video_support_enabled() && 'videos' === $nav_item->slug ) {
		$count = 0 !== (int) bp_video_get_total_group_video_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_albums_support_enabled() && 'albums' === $nav_item->slug ) {
		$count = 0 !== (int) bp_media_get_total_group_album_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_document_support_enabled() && 'documents' === $nav_item->slug ) {
		$count = 0 !== (int) bp_document_get_total_group_document_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && 'subgroups' === $nav_item->slug ) {
		$count = 0 !== (int) count( bp_get_descendent_groups( bp_get_current_group_id(), bp_loggedin_user_id() ) );
	} elseif ( 'personal' === $bp_nouveau->displayed_nav && ! empty( $nav_item->primary ) ) {
		$count = (bool) strpos( $nav_item->name, '="count"' );
	}

	/**
	 * Filter to edit whether the nav has a count attribute.
	 *
	 * @since BuddyPress 3.0.0
	 *
	 * @param bool   $value     True if the nav has a count attribute. False otherwise
	 * @param object $nav_item  The current nav item object.
	 * @param string $value     The current nav in use (eg: 'directory', 'groups', 'personal', etc..).
	 */
	return (bool) apply_filters( 'bp_nouveau_nav_has_count_override', false !== $count, $nav_item, $bp_nouveau->displayed_nav );
}

/**
 * Displays the nav item count attribute.
 *
 * @since BuddyPress 3.0.0
 */
function bp_nouveau_nav_count_override() {
	echo esc_html( number_format_i18n( bp_nouveau_get_nav_count_override() ) );
}

	/**
	 * Retrieve the count attribute for the current nav item.
	 *
	 * @since BuddyPress 3.0.0
	 *
	 * @return int The count attribute for the nav item.
	 */
function bp_nouveau_get_nav_count_override() {
	$bp_nouveau = bp_nouveau();
	$nav_item   = $bp_nouveau->current_nav_item;
	$count      = 0;

	if ( 'directory' === $bp_nouveau->displayed_nav ) {
		$count = (int) str_replace( ',', '', $nav_item->count );
  } elseif ( 'groups' === $bp_nouveau->displayed_nav && ( 'members' === $nav_item->slug || 'all-members' === $nav_item->slug ) ) {
		$count = (int) groups_get_current_group()->total_member_count;
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && 'subgroups' === $nav_item->slug ) {
		$count = count( bp_get_descendent_groups( bp_get_current_group_id(), bp_loggedin_user_id() ) );
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_document_support_enabled() && 'documents' === $nav_item->slug ) {
		$count = bp_document_get_total_group_document_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_media_support_enabled() && 'photos' === $nav_item->slug ) {
		$count = bp_media_get_total_group_media_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'media' ) && bp_is_group_albums_support_enabled() && 'albums' === $nav_item->slug ) {
		$count = bp_media_get_total_group_album_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && bp_is_active( 'video' ) && bp_is_group_video_support_enabled() && 'videos' === $nav_item->slug ) {
		$count = bp_video_get_total_group_video_count();
	} elseif ( 'groups' === $bp_nouveau->displayed_nav && 'leaders' === $nav_item->slug ) {
		$group  = groups_get_current_group();
		$admins = groups_get_group_admins( $group->id );
		$mods   = groups_get_group_mods( $group->id );
		$count  = sizeof( $admins ) + sizeof( $mods );		
  
    // @todo imho BuddyPress shouldn't add html tags inside Nav attributes...  
	} elseif ( 'personal' === $bp_nouveau->displayed_nav && ! empty( $nav_item->primary ) ) {
		$span = strpos( $nav_item->name, '<span' );

		// Grab count out of the <span> element.
		if ( false !== $span ) {
			$count_start = strpos( $nav_item->name, '>', $span ) + 1;
			$count_end   = strpos( $nav_item->name, '<', $count_start );
			$count       = (int) substr( $nav_item->name, $count_start, $count_end - $count_start );
		}
		if( bp_is_active( 'media' ) ) {
			$videos_count = BP_Video::total_video_count( bp_displayed_user_id() );
			if( $nav_item->slug == 'videos' ) {
				$count = $videos_count;
			}
		}
	}

	/**
	 * Filter to edit the count attribute for the nav item.
	 *
	 * @since BuddyPress 3.0.0
	 *
	 * @param int $count    The count attribute for the nav item.
	 * @param object $nav_item The current nav item object.
	 * @param string $value    The current nav in use (eg: 'directory', 'groups', 'personal', etc..).
	 */
	return (int) apply_filters( 'bp_nouveau_get_nav_count_override', $count, $nav_item, $bp_nouveau->displayed_nav );
}
