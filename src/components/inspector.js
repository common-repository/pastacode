import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { 
	PanelBody,
	ToggleControl, 
	SelectControl,
	TextControl,
 } from '@wordpress/components';

export default function PastacodeInspector ({attributes, setAttributes, services, langs}) {

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Code provider settings', 'pastacode' ) }>
			<SelectControl
				label={ __( 'Select a provider', 'pastacode' ) }
				value={ attributes.provider }
				options={ services }
				onChange={ (v) => setAttributes( { provider: v } ) }
				__nextHasNoMarginBottom
			/>
			{
				attributes.provider !== 'manual' && (
					<TextControl
						label={ __( 'Visible lines', 'pastacode' ) }
						value={ attributes.lines }
						onChange={ (v) => setAttributes( { lines: v } ) }
					/>
				)
			}
			</PanelBody>
			<PanelBody title={ __( 'Display settings', 'pastacode' ) }>
				<SelectControl
					label={ __( 'Language Syntax', 'pastacode' ) }
					value={ attributes.lang }
					options={ langs }
					onChange={ (v) => setAttributes( { lang: v } ) }
					__nextHasNoMarginBottom
				/>
				<TextControl
						label={ __( 'Highlighted lines', 'pastacode' ) }
						value={ attributes.highlight }
						placeholder="1-4,10…"
						onChange={ (v) => setAttributes( { highlight: v } ) }
					/>
				<ToggleControl
					label={ __( 'Show line numbers', 'pastacode' ) }
					checked={ attributes.linenumbers }
					onChange={ (v) => setAttributes( { linenumbers: v }  ) }
				/>
			</PanelBody>
		</InspectorControls>
	)
}