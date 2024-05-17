/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import {__} from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
// import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
import {useEffect} from "@wordpress/element";
import {InnerBlocks, useBlockProps} from "@wordpress/block-editor";

import WidgetInspectorControls from "./inspector";
import {useSelect} from "@wordpress/data";
export default function Edit(props) {
	const {
		attributes: { filter_options, layout, search, enable_job_filter,search_placeholder},
		setAttributes,
	} = props;
	const blockProps = useBlockProps();

	let specifications = awsmJobsAdmin.awsm_filters;
	specifications = specifications.filter(spec => {
		if (
			typeof filter_options !== "undefined" &&
			filter_options.includes(spec.key)
		) {
			return spec;
		}
	});

	const posts = useSelect(select => {
		return select("core").getEntityRecords("postType", "awsm_job_openings", {
			per_page: 5
		});
	}, []);

	const awsmDropDown = $elem => {
		if (
			"selectric" in awsmJobsPublic.vendors &&
			awsmJobsPublic.vendors.selectric
		) {
			$elem.selectric({
				onInit: function(select, selectric) {
					var id = select.id;
					var $input = jQuery(selectric.elements.input);
					jQuery(select).attr("id", "selectric-" + id);
					$input.attr("id", id);
				},
				arrowButtonMarkup:
					'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
				customClass: {
					prefix: "awsm-selectric",
					camelCase: false
				}
			});
		}
	};

	useEffect(() => {
		awsmDropDown(jQuery(".awsm-job-select-control"));
	});

	return (
		<div {...blockProps}>
			<WidgetInspectorControls {...props} />
			<div className="awsm-job-wrap">
			
				{specifications.length > 0 && (
					<div className="awsm-filter-wrap">
						<div className="awsm-filter-items">
							{specifications.map(spec => {
								const dropDown = (
									<div className="awsm-filter-item awsm-jobs-block-specs-group-in">
										<select
											name={`awsm_job_alerts_spec[${spec.key}]`}
											className="awsm-job-select-control"
											id="awsm_job_alerts_specs"
											multiple
										>
											<option value="">{spec.label}</option>
											{spec.terms.map(term => {
												return (
													<option value={term.term_id}>{term.name}</option>
												);
											})}
										</select>
									</div>
								);
								return dropDown;
							})}
						</div>
					</div>
				)}

            { (search && search == true) && [
			<div class="awsm-filter-item-search"><div class="awsm-filter-item-search-in"><label for="awsm-jq-1" class="awsm-sr-only">Search</label><input type="text" id="awsm-jq-1" name="jq" value="" placeholder={ search_placeholder } class="awsm-job-search awsm-job-form-control"/><span class="awsm-job-search-btn awsm-job-search-icon-wrapper"><i class="awsm-job-icon-search"></i></span><span class="awsm-job-search-close-btn awsm-job-search-icon-wrapper awsm-job-hide"><i class="awsm-job-icon-close-circle"></i></span></div></div>
			]}

				<div
					className={`awsm-job-listings ${
						layout === "list" ? "awsm-lists" : "awsm-row"
					}`}
				>
					{posts?.map(post => (
						<div
							key={`awsm-${layout}-item-${post.id}`}
							className={`awsm-job-listing-item awsm-${layout}-item`}
						>
							{layout === "list" ? (
								<div className="awsm-job-item">
									<div className={`awsm-${layout}-left-col`}>
										<h2 className="awsm-job-post-title">
											<a href={post.link}>{post.title.rendered}</a>
										</h2>
									</div>
									<div className={`awsm-${layout}-right-col`}>
										<div className="awsm-job-specification-wrapper">
											<div className="awsm-job-specification-item awsm-job-specification-job-location">
												<span className="awsm-job-specification-term">
													London
												</span>
											</div>
											<div className="awsm-job-specification-item awsm-job-specification-job-category">
												<span className="awsm-job-specification-term">
													Designer
												</span>
											</div>
										</div>
										<div className="awsm-job-more-container">
											<a
												className="awsm-job-more"
												href="http://localhost/awsm/jobs/rr/"
											>
												More Details <span></span>
											</a>
										</div>
									</div>
								</div>
							) : (
								<a href={post.link} className="awsm-job-item">
									<div className={`awsm-${layout}-left-col`}>
										<h2 className="awsm-job-post-title">
											{post.title.rendered}
										</h2>
									</div>
									<div className={`awsm-${layout}-right-col`}>
										<div className="awsm-job-specification-wrapper">
											<div className="awsm-job-specification-item awsm-job-specification-job-location">
												<span className="awsm-job-specification-term">
													London
												</span>
											</div>
											<div className="awsm-job-specification-item awsm-job-specification-job-category">
												<span className="awsm-job-specification-term">
													Designer
												</span>
											</div>
										</div>
										<div className="awsm-job-more-container">
											<span className="awsm-job-more">
												More Details <span></span>
											</span>
										</div>
									</div>
								</a>
							)}
						</div>
					))}
				</div>
			</div>
		</div>
	);
}
