import { createBlock } from '@wordpress/blocks';
console.log('entered deprecated');
export default [
	{
		attributes: {
			filter_options: { type: 'array', default: [] },
			select_filter_full: { type: 'boolean', default: false },
			other_options: { type: 'array', default: [] },
			layout: { type: 'string', default: 'list' },
			listing_per_page: { type: 'number', default: 10 },
			number_of_columns: { type: 'number', default: 3 },
			pagination: { type: 'string', default: 'modern' },
			hide_expired_jobs: { type: 'boolean', default: false },
			search: { type: 'boolean', default: false },
			search_placeholder: { type: 'string', default: '' },
			enable_job_filter: { type: 'boolean', default: true },
		},

		save() {
			return null;
		},

		// Migration for existing blocks
		migrate: ( attributes ) => {
			const placement = ( typeof attributes.placement === 'undefined' || attributes.placement === null )
				? 'top'
				: attributes.placement;
console.log('migrating');
				
			return {
				// direct carry over / safe defaults
				filter_options: attributes.filter_options || [],
				other_options: attributes.other_options || [],
				number_of_columns: attributes.number_of_columns || 3,
				pagination: attributes.pagination || 'modern',
				hide_expired_jobs: attributes.hide_expired_jobs || false,
				search_placeholder: attributes.search_placeholder || '',

				// renamed attributes
				jobsPerPage: attributes.listing_per_page || 5,

				// layout migration
				layout: attributes.layout === 'list' ? 'stack' : attributes.layout,

				// keep search toggle
				search: attributes.search !== undefined ? attributes.search : true,

				// defaults for new ones
				listType: attributes.listType || 'all',
				orderBy: attributes.orderBy || 'new',

				// Important: preserve placement if present; otherwise set to top for migrated blocks
				placement,

				selected_terms_main: attributes.selected_terms_main || [],
				selectedTerms: attributes.selectedTerms || {},
				filtersInitialized: attributes.filtersInitialized || false,
				specsInitialized: attributes.specsInitialized || false,
			};
		},


		// Transform old blocks into the new schema (extra safety for copy/paste, import, etc.)
		transforms: {
			from: [
				{
					type: 'block',
					blocks: ['wp-job-openings/blocks'],
					transform: (attributes) => {
						return createBlock('wp-job-openings/blocks', {
							...attributes,
							jobsPerPage: attributes.listing_per_page || 5,
							layout: attributes.layout === 'list' ? 'stack' : attributes.layout,
							placement: attributes.placement ? attributes.placement : 'top',
							listType: 'all',
							orderBy: 'new',
							selected_terms_main: [],
							selectedTerms: {},
							filtersInitialized: false,
							specsInitialized: false,
						});
					},
				},
			],
		},
	},
];
