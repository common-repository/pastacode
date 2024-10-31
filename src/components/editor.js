import CodePanel from './codemirrorpanel';
import PrismPanel from './prismpanel';
import { Fragment, useCallback } from '@wordpress/element';
import { TextControl } from '@wordpress/components';

export default function PastacodeEditor ({attributes, setAttributes, fields, selected, posInfo, code, cmStyle}) {
	const camelCase = useCallback(function(str) {
		return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function(word, index)
		{
			return index == 0 ? word.toLowerCase() : word.toUpperCase();
		}).replace(/\s+/g, '');
	});


	// Filtrer les fields
	const tf = [];
	for (const [key, value] of Object.entries(fields)) {
		if ( value.classes.includes(attributes.provider) ) {
			if ( ! ['manual','pastacode-highlight','pastacode-lines'].includes( key ) ) {
				const kcamel = camelCase( value.name );
				tf.push( <TextControl
					label={value.label}
					placeholder={value.placeholder}
					value={ attributes[ kcamel ] }
					onChange={ (v) => { setAttributes( { [kcamel]: v } ) } }
				/> )
			}
		}
	}

	return (
		selected ? (
		attributes.provider == 'manual' ? (
			<Fragment>
				<div className="blockcode-settings__wrapper">
					{
						tf
					}
				</div>
				<CodePanel
					code={attributes.manual}
					language={attributes.lang}
					showlines={attributes.linenumbers}
					cmStyle={cmStyle}
					onChange={(v) => setAttributes( { manual: v })}
				/>
			</Fragment>
		) : (
			<div className="blockcode-settings__wrapper">
			{
				tf
			}
			</div>
		)
		) : (
			<PrismPanel
				attributes={attributes}
				posInfo={posInfo}
				code={code}
			/>
		)
	)
}