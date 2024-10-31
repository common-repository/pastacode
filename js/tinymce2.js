(function( window, views, $, shortcode ) {

	var currentLang;

	function fields( provider, pfields, editor, values ) {
		var fields = [];

		for ( var k in pfields ) {

			if ( ! _.isUndefined( values ) && ! _.isUndefined( values[k] ) ) {
				pfields[k].value = values[k];
			} else {
				pfields[k].value = '';
			}

			if ( ! _.isUndefined( pfields[k]['classes'] ) ) {
				if ( _.contains( pfields[k]['classes'].split(' '), provider ) ) {
					fields.push( pfields[k] );
				}
			}else {
				if ( pfields[k]['name'] == 'lang' ) {
					pfields[k]['onpostRender'] = function(){
						currentLang = this;
					}
					fields.push( pfields[k] );
				}
			}
			
			if ( ! _.isUndefined( pfields[k]['multiline'] ) && pfields[k]['multiline'] ) {
				pfields[k]['onpostRender'] = function() {
					var self = this;
					this.before( {
						type:'button',
						text:' ',
						tooltip:pastacodeVars['extendText'],
						classes:'hidden-on-smallscreen',
						style:'background:none;-moz-box-shadow:none;-webkit-box-shadow:none;box-shadow:none;margin-left:-10px;background:url(' + pastacodeVars['extendIcon'] + ') no-repeat;background-size:100%;width:32px;height:31px;',
						border:'0',
						onclick: function() {
							var codemirrorEl;
							var CMargs = codeMirrorArgs( currentLang.value() );
							editor.windowManager.open( {
								title: pastacodeText['window-title'] + ' - ' 
									+ pastacodeText['window-manuel-full'] 
									+ ' [' + pastacodeVars.textLang[ currentLang.value() ] + ']',
								minHeight:window.innerHeight - 100,
								minWidth:window.innerWidth - 50,
								body:[{
									type:'textbox', 
									multiline:true, 
									minHeight:window.innerHeight - 160,
									name:'newCode',
									value : self.value(),
									onPostRender: function() {
										var textarea = this.getEl();
										$LAB.script( pastacodeVars.scripts.codemirror )
										  .wait()
										  .script( pastacodeVars.scripts.matchbrackets )
										  .script( CMargs.scripts )
										  .wait(function(){
											codemirrorEl = CodeMirror.fromTextArea( textarea, CMargs );
											codemirrorEl.on('change', function() {
												codemirrorEl.save();
											});
											setTimeout( function () {
												codemirrorEl.refresh();
											}, 200 );
										});
									}
								}],
								onsubmit:function( e ){
									self.value( e.data.newCode );
								},
								onkeydown: function (e) {
									if (e.keyCode == 9){ // tab pressed
							            e.preventDefault();
							            e.stopPropagation();
							            return false;
							        }
				                }
							} );
						},
					});
				};
			}
		}

		fields.push( {
			type: 'textbox',
			visible: false,
			value: provider,
			name:'provider',
			classes:'hidden-field'
		} );
		return fields;
	}

	function codeMirrorArgs( type ) {
		var args = {
			lineNumbers: true,
			indentWithTabs:true,
			matchBrackets: true,
			theme:pastacodeVars.editorTheme,
			indentUnit:4,
			tabeSize:4,
		};

		if ( ! _.isUndefined( pastacodeVars.language_mode[ type ] ) ) {
			args.scripts = pastacodeGetScripts( pastacodeVars.language_mode[ type ].libs );
			args.mode = pastacodeVars.language_mode[ type ].mode;
		} else {
			args.scripts = pastacodeGetScripts( [ 'xml', 'css', 'javascript', 'htmlmixed' ] );
			args.mode = 'htmlmixed';
		}

		return args;
	}

	function pastacodeGetScripts( scripts ) {
		var urls = [];
		for (var i = scripts.length - 1; i >= 0; i--) {
			if ( typeof pastacodeVars.scripts[ scripts[ i ] ] != 'undefined' ) {
				urls.push( pastacodeVars.scripts[ scripts[ i ] ] );
				delete pastacodeVars.scripts[ scripts[ i ] ];
			}
		}
		return urls;
	}

	function theFunction( key, editor, pvars ) {
		var fn2 = function() {
			editor.windowManager.open( {
				title: pastacodeText['window-title'] + ' - ' + pvars[key],
				body: fields( key, pastacodeVars['fields'], editor ),
				onsubmit: function( e ) {
					var out = '';
					if( e.data['provider'] == 'manual' ) {
						e.data.manual = encodeURIComponent( e.data.manual );
					}
					out += '[pastacode';
					for ( var attr in e.data ) {
						out += ' ' + attr + '="' + e.data[ attr ] + '"';
					}
					out += '/]';

					editor.insertContent( out );
					editor.nodeChanged();
				}
			} );
		};
		return fn2;
	}

	function getSourceCode( text ) {
		var atts = getAttributes(text);
			atts.action = 'pastacode-get-source-code';
		return new Promise(function(resolve, reject) {
			$.ajax( ajaxurl, {
				data:atts,
				method:'POST',
				success:function(data) {
					if ( data.success ) {
						resolve( data.data );
					} else {
						resolve( false );
					}
				},
				error:function(data) {
					reject( false );
				}
			} );
		});
	}

	function providers( editor, pvars ) {
		var providers = [];
		for (var key in pvars ) {
			var provider = new Object();
			provider.text = pvars[key];
			provider.onclick = theFunction( key, editor, pvars );
			providers.push( provider );
		};
		return providers;
	}

	tinymce.PluginManager.add('pcb', function( editor, url ) {

		editor.addButton('pcb', {
			icon: 'pcb-icon',
			type: 'menubutton',
			tooltip: pastacodeVars['tooltip'],
			menu : providers(editor,pastacodeVars['providers'])
		});

		views.register( 'pastacode', {
		    initialize: function() {
			    var self = this;
			    var yo = this;
			    var titre = '<div class="pastacode-view"><div class="pastacode-col"><strong class="pasta-titre">Pastacode</strong></div>';
			    var provider = getAttr( self.text, 'provider' );
				titre += '<div class="pastacode-col">' + pastacodeText['label-type'] + ' <strong>' + pastacodeVars.providers[ provider ] + '</strong><br>';
			    switch ( provider ) {
					case 'manual' :
			    		var testOld = proccessOldShortcode( self );
			    		if ( testOld ) {
			    			setTimeout(function() {
			    				$(editor.getBody())
			    				.find('[data-wpview-text="' + self.encodedText + '"]').eq(0)
			    				.attr('data-wpview-text', encodeURIComponent( testOld ) );
			    				self.update(self.text,editor);
			    			}, 50);
			    		}
						if ( getAttr( self.text, 'message' ) ) {
							titre += pastacodeText['label-title'] + ' <strong>' + _.escape( getAttr( self.text, 'message' ) ) + '</strong><br>';
						}
						break;
					default : 
						titre += pastacodeText['label-title'] + ' <strong>' + _.escape( getAttr( self.text, 'path_id' ) ) + '</strong><br/>';
				}
				if ( getAttr( self.text, 'lang' ) ) {
					titre += pastacodeText['label-lang'] + ' <strong>' + pastacodeVars.textLang[ getAttr( self.text, 'lang' ) ] + '</strong><br/>';
				}
				var l = getAttr( self.text, 'lines' );
				if ( l ) {
					titre += pastacodeText['label-lines'] + ' <strong>' + _.escape( l ) + '</strong><br/>';
				}
				titre += '</div>';
				if ( 'y' === pastacodeVars.preview ) {
					getSourceCode( self.text ).then( function( code ) {
						if ( code.code ) {
							var more = code.more ? ' class="more"' : '';
							titre += '<pre><code' + more + '>' + code.code + '</code></pre>';
						}
						titre += '</div>';
						self.render( titre );
						editor.focus();
					},function() {
						titre += '</div>';
						self.render( titre );
						editor.focus();
					});
				} else {
					titre += '</div>';
					self.render( titre );
					editor.focus();
				}
			},
			edit: function( text, update ) {
				var provider = getAttr(text, 'provider' );
				var values = [];
				for ( var field in pastacodeVars['fields'] ) {
					values[field] = getAttr(text, pastacodeVars['fields'][field].name );
				}

				var fn = theFunction( provider, editor, pastacodeVars['providers']);

				editor.windowManager.open( {
					title: pastacodeText['window-title'] + ' - ' + pastacodeVars['providers'][provider],
					body: fields( provider, pastacodeVars['fields'], editor, values),
					onsubmit: function( e ) {
						var out = '';
						if( e.data['provider'] == 'manual' ) {
							e.data.manual = encodeURIComponent( e.data.manual );
						}
						out += '[pastacode';
						for ( var attr in e.data ) {
							out += ' ' + attr + '="' + e.data[ attr ] + '"';
						}
						out += '/]';
						update( out );
					}
				});
			},
		} );

	});

	function proccessOldShortcode( obj ) {
		if ( obj.shortcode.content ) {
			var re = /<pre><code>([\s\S]+)<\/code><\/pre>/m; 
			var content = re.exec( obj.shortcode.content );
			if ( content ) {
				var newContent = content[1].replace( /&amp;/g, '&').replace(/&lt;/g, '<' ).replace(/&gt;/g, '>').replace(/&#34;/g, '"').replace(/&#039;/g, "'");
				obj.shortcode.attrs.named.manual = encodeURIComponent( newContent );

				obj.text = '[pastacode';
				for ( var attr in obj.shortcode.attrs.named ) {
					obj.text += ' ' + attr + '="' + obj.shortcode.attrs.named[ attr ] + '"';
				}
				obj.text += '/]';
				return obj.text;
			}
		}
		return false;
	}

	function getAttr( str, name ) {
		name = new RegExp( name + '=\"([^\"]+)\"' ).exec( str );
		return name ? window.decodeURIComponent( name[1] ) : '';
	}

	function getAttributes( str ) {
		var atts = {};
		var re = /([a-zA-Z0-9-_]+)=\"([^\"]+)\"/g; 
		var m;
		 
		while ((m = re.exec(str)) !== null) {
		    if (m.index === re.lastIndex) {
		        re.lastIndex++;
		    }
		    atts[ m[1] ] = window.decodeURIComponent( m[2] );
		}
		return atts;
	}

})( window, window.wp.mce.views, window.jQuery, window.wp.shortcode );