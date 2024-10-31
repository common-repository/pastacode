
import { __ } from '@wordpress/i18n';

export default function CodeInfo ({code,message}) {
	return (
	<div className="code-embed-infos">
		{code?.url && (
			<a 
			href={code.url}
			className="code-embed-name"
			target="blank"
			>
				{code.name}
			</a>
			)
		}
		{code?.raw && (
			<a 
			href={code.raw}
			className="code-embed-raw"
			target="blank"
			>
				{ __('View raw', 'pastacode' ) }
			</a>
			)
		}
		{(! code?.raw && ! code?.url) && message &&(
			<span
			className="code-embed-name"
			>
				{message}
			</span>
			)
		}
	</div>
	)
}