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
import {useBlockProps} from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import WidgetInspectorControls from "./inspector";
import { useEffect } from '@wordpress/element';

export default function Edit(props) {
	const {
		attributes: {
			filter_options,
		},
		setAttributes
	} = props;

	const blockProps = useBlockProps();

	let specifications = awsmJobsAdmin.awsm_filters_block;
	specifications = specifications.filter(spec => {
		if (
			typeof filter_options !== "undefined" &&
			filter_options.includes(spec.key)
		) {
			return spec;
		}
	});

	// Event handler to ignore clicks
    const handleClick = (event) => {
        event.preventDefault();
        event.stopPropagation();
    };

    const handleResize = () => {            
        const filtersWraps = document.querySelectorAll('.awsm-b-filter-wrap:not(.awsm-no-search-filter-wrap)');
        
        filtersWraps.forEach(wrapper => {
            const filterItems = wrapper.querySelectorAll('.awsm-b-filter-item');
            
            if (filterItems.length > 0) {
                const filterFirstTop = filterItems[0].getBoundingClientRect().top;
                const filterLastTop = filterItems[filterItems.length - 1].getBoundingClientRect().top;
                if(window.innerWidth < 768) {
                    wrapper.classList.remove('awsm-b-full-width-search-filter-wrap');
                    return;
                }
                if (filterLastTop > filterFirstTop) {
                    wrapper.classList.add('awsm-b-full-width-search-filter-wrap');
                } 
            }
        });
    };

    const checkElement = () => {
        const dynamicElement = document.querySelector('.awsm-b-job-wrap');
        if (dynamicElement && handleResize) {
            handleResize();
        } else {
            // Retry after a short delay until the element is found
            setTimeout(checkElement, 300);
        }
    };

    useEffect(() => {handleResize(); checkElement()}, [props.attributes.enable_job_filter, props.attributes.filter_options]);
	
    useEffect(() => {
        // document.addEventListener('DOMContentLoaded', () => {
          
        
            // Start checking for the element
            checkElement();
        // });

        // Define the handler function
       

        // Call the handler initially
        // handleResize();
        
        // Add resize event listener
        window.addEventListener('resize', handleResize);

        // Cleanup function to remove the event listener
        return () => {
            window.removeEventListener('resize', handleResize);
        };
    }, []); // Empty dependency array means this effect runs only once
	
	return (
		<div {...blockProps} onClick={handleClick}>
			<WidgetInspectorControls {...props} />
			<ServerSideRender
				block="wp-job-openings/blocks"
				attributes={props.attributes}
			/>
		</div>
	);
}
