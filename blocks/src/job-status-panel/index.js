/**
 * Job Status sidebar panel — renders the same server-side markup as the
 * classic "Job Status" metabox (see admin/templates/meta/job-status.php),
 * fetched over admin-ajax so it keeps running through the
 * awsm_job_status_mb_init / awsm_job_status_mb_data_rows hooks that Pro Pack
 * and other installed add-ons already depend on, instead of being
 * re-implemented client-side.
 *
 * wp.plugins / wp.editPost / wp.data are referenced as runtime globals
 * (provided by WordPress core) rather than imported, since those packages
 * aren't installed as local devDependencies in this build.
 */
import { RawHTML, useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

function fetchStatusHtml( postId ) {
	const body = new window.URLSearchParams();
	body.append( 'action', 'awsm_job_status_panel' );
	body.append( 'nonce', window.awsmJobStatusPanel.nonce );
	body.append( 'post_id', postId );

	return window
		.fetch( window.awsmJobStatusPanel.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body,
		} )
		.then( ( response ) => response.json() );
}

function JobStatusPanel() {
	const { useSelect } = wp.data;
	const postId = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostId(), [] );
	const isSavingPost = useSelect(
		( select ) =>
			select( 'core/editor' ).isSavingPost() && ! select( 'core/editor' ).isAutosavingPost(),
		[]
	);

	const [ html, setHtml ] = useState( null );
	const wasSavingRef = useRef( isSavingPost );

	const loadStatus = ( id ) => {
		if ( ! id ) {
			return;
		}
		fetchStatusHtml( id ).then( ( response ) => {
			if ( response && response.success ) {
				setHtml( response.data.html );
			}
		} );
	};

	useEffect( () => {
		loadStatus( postId );
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ postId ] );

	useEffect( () => {
		if ( wasSavingRef.current && ! isSavingPost ) {
			loadStatus( postId );
		}
		wasSavingRef.current = isSavingPost;
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ isSavingPost ] );

	return (
		<wp.editPost.PluginDocumentSettingPanel
			name="awsm-job-status"
			title={ __( 'Job Status', 'wp-job-openings' ) }
			className="awsm-job-status-panel"
		>
			{ html === null ? __( 'Loading…', 'wp-job-openings' ) : <RawHTML>{ html }</RawHTML> }
		</wp.editPost.PluginDocumentSettingPanel>
	);
}

wp.plugins.registerPlugin( 'awsm-job-status-panel', {
	render: JobStatusPanel,
	icon: null,
} );
