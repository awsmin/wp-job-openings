import { createBlock } from '@wordpress/blocks';

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
			console.log('Deprecated save() called');
			return null;
		},

		// Migration for existing blocks
		migrate: ( attributes ) => {
			console.log('DEPRECATED MIGRATE ENTERED', attributes);

			const placement =
				typeof attributes.placement === 'undefined' || attributes.placement === null
					? 'top'
					: attributes.placement;

			const migratedAttributes = {
				filter_options: attributes.filter_options || [],
				other_options: attributes.other_options || [],
				number_of_columns: attributes.number_of_columns || 3,
				pagination: attributes.pagination || 'modern',
				hide_expired_jobs: attributes.hide_expired_jobs || false,
				search_placeholder: attributes.search_placeholder || '',

				layout: attributes.layout === 'list' ? 'stack' : attributes.layout,

				search: attributes.search !== undefined ? attributes.search : true,

				list_type: attributes.list_type || 'all',
				order_by: attributes.order_by || 'new_to_old',

				placement,

				selected_terms_main: attributes.selected_terms_main || [],
				selected_terms: attributes.selected_terms || {},
				filtersInitialized: attributes.filtersInitialized || false,
				specsInitialized: attributes.specsInitialized || false,
			};

			console.log('MIGRATED ATTRIBUTES', migratedAttributes);

			return migratedAttributes;
		},

		// Transform old blocks into the new schema (copy/paste, recovery, etc.)
		transforms: {
			from: [
				{
					type: 'block',
					blocks: [ 'wp-job-openings/blocks' ],
					transform: ( attributes ) => {
						console.log('DEPRECATED TRANSFORM ENTERED', attributes);

						return createBlock('wp-job-openings/blocks', {
							...attributes,
							listing_per_page: attributes.listing_per_page || 10,
							layout: attributes.layout === 'list' ? 'stack' : attributes.layout,
							placement: attributes.placement || 'top',
							list_type: 'all',
							order_by: 'new',
							selected_terms_main: [],
							selected_terms: {},
							filtersInitialized: false,
							specsInitialized: false,
						});
					},
				},
			],
		},
	},
];
