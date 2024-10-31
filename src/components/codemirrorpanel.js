import React from 'react';
import CodeMirror from '@uiw/react-codemirror';
import { useState, useEffect, useCallback } from '@wordpress/element';
import { StreamLanguage } from "@codemirror/language";
import { sass } from "@codemirror/legacy-modes/mode/sass";
import { ruby } from "@codemirror/legacy-modes/mode/ruby";
import { csharp, c } from "@codemirror/legacy-modes/mode/clike";
import { shell } from "@codemirror/legacy-modes/mode/shell";
import { coffeeScript } from "@codemirror/legacy-modes/mode/coffeescript";
import { haskell } from "@codemirror/legacy-modes/mode/haskell";
import { css, less } from "@codemirror/legacy-modes/mode/css";
import { php } from '@codemirror/lang-php';
import { javascript } from '@codemirror/lang-javascript';
import { html } from '@codemirror/lang-html';
import { cpp } from '@codemirror/lang-cpp';
import { python } from '@codemirror/lang-python';
import { markdown } from '@codemirror/lang-markdown';
import { sql } from '@codemirror/lang-sql';
import { java } from '@codemirror/lang-java';

import * as themes from '@uiw/codemirror-themes-all';

export default function CodePanel (props) {

	const [lang, setLang] = useState( false );

	useEffect( useCallback( () => {
		let lang;
		switch( props.language ) {
			case 'php':
				lang = php({plain:true});
				break;
			case 'cpp':
				lang = cpp();
				break;
			case 'markup':
				lang = html();
				break;
			case 'javascript':
				lang = javascript({jsx:true});
				break;
			case 'typescript':
				lang = javascript({jsx:true,typescript:true});
				break;
			case 'python':
				lang = python();
				break;
			case 'markdown':
			case 'treeview':
				lang = markdown();
				break;
			case 'sql':
				lang = sql();
				break;
			case 'css':
				lang = StreamLanguage.define(css);
				break;
			case 'less':
				lang = StreamLanguage.define(less);
				break;
			case 'java':
				lang = java();
				break;
			case 'sass':
				lang = StreamLanguage.define(sass);
				break;
			case 'ruby':
			case 'haml':
				lang = StreamLanguage.define(ruby);
				break;
			case 'c':
				lang = StreamLanguage.define(c);
				break;
			case 'csharp':
				lang = StreamLanguage.define(csharp);
				break;
			case 'apacheconf':
			case 'bash':
			case 'git':
				lang = StreamLanguage.define(shell);
				break;
			case 'coffeescript':
				lang = StreamLanguage.define(coffeeScript);
				break;
			case 'haskell':
				lang = StreamLanguage.define(haskell);
				break;
		}
		setLang( lang );
	} ), [props.language])

	return (
		<CodeMirror
			value={props.code}
			minHeight="250px"
			onChange={props.onChange}
			basicSetup={{
				lineNumbers: props.showlines,
			}}
			theme={themes[props.cmStyle]}
			extensions={[lang]}
			/>
	);
}
