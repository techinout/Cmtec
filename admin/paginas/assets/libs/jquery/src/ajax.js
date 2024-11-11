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
					if{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��A<Y   	�RJ��RJ��QI��QI��QI��QI��PI��PH��PH��OH��OG��OG��OG��NG��NF��NF��NF��ME��ME��ME��LD��LD��LD��KD��KC��KC��JC��JC��JB��IB��IB��IA��HA��HA��H@��G@��G@��G@��G?��F?��F?��E>��E>��E>��D=��D=��D=��C<��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��NF�   	�RJ��RJ��RJ��RJ��QI��QI��QI��QI��PH��PH��PH��OH��OG��OG��NG��NG��NF��NF��MF��ME��ME��LE��LD��LD��LD��KD��KC��KC��JC��JC��JB��IB��IA��IA��HA��HA��H@��G@��G@��G?��F?��F?��F?��E>��E>��E>��D=��D=��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OG��OG��OG��NG��NF��NF��NF��MF��tn�ۦ��ۦ��ڦ��ڥ���yt��KD��KC��KC��JC��JC��JB��IB��IA��IA��HA��H@��H@��G@��G@��G?��F?��F?��F?��E>��E>��E>��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�SL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��PI��PH��PH��OH��OG��OG��OG��NG��NF��NF�כ������������������ۧ���LD��KD��KC��KC��KC��JC��JC��JB��IB��IA��HA��HA��H@��G@��G@��G@��G?��F?��F?��F>��E>��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�TL��TL��SK��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PH��PH��PH��OH��OG��OG��NG��NG�ל������������������ۧ���LD��LD��LD��KD��KC��KC��JC��JC��JB��IB��IB��IA��HA��HA��H@��G@��G@��G?��G?��F?��F?��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�TL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OH��OG��OG�ל������������������ܨ���ME��LE��LD��LD��KD��KD��KC��KC��JC��JC��JB��IB��IA��IA��HA��HA��H@��G@��G@��G?��F?��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�UM��UL��TL��TL��TL��SL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OG�ל������������������ܨ���MF��ME��ME��LE��LD��LD��KD��KD��KC��KC��JC��JC��JB��IB��IA��IA��HA��HA��H@��G@��G@��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�UM��UM��UM��UL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PI��PH��PH�؜������������������ܨ���NF��NF��ME��ME��LE��LD��LD��LD��KD��KD��KC��JC��JC��JB��IB��IB��IA��HA��HA��H@��G@��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�VN��UN��UM��UM��UM��TL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PI�؝������������������ܨ���NG��NF��NF��MF��ME��ME��LE��LD��LD��LD��KD��KC��KC��JC��JC��JB��IB��IB��IA��HA��HA��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�VN��VN��VN��UN��UM��UM��UM��TL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI�؝������������������ܩ���OG��NG��NG��NF��NF��MF��ME��ME��LE��LD��LD��KD��KD��KC��KC��JC��JC��JB��IB��IA��IA��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�WO��VO��VN��VN��VN��UM��UM��UM��UL��TL��TL��TL��SL��SK��SK��SK��RJ��RJ��RJ��QI��QI�؝������������������ݩ���OG��OG��OG��NG��NF��NF��NF��MF��ME��ME��LE��LD��LD��KD��KD��KC��KC��JC��JC��IB��IB��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�WP��WO��WO��VO��VN��VN��VN��UM��UM��UM��TL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��RJ�؝������������������ݩ���PH��OH��OG��OG��NG��NG��NF��NF��NF��ME��ME��LE��LD��LD��LD��KD��KC��KC��JC��JC��JB��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�XP��XP��WO��WO��WO��VN��VN��VN��UN��UM��UM��UM��TL��TL��TL��TL��SK��SK��SK��RJ��RJ�ٞ������������������ݪ���PI��PH��PH��OH��OG��OG��NG��NG��NF��NF��MF��ME��ME��LE��LD��LD��LD��KD��KC��KC��JC��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�XP��XP��XP��XP��WO��WO��WO��VN��VN��VN��UM��UM��UM��UL��TL��TL��TL��SL��SK��SK��SK�ٞ������������������ݪ���QI��QI��PH��PH��PH��OH��OG��OG��NG��NG��NF��NF��MF��ME��ME��LE��LD��LD��KD��KD��KC��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�YQ��XQ��XP��XP��XP��WP��WO��WO��VO��VN��VN��VN��UM��UM��UM��UL��TL��TL��TL��SK��SK�ٞ������������������ݪ���QI��QI��QI��PI��PH��PH��OH��OG��OG��OG��NG��NF��NF��NF��MF��ME��ME��LE��LD��LD��KD��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�YQ��YQ��YQ��XQ��XP��XP��XP��WP��WO��WO��VO��VN��VN��VN��UM��UM��UM��TL��TL��TL��TL�ڞ������������������ު���RJ��QI��QI��QI��QI��PI��PH��PH��OH��OG��OG��NG��NG��NF��NF��NF��MF��ME��ME��LD��LD��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�ZR��YQ��YQ��YQ��YQ��XP��XP��XP��XP��WO��WO��WO��VN��VN��VN��UN��UM��UM��UM��TL��TL�ڟ������������������ު���RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OH��OG��OG��NG��NG��NF��NF��MF��ME��ME��LE��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KC��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�ZR��ZR��ZQ��YQ��YQ��YQ��YQ��XP��XP��XP��WP��WO��WO��VO��VN��VN��VN��UM��UM��UM��UL�ڟ������������������ޫ���SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OH��OG��OG��NG��NF��NF��NF��MF��ME��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KD��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�[S��ZR��ZR��ZR��YQ��YQ��YQ��YQ��XQ��XP��XP��XP��WP��WO��WO��VO��VN��VN��VN��UM��UM�ڟ������������������ޫ���SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PI��PH��PH��OH��OG��OG��OG��NG��NF��NF��NF��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KD��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   	�[S��[S��ZS��ZR��ZR��ZR��YQ��YQ��YQ��YQ��XP��XP��XP��XP��WO��WO��WO��VN��VN��VN��UN�ڟ������������������ޫ���TL��SK��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PI��PH��PH��OH��OG��OG��OG��NG��NF��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��d]��KD��RJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��QH�   �\T��[S��[S��[S��ZR��ZR��ZR��ZQ��YQ��YQ��YQ��YQ��XP��XP��XP��XP��WO��WO��WO��VN��VN�ڠ������������������߫���TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OH��OG��OG��NG��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��e]��LD��SJ��VM��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��RI�   �\T��\T��[T��[S��[S��[S��ZR��ZR��ZR��ZQ��YQ��YQ��YQ��YQ��XP��XP��XP��WP��WO��WO��VO�ڠ������������������߬���UL��TL��TL��TL��SL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��PH��PH��PH��OH��OG��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��e^��MF��SK��WN��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��SJ�   �\T��\T��\T��\T��[S��[S��[S��ZS��ZR��ZR��ZR��YQ��YQ��YQ��YQ��XQ��XP��XP��XP��WP��WO�ۡ������������������߬���UM��UM��TL��TL��TL��TL��SL��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PI��PH��PH��OH��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��g_��OG��TK��WN��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��UL�   �\U��\T��\T��\T��\T��\T��[S��[S��[S��ZS��ZR��ZR��ZR��YQ��YQ��YQ��YQ��XQ��XP��XP��XP�ۡ������������������߬���UN��UM��UM��UM��TL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��QI��PH��PH��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��c[��RI��UL��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��VMm    �]U��]U��\U��\T��\T��\T��\T��\T��[S��[S��[S��ZR��ZR��ZR��ZQ��YQ��YQ��YQ��YQ��XP��XP�ۡ������������������߬���VN��VN��UN��UM��UM��UL��TL��TL��TL��TL��SK��SK��SK��RJ��RJ��RJ��QI��QI��QI��QI��QI��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��~v��SK��TK��VN��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YO�LL    �^V��]V��]U��]U��\U��\T��\T��\T��\T��[T��[S��[S��[S��wq����������������������������������������������������������������������������������zt��TL��TL��TL��SL��SK��SK��RJ��RJ��RJ��RJ��QI��QI��QI��c]��oi��vn��{t��w��y��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��{��x��]U��SJ��UM��WN��XO��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��YP��XO��VOM        �^V��^V��^V��]V��]U��]U��\T��\T��\T��\T��