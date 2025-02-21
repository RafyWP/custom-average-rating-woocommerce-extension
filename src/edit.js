/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * Edit component for the 'Custom Average Rating WooCommerce Extension' block.
 *
 * @since 1.0.0
 * @author Lit ✴ Code
 *
 * @param {Object} props - The component props.
 * @param {Object} props.attributes - Block attributes.
 * @param {Function} props.setAttributes - Function to update block attributes.
 * @returns {JSX.Element} The rendered edit component.
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';

const Edit = ( { attributes, setAttributes } ) => {
	const { apiEndpoint, clickUrl } = attributes;
	const blockProps = useBlockProps();

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'custom-average-rating' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'API Endpoint', 'custom-average-rating' ) }
						value={ apiEndpoint }
						onChange={ ( newApiEndpoint ) => setAttributes( { apiEndpoint: newApiEndpoint } ) }
						placeholder={ __( 'Enter API endpoint URL...', 'custom-average-rating' ) }
					/>
					<TextControl
						label={ __( 'Click URL', 'custom-average-rating' ) }
						value={ clickUrl }
						onChange={ ( newClickUrl ) => setAttributes( { clickUrl: newClickUrl } ) }
						placeholder={ __( 'Point to URL...', 'custom-average-rating' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<p>
					{ apiEndpoint ? '★★★★★' : __( 'Not set', 'custom-average-rating' ) }
				</p>
			</div>
		</Fragment>
	);
};

export default Edit;
