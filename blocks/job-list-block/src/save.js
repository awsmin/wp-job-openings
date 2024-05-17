import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save(props) {

	const { attributes } = props;
	const blockProps = useBlockProps.save();
    return <InnerBlocks.Content />;
}