/**
 * Job Expiry sidebar panel — mirrors WordPress core's native "Publish" date
 * picker, backed by the awsm_set_exp_list / awsm_job_expiry /
 * awsm_exp_list_display post meta (registered with show_in_rest so this
 * panel's edits are saved through the same REST request as the rest of the
 * post, instead of a separate mechanism).
 *
 * wp.plugins / wp.editPost / wp.coreData are referenced as runtime globals
 * (provided by WordPress core) rather than imported, since those packages
 * aren't installed as local devDependencies in this build.
 *
 * Timezone handling: the rest of the plugin reads/writes awsm_job_expiry as
 * a plain "Y-m-d H:i:s" wall-clock value with no timezone math (strtotime()
 * treats it as server-local, the same way post_date works). <DateTimePicker>,
 * however, always interprets/returns values relative to the site's REST
 * timezone setting (wp.date.getSettings().timezone) — verified empirically:
 * feeding it a Date object whose *epoch* equals Date.UTC(...) of our target
 * wall-clock numbers displays correctly only when the site offset is 0, and
 * its onChange callback returns a naive ISO string ("2026-07-21T07:45:00",
 * no "Z"/offset) whose digits directly ARE the wall-clock value the user saw
 * and picked. So conversion here is done purely on plain {year, month, day,
 * hour, minute, second} components — never through a browser-local Date
 * object — compensating for the site offset only at the one boundary
 * (`componentsToPickerDate`) where <DateTimePicker> needs it.
 */
import { ToggleControl, Dropdown, Button, DateTimePicker } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { getSettings } from '@wordpress/date';
import { close } from '@wordpress/icons';

import './style.scss';

const POST_TYPE = 'awsm_job_openings';

function pad( n ) {
	return String( n ).padStart( 2, '0' );
}

function parseStoredComponents( value ) {
	if ( ! value ) {
		return null;
	}
	const [ datePart, timePart ] = value.split( ' ' );
	return componentsFromParts( datePart, timePart );
}

function componentsFromNaiveISOString( isoString ) {
	const [ datePart, timePart ] = isoString.split( 'T' );
	return componentsFromParts( datePart, timePart );
}

function componentsFromParts( datePart, timePart ) {
	const [ year, month, day ] = datePart.split( '-' ).map( Number );
	const [ hour, minute, second ] = ( timePart || '00:00:00' ).split( ':' ).map( Number );
	return { year, month, day, hour, minute, second: second || 0 };
}

function nowComponentsAtSiteOffset( siteOffsetHours ) {
	const shifted = new Date( Date.now() + siteOffsetHours * 3600000 );
	return {
		year: shifted.getUTCFullYear(),
		month: shifted.getUTCMonth() + 1,
		day: shifted.getUTCDate(),
		hour: shifted.getUTCHours(),
		minute: shifted.getUTCMinutes(),
		second: shifted.getUTCSeconds(),
	};
}

function componentsToStoredFormat( c ) {
	return `${ c.year }-${ pad( c.month ) }-${ pad( c.day ) } ${ pad( c.hour ) }:${ pad( c.minute ) }:${ pad( c.second ) }`;
}

function componentsToDisplayLabel( c, months ) {
	const hours12 = c.hour % 12 === 0 ? 12 : c.hour % 12;
	const ampm = c.hour < 12 ? 'am' : 'pm';
	return `${ months[ c.month - 1 ] } ${ c.day }, ${ c.year } ${ hours12 }:${ pad( c.minute ) } ${ ampm }`;
}

function componentsToPickerDate( c, siteOffsetHours ) {
	const utcMs = Date.UTC( c.year, c.month - 1, c.day, c.hour, c.minute, c.second );
	return new Date( utcMs - siteOffsetHours * 3600000 );
}

