define( [
	"./core",
	"./var/document",
	"./var/isFunction",
	"./var/rnothtmlwhite",
	"./ajax/var/location",
	"./ajax/var/nonce",
	"./ajax/var/rquery",

	"./core/init",
	"./ajax/parseXML",
	"./event/trigger",
	"./deferred",
	"./serialize" // jQuery.param
], function( jQuery, document, isFunction, rnothtmlwhite, location, nonce, rquery ) {

"use strict";

var
	r20 = /%20/g,
	rhash = /#.*$/,
	rantiCache = /([?&])_=[^&]*/,
	rheaders = /^(.*?):[ \t]*([^\r\n]*)$/mg,

	// #7653, #8125, #8152: local protocol detection
	rlocalProtocol = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
	rnoContent = /^(?:GET|HEAD)$/,
	rprotocol = /^\/\//,

	/* Prefilters
	 * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
	 * 2) These are called:
	 *    - BEFORE asking for a transport
	 *    - AFTER param serialization (s.data is a string if s.processData is true)
	 * 3) key is the dataType
	 * 4) the catchall symbol "*" can be used
	 * 5) execution will start with transport dataType and THEN continue down to "*" if needed
	 */
	prefilters = {},

	/* Transports bindings
	 * 1) key is the dataType
	 * 2) the catchall symbol "*" can be used
	 * 3) selection will start with transport dataType and THEN go to "*" if needed
	 */
	transports = {},

	// Avoid comment-prolog char sequence (#10098); must appease lint and evade compression
	allTypes = "*/".concat( "*" ),

	// Anchor tag for parsing the document origin
	originAnchor = document.createElement( "a" );
	originAnchor.href = location.href;

// Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
function addToPrefiltersOrTransports( structure ) {

	// dataTypeExpression is optional and defaults to "*"
	return function( dataTypeExpression, func ) {

		if ( typeof dataTypeExpression !== "string" ) {
			func = dataTypeExpression;
			dataTypeExpression = "*";
		}

		var dataType,
			i = 0,
			dataTypes = dataTypeExpression.toLowerCase().match( rnothtmlwhite ) || [];

		if ( isFunction( func ) ) {

			// For each dataType in the dataTypeExpression
			while ( ( dataType = dataTypes[ i++ ] ) ) {

				// Prepend if requested
				if ( dataType[ 0 ] === "+" ) {
					dataType = dataType.slice( 1 ) || "*";
					( structure[ dataType ] = structure[ dataType ] || [] ).unshift( func );

				// Otherwise append
				} else {
					( structure[ dataType ] = structure[ dataType ] || [] ).push( func );
				}
			}
		}
	};
}

// Base inspection function for prefilters and transports
function inspectPrefiltersOrTransports( structure, options, originalOptions, jqXHR ) {

	var inspected = {},
		seekingTransport = ( structure === transports );

	function inspect( dataType ) {
		var selected;
		inspected[ dataType ] = true;
		jQuery.each( structure[ dataType ] || [], function( _, prefilterOrFactory ) {
			var dataTypeOrTransport = prefilterOrFactory( options, originalOptions, jqXHR );
			if ( typeof dataTypeOrTransport === "string" &&
				!seekingTransport && !inspected[ dataTypeOrTransport ] ) {

				options.dataTypes.unshift( dataTypeOrTransport );
				inspect( dataTypeOrTransport );
				return false;
			} else if ( seekingTransport ) {
				return !( selected = dataTypeOrTransport );
			}
		} );
		return selected;
	}

	return inspect( options.dataTypes[ 0 ] ) || !inspected[ "*" ] && inspect( "*" );
}

// A special extend for ajax options
// that takes "flat" options (not to be deep extended)
// Fixes #9887
function ajaxExtend( target, src ) {
	var key, deep,
		flatOptions = jQuery.ajaxSettings.flatOptions || {};

	for ( key in src ) {
		if ( src[ key ] !== undefined ) {
			( flatOptions[ key ] ? target : ( deep || ( deep = {} ) ) )[ key ] = src[ key ];
		}
	}
	if ( deep ) {
		jQuery.extend( true, target, deep );
	}

	return target;
}

/* Handles responses to an ajax request:
 * - finds the right dataType (mediates between content-type and expected dataType)
 * - returns the corresponding response
 */
function ajaxHandleResponses( s, jqXHR, responses ) {

	var ct, type, finalDataType, firstDataType,
		contents = s.contents,
		dataTypes = s.dataTypes;

	// Remove auto dataType and get content-type in the process
	while ( dataTypes[ 0 ] === "*" ) {
		dataTypes.shift();
		if ( ct === undefined ) {
			ct = s.mimeType || jqXHR.getResponseHeader( "Content-Type" );
		}
	}

	// Check if we're dealing with a known content-type
	if ( ct ) {
		for ( type in contents ) {
			if ( contents[ type ] && contents[ type ].test( ct ) ) {
				dataTypes.unshift( type );
				break;
			}
		}
	}

	// Check to see if we have a response for the expected dataType
	if ( dataTypes[ 0 ] in responses ) {
		finalDataType = dataTypes[ 0 ];
	} else {

		// Try convertible dataTypes
		for ( type in responses ) {
			if ( !dataTypes[ 0 ] || s.converters[ type + " " + dataTypes[ 0 ] ] ) {
				finalDataType = type;
				break;
			}
			if ( !firstDataType ) {
				firstDataType = type;
			}
		}

		// Or just use first one
		finalDataType = finalDataType || firstDataType;
	}

	// If we found a dataType
	// We add the dataType to the list if needed
	// and return the corresponding response
	if ( finalDataType ) {
		if ( finalDataType !== dataTypes[ 0 ] ) {
			dataTypes.unshift( finalDataType );
		}
		return responses[ finalDataType ];
	}
}

/* Chain conversions given the request and the original response
 * Also sets the responseXXX fields on the jqXHR instance
 */
function ajaxConvert( s, response, jqXHR, isSuccess ) {
	var conv2, current, conv, tmp, prev,
		converters = {},

		// Work with a copy of dataTypes in case we need to modify it for conversion
		dataTypes = s.dataTypes.slice();

	// Create converters map with lowercased keys
	if ( dataTypes[ 1 ] ) {
		for ( conv in s.converters ) {
			converters[ conv.toLowerCase() ] = s.converters[ conv ];
		}
	}

	current = dataTypes.shift();

	// Convert to each sequential dataType
	while ( current ) {

		if ( s.responseFields[ current ] ) {
			jqXHR[ s.responseFields[ current ] ] = response;
		}

		// Apply the dataFilter if provided
		if ( !prev && isSuccess && s.dataFilter ) {
			response = s.dataFilter( response, s.dataType );
		}

		prev = current;
		current = dataTypes.shift();

		if ( current ) {

			// There's only work to do if current dataType is non-auto
			if ( current === "*" ) {

				current = prev;

			// Convert response if prev dataType is non-auto and differs from current
			} else if ( prev !== "*" && prev !== current ) {

				// Seek a direct converter
				conv = converters[ prev + " " + current ] || converters[ "* " + current ];

				// If none found, seek a pair
				if ( !conv ) {
					for ( conv2 in converters ) {

						// If conv2 outputs current
						tmp = conv2.split( " " );
						if ( tmp[ 1 ] === current ) {

							// If prev can be converted to accepted input
							conv = converters[ prev + " " + tmp[ 0 ] ] ||
								converters[ "* " + tmp[ 0 ] ];
							if ( conv ) {

								// Condense equivalence converters
								if ( conv === true ) {
									conv = converters[ conv2 ];

								// Otherwise, insert the intermediate dataType
								} else if ( converters[ conv2 ] !== true ) {
									current = tmp[ 0 ];
									dataTypes.unshift( tmp[ 1 ] );
								}
								break;
							}
						}
					}
				}

				// Apply converter (if not an equivalence)
				if ( conv !== true ) {

					// Unless errors are allowed to bubble, catch and return them
					if ( conv && s.throws ) {
						response = conv( response );
					} else {
						try {
							response = conv( response );
						} catch ( e ) {
							return {
								state: "parsererror",
								error: conv ? e : "No conversion from " + prev + " to " + current
							};
						}
					}
				}
			}
		}
	}

	return { state: "success", data: response };
}

jQuery.extend( {

	// Counter for holding the number of active queries
	active: 0,

	// Last-Modified header cache for next request
	lastModified: {},
	etag: {},

	ajaxSettings: {
		url: location.href,
		type: "GET",
		isLocal: rlocalProtocol.test( location.protocol ),
		global: true,
		processData: true,
		async: true,
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",

		/*
		timeout: 0,
		data: null,
		dataType: null,
		username: null,
		password: null,
		cache: null,
		throws: false,
		traditional: false,
		headers: {},
		*/

		accepts: {
			"*": allTypes,
			text: "text/plain",
			html: "text/html",
			xml: "application/xml, text/xml",
			json: "application/json, text/javascript"
		},

		contents: {
			xml: /\bxml\b/,
			html: /\bhtml/,
			json: /\bjson\b/
		},

		responseFields: {
			xml: "responseXML",
			text: "responseText",
			json: "responseJSON"
		},

		// Data converters
		// Keys separate source (or catchall "*") and destination types with a single space
		converters: {

			// Convert anything to text
			"* text": String,

			// Text to html (true = no transformation)
			"text html": true,

			// Evaluate text as a json expression
			"text json": JSON.parse,

			// Parse text as xml
			"text xml": jQuery.parseXML
		},

		// For options that shouldn't be deep extended:
		// you can add your own custom options here if
		// and when you create one that shouldn't be
		// deep extended (see ajaxExtend)
		flatOptions: {
			url: true,
			context: true
		}
	},

	// Creates a full fledged settings object into target
	// with both ajaxSettings and settings fields.
	// If target is omitted, writes into ajaxSettings.
	ajaxSetup: function( target, settings ) {
		return settings ?

			// Building a settings object
			ajaxExtend( ajaxExtend( target, jQuery.ajaxSettings ), settings ) :

			// Extending ajaxSettings
			ajaxExtend( jQuery.ajaxSettings, target );
	},

	ajaxPrefilter: addToPrefiltersOrTransports( prefilters ),
	ajaxTransport: addToPrefiltersOrTransports( transports ),

	// Main method
	ajax: function( url, options ) {

		// If url is an object, simulate pre-1.5 signature
		if ( typeof url === "object" ) {
			options = url;
			url = undefined;
		}

		// Force options to be an object
		options = options || {};

		var transport,

			// URL without anti-cache param
			cacheURL,

			// Response headers
			responseHeadersString,
			responseHeaders,

			// timeout handle
			timeoutTimer,

			// Url cleanup var
			urlAnchor,

			// Request state (becomes false upon send and true upon completion)
			completed,

			// To know if global events are to be dispatched
			fireGlobals,

			// Loop variable
			i,

			// uncached part of the url
			uncached,

			// Create the final options object
			s = jQuery.ajaxSetup( {}, options ),

			// Callbacks context
			callbackContext = s.context || s,

			// Context for global events is callbackContext if it is a DOM node or jQuery collection
			globalEventContext = s.context &&
				( callbackContext.nodeType || callbackContext.jquery ) ?
					jQuery( callbackContext ) :
					jQuery.event,

			// Deferreds
			deferred = jQuery.Deferred(),
			completeDeferred = jQuery.Callbacks( "once memory" ),

			// Status-dependent callbacks
			statusCode = s.statusCode || {},

			// Headers (they are sent all at once)
			requestHeaders = {},
			requestHeadersNames = {},

			// Default abort message
			strAbort = "canceled",

			// Fake xhr
			jqXHR = {
				readyState: 0,

				// Builds headers hashtable if needed
				getResponseHeader: function( key ) {
					var match;
					if ( completed ) {
						if ( !responseHeaders ) {
							responseHeaders = {};
							while ( ( match = rheaders.exec( responseHeadersString ) ) ) {
								responseHeaders[ match[ 1 ].toLowerCase() + " " ] =
									( responseHeaders[ match[ 1 ].toLowerCase() + " " ] || [] )
										.concat( match[ 2 ] );
							}
						}
						match = responseHeaders[ key.toLowerCase() + " " ];
					}
					return match == null ? null : match.join( ", " );
				},

				// Raw string
				getAllResponseHeaders: function() {
					return completed ? responseHeadersString : null;
				},

				// Caches the header
				setRequestHeader: function( name, value ) {
					if ( completed == null ) {
						name = requestHeadersNames[ name.toLowerCase() ] =
							requestHeadersNames[ name.toLowerCase() ] || name;
						requestHeaders[ name ] = value;
					}
					return this;
				},

				// Overrides response content-type header
				overrideMimeType: function( type ) {
					if{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ—A<Y   	»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¹OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MEÿ·MEÿ¶MEÿ¶LDÿ¶LDÿµLDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ´IBÿ³IBÿ³IAÿ³HAÿ²HAÿ²H@ÿ²G@ÿ±G@ÿ±G@ÿ±G?ÿ±F?ÿ°F?ÿ°E>ÿ°E>ÿ°E>ÿ¯D=ÿ¯D=ÿ¯D=ÿ®C<ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ°NF‚   	»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ¸NGÿ¸NFÿ·NFÿ·MFÿ·MEÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµLDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IAÿ³IAÿ²HAÿ²HAÿ²H@ÿ²G@ÿ±G@ÿ±G?ÿ±F?ÿ±F?ÿ°F?ÿ°E>ÿ°E>ÿ°E>ÿ¯D=ÿ¯D=ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿ¹PHÿ¹PHÿ¹PHÿ¹OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MFÿÇtnÿÛ¦¢ÿÛ¦¢ÿÚ¦¢ÿÚ¥¢ÿÈytÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IAÿ³IAÿ²HAÿ²H@ÿ²H@ÿ²G@ÿ±G@ÿ±G?ÿ±F?ÿ°F?ÿ°F?ÿ°E>ÿ°E>ÿ°E>ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¼SLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¹OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ×›—ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÛ§£ÿ¶LDÿµKDÿµKCÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IAÿ³HAÿ²HAÿ²H@ÿ²G@ÿ±G@ÿ±G@ÿ±G?ÿ±F?ÿ°F?ÿ°F>ÿ°E>ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ¸NGÿ×œ—ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÛ§¤ÿ¶LDÿ¶LDÿµLDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ´IBÿ³IBÿ³IAÿ³HAÿ²HAÿ²H@ÿ²G@ÿ±G@ÿ±G?ÿ±G?ÿ±F?ÿ°F?ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	½TLÿ¼TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ×œ˜ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÜ¨¤ÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµKDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IAÿ³IAÿ²HAÿ²HAÿ²H@ÿ²G@ÿ±G@ÿ±G?ÿ±F?ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	½UMÿ½ULÿ½TLÿ¼TLÿ¼TLÿ¼SLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿ¹PHÿ¹PHÿ¹PHÿ¹OGÿ×œ˜ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÜ¨¥ÿ·MFÿ·MEÿ¶MEÿ¶LEÿ¶LDÿ¶LDÿµKDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IAÿ³IAÿ²HAÿ²HAÿ²H@ÿ²G@ÿ±G@ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	½UMÿ½UMÿ½UMÿ½ULÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿØœ˜ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÜ¨¥ÿ·NFÿ·NFÿ·MEÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµLDÿµKDÿµKDÿµKCÿ´JCÿ´JCÿ´JBÿ´IBÿ³IBÿ³IAÿ³HAÿ²HAÿ²H@ÿ²G@ÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	½VNÿ½UNÿ½UMÿ½UMÿ½UMÿ½TLÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿØ˜ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÜ¨¥ÿ¸NGÿ¸NFÿ·NFÿ·MFÿ·MEÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµLDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IBÿ³IAÿ²HAÿ²HAÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¾VNÿ½VNÿ½VNÿ½UNÿ½UMÿ½UMÿ½UMÿ½TLÿ¼TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿØ™ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÜ©¥ÿ¸OGÿ¸NGÿ¸NGÿ¸NFÿ·NFÿ·MFÿ·MEÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµKDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ³IBÿ³IAÿ³IAÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¾WOÿ¾VOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿ½UMÿ½ULÿ½TLÿ¼TLÿ¼TLÿ¼SLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿØ™ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿİ©¥ÿ¹OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MFÿ·MEÿ¶MEÿ¶LEÿ¶LDÿ¶LDÿµKDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´IBÿ³IBÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¾WPÿ¾WOÿ¾WOÿ¾VOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿ½UMÿ½TLÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿØ™ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿİ©¥ÿ¹PHÿ¹OHÿ¹OGÿ¸OGÿ¸NGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MEÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµLDÿµKDÿµKCÿµKCÿ´JCÿ´JCÿ´JBÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¾XPÿ¾XPÿ¾WOÿ¾WOÿ¾WOÿ¾VNÿ½VNÿ½VNÿ½UNÿ½UMÿ½UMÿ½UMÿ½TLÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿÙ™ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿİª¦ÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ¸NGÿ¸NFÿ·NFÿ·MFÿ·MEÿ·MEÿ¶LEÿ¶LDÿ¶LDÿµLDÿµKDÿµKCÿµKCÿ´JCÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¿XPÿ¿XPÿ¾XPÿ¾XPÿ¾WOÿ¾WOÿ¾WOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿ½UMÿ½ULÿ½TLÿ¼TLÿ¼TLÿ¼SLÿ¼SKÿ¼SKÿ¼SKÿÙ™ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿİª¦ÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ¸NGÿ·NFÿ·NFÿ·MFÿ·MEÿ¶MEÿ¶LEÿ¶LDÿ¶LDÿµKDÿµKDÿµKCÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¿YQÿ¿XQÿ¿XPÿ¾XPÿ¾XPÿ¾WPÿ¾WOÿ¾WOÿ¾VOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿ½UMÿ½ULÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿÙšÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿİª¦ÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¹OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MFÿ·MEÿ¶MEÿ¶LEÿ¶LDÿµLDÿµKDÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¿YQÿ¿YQÿ¿YQÿ¿XQÿ¿XPÿ¾XPÿ¾XPÿ¾WPÿ¾WOÿ¾WOÿ¾VOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿ½UMÿ½TLÿ½TLÿ¼TLÿ¼TLÿÚšÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿŞª§ÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¹OGÿ¸OGÿ¸NGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MFÿ·MEÿ¶MEÿ¶LDÿ¶LDÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¿ZRÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XPÿ¿XPÿ¾XPÿ¾XPÿ¾WOÿ¾WOÿ¾WOÿ¾VNÿ½VNÿ½VNÿ½UNÿ½UMÿ½UMÿ½UMÿ½TLÿ½TLÿÚŸ›ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿŞª§ÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ¸NGÿ¸NFÿ·NFÿ·MFÿ·MEÿ·MEÿ¶LEÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KCÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	¿ZRÿ¿ZRÿ¿ZQÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XPÿ¿XPÿ¾XPÿ¾WPÿ¾WOÿ¾WOÿ¾VOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿ½UMÿ½ULÿÚŸ›ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿŞ«§ÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ·MFÿ·MEÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿ©KDÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	À[SÿÀZRÿ¿ZRÿ¿ZRÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XQÿ¿XPÿ¾XPÿ¾XPÿ¾WPÿ¾WOÿ¾WOÿ¾VOÿ½VNÿ½VNÿ½VNÿ½UMÿ½UMÿÚŸ›ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿŞ«§ÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¹OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ·NFÿ·NFÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿªKDÿ¹RJÿÂVMÿÆXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   	À[SÿÀ[SÿÀZSÿÀZRÿ¿ZRÿ¿ZRÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XPÿ¿XPÿ¾XPÿ¾XPÿ¾WOÿ¾WOÿ¾WOÿ¾VNÿ½VNÿ½VNÿ½UNÿÚŸ›ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿŞ«§ÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸OGÿ¸NGÿ¸NFÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÄd]ÿªKDÿ¹RJÿÂVMÿÇXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¶QH   À\TÿÀ[SÿÀ[SÿÀ[SÿÀZRÿ¿ZRÿ¿ZRÿ¿ZQÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XPÿ¿XPÿ¾XPÿ¾XPÿ¾WOÿ¾WOÿ¾WOÿ¾VNÿ½VNÿÚ œÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿß«§ÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ¸OGÿ¸NGÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÅe]ÿ¬LDÿºSJÿÃVMÿÇXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ¹RI‹   À\TÿÀ\TÿÀ[TÿÀ[SÿÀ[SÿÀ[SÿÀZRÿ¿ZRÿ¿ZRÿ¿ZQÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XPÿ¿XPÿ¾XPÿ¾WPÿ¾WOÿ¾WOÿ¾VOÿÚ œÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿß¬¨ÿ½ULÿ½TLÿ¼TLÿ¼TLÿ¼SLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ¹PHÿ¹OHÿ¸OGÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÇe^ÿ¯MFÿ»SKÿÃWNÿÇXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿ»SJ‰   Á\TÿÀ\TÿÀ\TÿÀ\TÿÀ[SÿÀ[SÿÀ[SÿÀZSÿÀZRÿ¿ZRÿ¿ZRÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XQÿ¿XPÿ¾XPÿ¾XPÿ¾WPÿ¾WOÿÛ¡œÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿß¬¨ÿ½UMÿ½UMÿ½TLÿ½TLÿ¼TLÿ¼TLÿ¼SLÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPIÿ¹PHÿ¹PHÿ¹OHÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÊg_ÿ³OGÿ¾TKÿÄWNÿÇXOÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÀUL†   Á\UÿÁ\TÿÀ\TÿÀ\TÿÀ\TÿÀ\TÿÀ[SÿÀ[SÿÀ[SÿÀZSÿ¿ZRÿ¿ZRÿ¿ZRÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XQÿ¿XPÿ¾XPÿ¾XPÿÛ¡œÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿß¬¨ÿ½UNÿ½UMÿ½UMÿ½UMÿ½TLÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºPHÿ¹PHÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿÇc[ÿ¸RIÿÀULÿÅXOÿÈYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÂVMm    Á]UÿÁ]UÿÁ\UÿÁ\TÿÀ\TÿÀ\TÿÀ\TÿÀ\TÿÀ[SÿÀ[SÿÀ[SÿÀZRÿ¿ZRÿ¿ZRÿ¿ZQÿ¿YQÿ¿YQÿ¿YQÿ¿YQÿ¿XPÿ¿XPÿÛ¡ÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿß¬¨ÿ½VNÿ½VNÿ½UNÿ½UMÿ½UMÿ½ULÿ½TLÿ½TLÿ¼TLÿ¼TLÿ¼SKÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿºQIÿºQIÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿæ~vÿ¸SKÿ½TKÿÂVNÿÆXOÿÈYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYOê²LL    Á^VÿÁ]VÿÁ]UÿÁ]UÿÁ\UÿÁ\TÿÀ\TÿÀ\TÿÀ\TÿÀ[TÿÀ[SÿÀ[SÿÀ[SÿËwqÿóàßÿóàŞÿóàŞÿóàŞÿóàŞÿóàŞÿóàŞÿøííÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿÿùğïÿóàŞÿóàŞÿóàŞÿóßŞÿóßŞÿóßŞÿóßŞÿÌztÿ½TLÿ¼TLÿ¼TLÿ¼SLÿ¼SKÿ¼SKÿ»RJÿ»RJÿ»RJÿ»RJÿºQIÿºQIÿºQIÿ±c]ÿÇoiÿÓvnÿİ{tÿãwÿçyÿê‚{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿëƒ{ÿè€xÿÃ]Uÿ»SJÿÁUMÿÅWNÿÇXOÿÈYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉYPÿÉXOñÆVOM        Á^VÿÁ^VÿÁ^VÿÁ]VÿÁ]UÿÁ]UÿÁ\TÿÀ\TÿÀ\TÿÀ\TÿÀ