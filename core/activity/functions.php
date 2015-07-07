<?php


/**
 * Get the attached gallery id for an activity
 * 
 * @param type $activity_id
 * @return type
 * not used
 */
function mpp_activity_get_attached_gallery_id( $activity_id ) {
    
    return bp_activity_get_meta( $activity_id, '_mpp_attached_gallery_id', true );
}
/**
 * Updated attached gallery id for an activity
 * 
 * @param type $activity_id
 * @param type $gallery_id
 * @return type
 * 
 * Not Used
 */
function mpp_activity_update_attached_gallery_id( $activity_id, $gallery_id ) {
    
    return  bp_activity_update_meta( $activity_id, '_mpp_attached_gallery_id', $gallery_id );
    
}
/**
 * delete attached gallery
 * 
 * Not used
 */
function mpp_activity_delete_attached_gallery_id( $activity_id ) {
    
    return  bp_activity_delete_meta( $activity_id, '_mpp_attached_gallery_id' );
    
}

/**
 * Get attached media Ids or an acitivyt 
 * @param type $activity_id
 * @return array of media ids
 */
function mpp_activity_get_attached_media_ids( $activity_id ) {
    
    return bp_activity_get_meta( $activity_id, '_mpp_attached_media_ids', false );
    
}
/**
 * Update Attached list of media ids for an activity
 * 
 * @param type $activity_id
 * @param type $media_ids
 * @return type
 */
function mpp_activity_update_attached_media_ids( $activity_id, $media_ids ) {
	
   foreach( $media_ids as $media_id )
		bp_activity_add_meta( $activity_id, '_mpp_attached_media_ids', $media_id );
   
   return $media_ids;

}
/**
 * Delete Attached list of media ids for an activity
 * 
 */
function mpp_activity_delete_attached_media_ids( $activity_id ) {
   
    return bp_activity_delete_meta( $activity_id, '_mpp_attached_media_ids' );

}


/**
 * Get the activity Id for media/Gallery
 */
/**
 * Check if activity has associated media
 * 
 * @param int $activity_id
 * @return mixed false if no attachment else array of attachment ids
 */
function mpp_activity_has_media( $activity_id = false ) {
    
    if( ! $activity_id )
        $activity_id = bp_get_activity_id ();
    
    return mpp_activity_get_attached_media_ids( $activity_id );
}




function mpp_activity_get_associated_comment_id( $activity_id ) {
    
    return bp_activity_get_meta( $activity_id, '_mpp_comment_id',  true );
    
}

function mpp_activity_update_associated_comment_id( $activity_id, $value ) {
    
    return bp_activity_update_meta( $activity_id, '_mpp_comment_id', $value );
    
}

function mpp_activity_delete_associated_comment_id( $activity_id ){
    
    return bp_activity_delete_meta( $activity_id, '_mpp_comment_id' );
    
}

/***
 * For single Media comment
 */
/**
 * Get the id of the gallery associated with this activity
 * _mpp_gallery_id meta key is added for activity uploads as well as single gallery activity/comment
 * 
 * If it is a single gallery activity(comments on single gallery page), there won't exist the meta _mpp_media_id
 * 
 * This meta is added to activity when an activity has uploads from activity page or a comment is made on the single gallery page(not the single media).
 * The only way to differentiate these two types of activity is to check for the presence of the _mpp_attached_media_ids meta
 * 
 *  If a new activity is created by posting on single media page(comments), It does not have _mpp_gallery_id associated with it
 * 
 * @param type $activity_id
 * @return int gallery id
 */
function mpp_activity_get_gallery_id( $activity_id ) {
	
	return bp_activity_get_meta( $activity_id, '_mpp_gallery_id', true );
}
/**
 * Update the gallery id associated with this activity
 * 
 * @param int $activity_id
 * @param int $gallery_id
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function mpp_activity_update_gallery_id( $activity_id, $gallery_id ) {
	
	return bp_activity_update_meta( $activity_id, '_mpp_gallery_id', $gallery_id );
}
/**
 * Deete gallery id associated with this activity
 * 
 * @param int $activity_id
 */
function mpp_activity_delete_gallery_id( $activity_id ) {
	
	bp_activity_delete_meta( $activity_id, '_mpp_gallery_id' );
}
/**
 * Get the id of the media associated with this activity
 * It is used to differentiate single media activity from the activity upload
 * for activity uploaded media please see _mpp_attached_media_ids
 * 
 * 
 * Please note, we do not consider activity uploads as media activity(We consider activity uploads as gallery activity instead), see _mpp_attached_media_ids for the same
 * 
 * It is for single media activity comment
 * 
 * @param type $activity_id
 * @return type
 */
function mpp_activity_get_media_id( $activity_id ) {
	
	return bp_activity_get_meta( $activity_id, '_mpp_media_id', true );
}

function mpp_activity_update_media_id( $activity_id, $media_id ) {
	
	return bp_activity_update_meta( $activity_id, '_mpp_media_id', $media_id );
}

function mpp_activity_delete_media_id( $activity_id ) {
	
	return bp_activity_delete_meta( $activity_id, '_mpp_media_id' );
}

/**
 * When an activity is saved, check if there exists a media attachment cookie,
 *  if yes, mark it as non orphaned and store in the activity meta
 * 
 */

