const { createBlock } = wp.blocks;
import {escape, unescape} from 'he';

const Transforms = {
	to:[
		{
			type:'block',
			blocks: ['core/code'],
			transform: ({manual}) => {
				return createBlock( 'core/code', {
					content : escape( manual )
				} );
			},
			priority: 20
		}
	],
	from: [
		{
			type: 'enter',
			regExp: /^code|pastacode$/,
			transform: () => createBlock( 'wab/pastacode' ),
		},
		{
			type: 'block',
			blocks: [ 'core/code' ],
			transform: ({content}) => {
				return createBlock( 'wab/pastacode', {
					lang         : "markup",
					provider     : "manual",
					manual       : unescape( content ),
					message      : "",
					supinfos     : "",
				} );
			},
		},
		{
			type: 'block',
			blocks: [ 'core/shortcode' ],
			transform: ( {text} ) => {
				let scAtts = {};
				text.match(/[\w-]+=".*?"/g).forEach(function(atts) {
					atts = atts.match(/([\w-]+)="(.*?)"/);
					scAtts[atts[1]] = atts[2];
				});

				return createBlock( 'wab/pastacode', {
					lang         : scAtts.lang,
					user         : scAtts.user,
					path_id      : scAtts.path_id,
					repos        : scAtts.repos,
					revision     : scAtts.revision,
					file         : scAtts.file,
					highlight    : scAtts.highlight,
					supinfos     : scAtts.supinfo,
					provider     : scAtts.provider,
					lines        : scAtts.lines,
					message      : scAtts.message,
					linenumbers  : scAtts.linenumbers,
					manual       : scAtts.manual ? decodeURIComponent( scAtts.manual ) : ''
				} );
			},
			isMatch:({text}) => {
				const re = wp.shortcode.regexp('pastacode');
				return false !== re.test(text);
			}
		},
		{
			type:'shortcode',
			tag:'pastacode',
			attributes: {
				lang: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.lang;
					}
				},
				user: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.user;
					}
				},
				provider: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.provider;
					}
				},
				repos: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.repos;
					}
				},
				revision: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.revision;
					}
				},
				linenumbers: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.linenumbers;
					}
				},
				lines: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.lines;
					}
				},
				highlight: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.highlight;
					}
				},
				message: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.message;
					}
				},
				file: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.file;
					}
				},
				path_id: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.path_id;
					}
				},
				manual: {
					type: 'string',
					shortcode: (attrs, _ref) => {
						return attrs.named.provider == 'manual' ? decodeURIComponent( attrs.named.manual ) : '';
					}
				}
			  },
			  priority: 20
		}
	],
};

export default Transforms;