function JobExpiryPanel() {
	const { useEntityProp } = wp.coreData;
	const [ meta, setMeta ] = useEntityProp( 'postType', POST_TYPE, 'meta' );

	if ( ! meta ) {
		return null;
	}

	const settings = getSettings();
	const siteOffsetHours = settings && settings.timezone && typeof settings.timezone.offset === 'number' ? settings.timezone.offset : 0;

	const isExpirySet = meta.awsm_set_exp_list === 'set_listing';
	const displayOnList = meta.awsm_exp_list_display === 'list_display';
	const components = parseStoredComponents( meta.awsm_job_expiry ) || nowComponentsAtSiteOffset( siteOffsetHours );

	const onToggleExpiry = ( checked ) => {
		if ( checked ) {
			setMeta( {
				awsm_set_exp_list: 'set_listing',
				awsm_job_expiry: meta.awsm_job_expiry || componentsToStoredFormat( nowComponentsAtSiteOffset( siteOffsetHours ) ),
			} );
		} else {
			setMeta( {
				awsm_set_exp_list: '',
				awsm_job_expiry: '',
				awsm_exp_list_display: '',
			} );
		}
	};

	const onToggleDisplay = ( checked ) => {
		setMeta( { awsm_exp_list_display: checked ? 'list_display' : '' } );
	};

	const onChangeExpiryDate = ( naiveIsoString ) => {
		setMeta( { awsm_job_expiry: componentsToStoredFormat( componentsFromNaiveISOString( naiveIsoString ) ) } );
	};

	const onClickNow = () => {
		setMeta( { awsm_job_expiry: componentsToStoredFormat( nowComponentsAtSiteOffset( siteOffsetHours ) ) } );
	};

	const pickerDate = componentsToPickerDate( components, siteOffsetHours );

	return (
		<wp.editPost.PluginDocumentSettingPanel
			name="awsm-job-expiry"
			title={ __( 'Job Expiry', 'wp-job-openings' ) }
			className="awsm-job-expiry-panel"
		>
			<ToggleControl
				label={ __( 'Set expiry for listing', 'wp-job-openings' ) }
				checked={ isExpirySet }
				onChange={ onToggleExpiry }
			/>
			{ isExpirySet && (
				<>
					<span className="awsm-job-expiry-date-label">
						{ __( 'Select Expiry Date & Time', 'wp-job-openings' ) }
					</span>
					<Dropdown
						className="awsm-job-expiry-date-dropdown"
						contentClassName="awsm-job-expiry-date-popover"
						position="bottom left"
						renderToggle={ ( { isOpen, onToggle } ) => (
							<Button
								onClick={ onToggle }
								aria-expanded={ isOpen }
								variant="tertiary"
							>
								{ componentsToDisplayLabel( components, settings.l10n.months ) }
							</Button>
						) }
						renderContent={ ( { onClose } ) => (
							<div className="awsm-job-expiry-date-popover-inner">
								<div className="awsm-job-expiry-date-popover-header">
									<span className="awsm-job-expiry-date-popover-title">
										{ __( 'Set Expiry', 'wp-job-openings' ) }
									</span>
									<Button variant="link" onClick={ onClickNow }>
										{ __( 'Now', 'wp-job-openings' ) }
									</Button>
									<Button
										icon={ close }
										label={ __( 'Close', 'wp-job-openings' ) }
										onClick={ onClose }
									/>
								</div>
								<DateTimePicker
									currentDate={ pickerDate }
									onChange={ onChangeExpiryDate }
									is12Hour
									startOfWeek={ settings.l10n.startOfWeek }
								/>
							</div>
						) }
					/>
					<ToggleControl
						label={ __( 'Display expiry date', 'wp-job-openings' ) }
						checked={ displayOnList }
						onChange={ onToggleDisplay }
					/>
				</>
			) }
		</wp.editPost.PluginDocumentSettingPanel>
	);
}

wp.plugins.registerPlugin( 'awsm-job-expiry-panel', {
	render: JobExpiryPanel,
	icon: null,
} );
