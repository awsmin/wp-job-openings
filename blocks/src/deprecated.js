import { createBlock } from '@wordpress/blocks';

export default [
	{
		// 🔑 Force Gutenberg to use this deprecated version
		// Old blocks NEVER had placement
		isEligible( attributes ) {
			return typeof attributes.placement === 'undefined';
		},

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

		// ✅ MIGRATION: old blocks → placement = top
		migrate( attributes ) {
			console.log('🔥 MIGRATING OLD BLOCK → placement = top');

			return {
				// carry over old values
				filter_options: attributes.filter_options || [],
				other_options: attributes.other_options || [],
				number_of_columns: attributes.number_of_columns || 3,
				pagination: attributes.pagination || 'modern',
				hide_expired_jobs: attributes.hide_expired_jobs || false,
				search_placeholder: attributes.search_placeholder || '',

				layout: attributes.layout === 'list' ? 'stack' : attributes.layout,

				search:
					attributes.search !== undefined
						? attributes.search
						: true,

				listing_per_page: attributes.listing_per_page || 10,

				// 🔑 OLD BLOCKS ALWAYS GET TOP
				placement: 'top',

				// new schema defaults
				listType: 'all',
				orderBy: 'new',

				selected_terms_main: [],
				selectedTerms: {},
				filtersInitialized: false,
				specsInitialized: false,
			};
		},

		// Safety net (copy/paste, recovery)
		transforms: {
			from: [
				{
					type: 'block',
					blocks: [ 'wp-job-openings/blocks' ],
					transform( attributes ) {
						return createBlock( 'wp-job-openings/blocks', {
							...attributes,
							layout:
								attributes.layout === 'list'
									? 'stack'
									: attributes.layout,

							placement: 'top',

							listType: 'all',
							orderBy: 'new',
							selected_terms_main: [],
							selectedTerms: {},
							filtersInitialized: false,
							specsInitialized: false,
						} );
					},
				},
			],
		},
	},
];