function mpp_activity_mark_attached_media( $activity_id  ) {
    
    if( ! is_user_logged_in() )
        return ;
    
    if( empty( $_COOKIE['_mpp_activity_attached_media_ids'] ) )
        return ;//don't do anything
    
    //let us process
    $media_ids = $_COOKIE['_mpp_activity_attached_media_ids'];
    $media_ids = explode( ',',  $media_ids ); //make an array
    
    foreach( $media_ids as $media_id ) {
        //should we verify the logged in user & owner of media is same?
        
        mpp_delete_media_meta( $media_id, '_mpp_is_orphan');//or should we delete the key?
     
    }
    mpp_activity_update_attached_media_ids( $activity_id, $media_ids );
     
    //store the media ids in the activity meta
   
    //also add the activity to gallery & gallery to activity link
    $media = mpp_get_media( $media_id );
    
    if( $media->gallery_id ) {
        
        mpp_activity_update_gallery_id( $activity_id, $media->gallery_id );
        
    }
    
    //also update this activity and set its action to be mpp_media_upload
    $activity = new BP_Activity_Activity( $activity_id );
   // $activity->component = buddypress()->mediapress->id;
    $activity->type = 'mpp_media_upload';
    $activity->save();
    
    mpp_activity_clear_attached_media_cookie();//clear cookies
    //reset the cookie
}


/**
 * Record MediaPress activity
 * We piggyback on others
 * 
 * 
 * @param type $media_ids
 * @todo remove in future as we don't need it 
 */
function mpp_record_activity( $args = array() ) {
	
	$default = array(
		'media_ids'		=> false,
		'gallery_id'	=> 0,
		'media_id'		=> 0,
		'content'		=> '',
		'component'		=> '',
		'component_id'	=> 0,
	);

	$args = wp_parse_args( $args, $default );
	
	$media_ids = $args['media_ids'];
	
	if( empty( $media_ids ) ) {
		$media_ids = array();
	}	
	
	if( ! empty( $args['media_id'] ) ) {
		
		array_push ( $media_ids, $args['media_id'] );
	}
	
	$args['media_ids'] = $media_ids;
	
	$activity_id = _mpp_record_activity( $args );
	
	//unable to save or anything
	if( ! $activity_id )
		return false;
	
	if( $args['gallery_id'] ) {
		
		$gallery = mpp_get_gallery( absint( $args['gallery_id'] ) );
		
		$component		= $gallery->component;
		$component_id	= $gallery->component_id;
		
	} else {
		
		if( empty( $media_ids ) )
			return;
		
		$media_id = $media_ids[0];
		
		$media = mpp_get_media( $media_id );
		
		$component = $media->component;
		
		$component_id = $media->component_id;
		
		$gallery = mpp_get_gallery( $media->gallery_id );
		
	}
	
	if( empty( $gallery ) )
		return false;
	
	$activity = new BP_Activity_Activity( $activity_id );
	
	$hide_sitewide = $activity->hide_sitewide;
	
	$status = mpp_get_gallery_status( $gallery );
	
	//for non public galleries
	if( $status != 'public' )
		$hide_sitewide = 1;
	
	$activity->type = 'mpp_media_upload';
	$activity->hide_sitewide = $hide_sitewide;
	//save
    $activity->save();
	
	if( ! empty( $media_ids ) ) {
		mpp_activity_update_attached_media_ids( $activity_id, $media_ids );
	}
	mpp_activity_update_gallery_id( $activity_id, $gallery->id );
	
	return true;
	
}

/**
 * Record Media Activity
 * 
 * It does not actually records activity, simply simulates the activity update and rest are done by the actions.php functions
 * 
 * It will be removed in future for a better record_activity method
 * @param type $args
 * @return boolean
 */
function _mpp_record_activity( $args = null ) {
	
	//if activity module is not active, why bother
	if( ! bp_is_active( 'activity' ) ) {
		return false; 
	}
	
	$default = array(
		'gallery_id'	=> 0,
		'media_ids'		=> '',
		'component'		=> mpp_get_current_component(),
		'component_id'	=> mpp_get_current_component_id(),
		'user_id'		=> get_current_user_id(),
		'action'		=> '',
		'content'		=> '',
	);
	
	$args = wp_parse_args( $args, $default );
	
	//if media ids are not provided or component is not given or user id is not given, we will not record the activity
	if( empty( $args['media_ids'] ) || empty( $args['component'] ) || empty( $args['user_id'] ) || empty( $args['component_id'] ) ) {
	
		return false;
	}	
	
	$media_ids = $args['media_ids'];
	
	//sanitize ids
	$media_ids = wp_parse_id_list( $media_ids );
	
	$media_count = count( $media_ids );
	//get the first media
	$media = mpp_get_media( $media_ids[0] );
	
	$type = $media->type;
	//we need the type plural in case of mult
	$type = _n( $type, $type . 's', $media_count );//photo vs photos etc
	
	if( empty( $args['content'] ) ) {
		
		$content = sprintf( __( 'Added %s', 'mediapress' ), $type );
		
	} else {
		
		$content = $args['content'];
	}	
	//here is the way to shortcircuit the things
	//fake media ids in cookie
	//$_COOKIE['_mpp_activity_attached_media_ids'] = join( ',', $media_ids );//it is expected as comma separated
	
	
	if( $args['component'] == 'groups' && bp_is_active( 'groups') ) {
		
		
		$activity_id = groups_post_update( array(
			'group_id'	=> $args['component_id'],
			'content'	=> $content,
			'user_id'	=> $args['user_id']
		) );
		
	} else {
	
		$activity_id = bp_activity_post_update( array(
			'user_id'	=> $args['user_id'],
			'content'	=> $content,
		) );
		
		//simply post to activity stream
	}
	
	return $activity_id;
}