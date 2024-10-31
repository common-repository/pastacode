import { __ } from '@wordpress/i18n';
import { useRef, useCallback, useMemo, useEffect } from '@wordpress/element';
import CodeInfo from './codeinfo';
import { decodeEntities } from '@wordpress/html-entities';

export default function PrismPanel ({attributes, posInfo, code}) {
	
	const codeWrapper = useRef( null );

	/**
	 * Do Prism
	 */
	useEffect( useCallback( () => {
		Prism.highlightAllUnder( codeWrapper.current );
	}), [
		attributes.provider,
		attributes.manual,
		code
	])

	/**
	 * Select code to show
	 */
	const codeToShow = useMemo( () => {
		return attributes.provider === 'manual' ? attributes.manual : code?.code || __( 'Code not found', 'pastacode')
	},[
		attributes.provider,
		attributes.manual,
		code
	])

	/**
	 * build highlight data-attributes
	 */
	const lineHighlight = useMemo( () => {
		const regex = new RegExp('([0-9-,]+)', 'gm')
		return ( regex.exec(attributes.highlight) !== null ) ? attributes.highlight : '';
	},[attributes.highlight])

	/**
	 * Set prism css class
	 */
	const prismClasses = useMemo( () => {
		return {
			pre: 'code-embed-pre language-' + attributes.lang,
			code: 'code-embed-code language-' + attributes.lang + ( attributes.linenumbers ? ' line-numbers' : '' )
		}
	});

	return (
		<div className="code-embed-wrapper" ref={codeWrapper}>
			{ posInfo == 'top' && <CodeInfo code={code} message={attributes.message}/> }
			<pre className={prismClasses.pre} data-start={
				( code && code.start ) ? code.start : '1'
			} data-line-offset={
				( code && code.start ) ? code.start - 1 : '0'
			}
			data-line={lineHighlight}><code className={prismClasses.code}>{
				decodeEntities( codeToShow )
			}</code></pre>
			{ posInfo == 'bottom' && <CodeInfo code={code} message={attributes.message}/> }
		</div>
	)
}
