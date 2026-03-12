/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

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
import { useBlockProps } from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import WidgetInspectorControls from "./inspector";
import { useEffect, useRef } from "@wordpress/element";

export default function Edit(props) {
    const {
        attributes,
        setAttributes
    } = props;

    const blockProps = useBlockProps();

    const { filter_options } = attributes;

    // In some editor contexts (or during load), the localized object may not be present.
    // Guard to avoid breaking block SSR with a JS error.
    let specifications =
        window.awsmJobsAdmin && Array.isArray(window.awsmJobsAdmin.awsm_filters_block)
            ? window.awsmJobsAdmin.awsm_filters_block
            : [];
	const filterOptionKeys = Array.isArray( filter_options )
		? filter_options
				.map( ( opt ) => ( typeof opt === 'string' ? opt : opt?.specKey ) )
				.filter( Boolean )
		: [];

	specifications = specifications.filter( ( spec ) =>
		typeof filter_options !== 'undefined' && filterOptionKeys.includes( spec.key )
	);

    // Event handler to ignore clicks
    const handleClick = event => {
        event.preventDefault();
        event.stopPropagation();
    };

    const handleResize = () => {
        const filtersWraps = document.querySelectorAll(
            ".awsm-b-filter-wrap:not(.awsm-no-search-filter-wrap)"
        );
        filtersWraps.forEach(wrapper => {
            const filterItems = wrapper.querySelectorAll(".awsm-b-filter-item");
            if (filterItems.length > 0) {
                const filterFirstTop = filterItems[0].getBoundingClientRect().top;
                const filterLastTop = filterItems[
                    filterItems.length - 1
                ].getBoundingClientRect().top;
                if (window.innerWidth < 768) {
                    wrapper.classList.remove("awsm-b-full-width-search-filter-wrap");
                    return;
                }
                if (filterLastTop > filterFirstTop) { 
                    wrapper.classList.add("awsm-b-full-width-search-filter-wrap");
                }
            }
        });
    };

    const checkElement = ( retries = 0 ) => {
        const dynamicElement = document.querySelector(".awsm-b-job-wrap");
        if (dynamicElement) {
            handleResize();
        } else if ( retries < 20 ) {
            setTimeout(() => checkElement( retries + 1 ), 300);
        }
    };

    useEffect(() => {
        window.addEventListener("resize", handleResize);
        checkElement();
        handleResize(); 

        return () => {
            window.removeEventListener("resize", handleResize);
        };
    }, []);

    const checkFilters = () => {
        const wrapper = document.querySelector(
            "#block-" + props.clientId + " .awsm-b-filter-wrap"
        );

        if (!wrapper) {
            return;
        }
        const filterItems = document.querySelectorAll("#block-" + props.clientId + " .awsm-b-filter-item");

        if (filterItems.length > 0) {
            const filterFirstTop = filterItems[0].getBoundingClientRect().top;
            const filterLastTop = filterItems[
                filterItems.length - 1
            ].getBoundingClientRect().top;
            if (window.innerWidth < 768) {
                wrapper.classList.remove("awsm-b-full-width-search-filter-wrap");
                return;
            }
            if (filterLastTop > filterFirstTop) { 
                wrapper.classList.add("awsm-b-full-width-search-filter-wrap");
            }
        }
    };

    useEffect(() => {
        const observer = new MutationObserver(() => {
            checkFilters();
        });

        const observeItem = document.querySelector("#block-" + props.clientId);
       
        if(observeItem) {
            observer.observe( observeItem, { childList: true, subtree: true });
        }

        return () => {
            observer.disconnect();
        };
    }, []);

    return (
        <div {...blockProps} onClick={handleClick}>
            <WidgetInspectorControls {...props} />
            {/*
              ServerSideRender uses a GET request with attributes encoded in the query string.
              Sending large style objects (borders/padding/radius) can exceed proxy limits (502 on some hosts).
              To keep preview accurate, we inject the CSS vars here and omit style attrs from the SSR request.
            */}
            <style>{(() => {
                const id = attributes?.blockId || `block-${props.clientId}`;

                const sfBorderWidth = attributes?.hz_sf_border?.width || "1px";
                const sfBorderColor = attributes?.hz_sf_border?.color || "#ccc";
                const sfRadius = attributes?.hz_sf_border_radius || {};
                const sfPad = attributes?.hz_sf_padding || {};
                const sidebarWidth = attributes?.hz_sidebar_width ? `${attributes.hz_sidebar_width}%` : "33.333%";

                const lsBorderWidthRaw = attributes?.hz_ls_border?.width || "1px";
                const lsBorderWidth = lsBorderWidthRaw === "0px" ? "1px" : lsBorderWidthRaw;
                const lsBorderColor = attributes?.hz_ls_border?.color || "#ccc";
                const lsRadius = attributes?.hz_ls_border_radius || {};

                const jlBorderWidthRaw = attributes?.hz_jl_border?.width || "1px";
                const jlBorderWidth = jlBorderWidthRaw === "0px" ? "1px" : jlBorderWidthRaw;
                const jlBorderColor = attributes?.hz_jl_border?.color || "#cbcbcb";
                const jlRadius = attributes?.hz_jl_border_radius || {};
                const jlPad = attributes?.hz_jl_padding || {};

                const bsBorderWidthRaw = attributes?.hz_bs_border?.width || "1px";
                const bsBorderWidth = bsBorderWidthRaw === "0px" ? "1px" : bsBorderWidthRaw;
                const bsBorderColor = attributes?.hz_bs_border?.color || "#4e35df";
                const bsRadius = attributes?.hz_bs_border_radius || {};
                const bsPad = attributes?.hz_bs_padding || {};

                const bBg = attributes?.hz_button_background_color || "";
                const bTx = attributes?.hz_button_text_color || "";

                return `
                    #${id}{
                        --hz-sf-border-width:${sfBorderWidth};
                        --hz-sf-border-color:${sfBorderColor};
                        --hz-sf-border-style:${sfBorderWidth && sfBorderWidth !== "0px" ? "solid" : "none"};
                        --hz-sf-border-radius-topleft:${sfRadius.topLeft || "5px"};
                        --hz-sf-border-radius-topright:${sfRadius.topRight || "5px"};
                        --hz-sf-border-radius-bottomright:${sfRadius.bottomRight || "5px"};
                        --hz-sf-border-radius-bottomleft:${sfRadius.bottomLeft || "5px"};
                        --hz-sf-padding-left:${sfPad.left || "15px"};
                        --hz-sf-padding-right:${sfPad.right || "15px"};
                        --hz-sf-padding-top:${sfPad.top || "15px"};
                        --hz-sf-padding-bottom:${sfPad.bottom || "15px"};

                        --hz-sidebar-width:${sidebarWidth};

                        --hz-ls-border-width:${lsBorderWidth};
                        --hz-ls-border-color:${lsBorderColor};
                        --hz-ls-border-style:${lsBorderWidth && lsBorderWidth !== "0px" ? "solid" : "none"};
                        --hz-ls-border-radius-topleft:${lsRadius.topLeft || "5px"};
                        --hz-ls-border-radius-topright:${lsRadius.topRight || "5px"};
                        --hz-ls-border-radius-bottomright:${lsRadius.bottomRight || "5px"};
                        --hz-ls-border-radius-bottomleft:${lsRadius.bottomLeft || "5px"};

                        --hz-jl-border-width:${jlBorderWidth};
                        --hz-jl-border-color:${jlBorderColor};
                        --hz-jl-border-style:${jlBorderWidth && jlBorderWidth !== "0px" ? "solid" : "none"};
                        --hz-jl-border-radius-topleft:${jlRadius.topLeft || "5px"};
                        --hz-jl-border-radius-topright:${jlRadius.topRight || "5px"};
                        --hz-jl-border-radius-bottomright:${jlRadius.bottomRight || "5px"};
                        --hz-jl-border-radius-bottomleft:${jlRadius.bottomLeft || "5px"};
                        --hz-jl-padding-left:${jlPad.left || "15px"};
                        --hz-jl-padding-right:${jlPad.right || "15px"};
                        --hz-jl-padding-top:${jlPad.top || "15px"};
                        --hz-jl-padding-bottom:${jlPad.bottom || "15px"};

                        --hz-bs-border-width:${bsBorderWidth};
                        --hz-bs-border-color:${bsBorderColor};
                        --hz-bs-border-style:${bsBorderWidth && bsBorderWidth !== "0px" ? "solid" : "none"};
                        --hz-bs-border-radius-topleft:${bsRadius.topLeft || "5px"};
                        --hz-bs-border-radius-topright:${bsRadius.topRight || "5px"};
                        --hz-bs-border-radius-bottomright:${bsRadius.bottomRight || "5px"};
                        --hz-bs-border-radius-bottomleft:${bsRadius.bottomLeft || "5px"};

                        --hz-b-bg-color:${bBg};
                        --hz-b-tx-color:${bTx};
                        --hz-b-padding-left:${bsPad.left || "13px"};
                        --hz-b-padding-right:${bsPad.right || "13px"};
                        --hz-b-padding-top:${bsPad.top || "13px"};
                        --hz-b-padding-bottom:${bsPad.bottom || "13px"};
                    }`;
            })()}</style>
            <ServerSideRender
                block="wp-job-openings/blocks"
                attributes={(() => {
                    const ssrAttributes = { ...attributes };
                    delete ssrAttributes.hz_sf_border;
                    delete ssrAttributes.hz_sf_border_radius;
                    delete ssrAttributes.hz_sf_padding;
                    delete ssrAttributes.hz_sidebar_width;
                    delete ssrAttributes.hz_ls_border;
                    delete ssrAttributes.hz_ls_border_radius;
                    delete ssrAttributes.hz_jl_border;
                    delete ssrAttributes.hz_jl_border_radius;
                    delete ssrAttributes.hz_jl_padding;
                    delete ssrAttributes.hz_bs_border;
                    delete ssrAttributes.hz_bs_border_radius;
                    delete ssrAttributes.hz_bs_padding;
                    delete ssrAttributes.hz_button_background_color;
                    delete ssrAttributes.hz_button_text_color;
                    return ssrAttributes;
                })()}
            />
        </div>
    );
}
