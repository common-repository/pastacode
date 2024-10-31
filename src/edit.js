import './editor.scss';
import { useBlockProps } from '@wordpress/block-editor';
import { useState, useCallback, Fragment, useEffect } from '@wordpress/element';

import PastacodeInspector from './components/inspector';
import PastacodeEditor from './components/editor';
const { fields, langs, services, posInfo, cmStyle } = pastaGutVars;

export default function Edit( props ) {

	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();
	const [ code, setRemoteCode ] = useState({});

	/**
	 * Fetch code
	 */
	 useEffect( useCallback( () => {
		let abortController = new AbortController();
		wp.apiFetch( {
			path: 'pastacode/v2/retrieve_code',
			signal: abortController?.signal,
			method: 'POST',
			data:{
				provider:attributes.provider,
				lines:attributes.lines,
				file:attributes.file,
				message:attributes.message,
				revision:attributes.revision,
				path_id:attributes.path_id,
				repos:attributes.repos,
				user:attributes.user,
			}
		} ).then( ( res ) => {
			setRemoteCode( res );
		} ).catch( (e) => {
			setRemoteCode( {} );
		});

		return () => {
			abortController.abort();
		};
	} ), [
		attributes.provider,
		attributes.lines,
		attributes.file,
		attributes.message,
		attributes.revision,
		attributes.path_id,
		attributes.repos,
		attributes.user
	])


	return (
		<Fragment>
				{props.isSelected && (
					<PastacodeInspector
						attributes={attributes}
						setAttributes={setAttributes}
						services={services}
						langs={langs}
					/>
				) }
				<div {...blockProps}>
					<PastacodeEditor
						fields={fields}
						code={code}
						langs={langs}
						attributes={attributes}
						setAttributes={setAttributes}
						posInfo={posInfo}
						cmStyle={cmStyle}
						selected={props.isSelected}
					/>
				</div>
			</Fragment>
	);
}
