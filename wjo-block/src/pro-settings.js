import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { ToggleControl, PanelBody } from '@wordpress/components';

const withProSettings = createHigherOrderComponent((BlockEdit) => {
    return (props) => { 
        if (props.name !== 'awsm/job') {
           // return <BlockEdit {...props} />;
        }

        const {
            attributes: { position_filling },
            setAttributes
        } = props;

        return (
            <>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title="Pro Settings">
                        <ToggleControl
                            label="Position Filling"
                            checked={position_filling}
                            onChange={position_filling => setAttributes({ position_filling })}
                        />
                    </PanelBody>
                </InspectorControls>
            </>
        );
    };
}, 'withProSettings');

addFilter('awsmJobBlock.WidgetInspectorControls', 'my-plugin/with-pro-settings', withProSettings);