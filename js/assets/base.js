/**
 * Pigmento Live Tweets Js
 */
(function(LiveTweets, $, undefined) {

	// reload time (ms)
	var _iReloadTime = 60 * 1000,
		// container selector
		_sContainerSlct = '.live-tweet-container',
		// events list
		_oEventsNames = {
			reload: 'live-tweets:reload'
		},
		// single tweet format
		_sTweetFormat = '<time>{date}</time> {text}',
		// date format
		_sDateFormat = 'd M',
		// link text
		_bLinkText = true,
		// grapper path
		// TODO: dynamic path
		_sGrabberUrl = '/wp-content/plugins/live-tweets/live-tweets-grabber.php';

	/**
	 * extend string prototype with format string
	 */
	if (!('format' in String.prototype)) {
		String.prototype.format = function(values) {
			return this.replace(/\{\{|\}\}|\{(\w+)\}/g, function(m, n) {
				if (m == "{{") {
					return "{";
				}
				if (m == "}}") {
					return "}";
				}
				return values[n];
			});
		};
	}

	/**
	 * private: bind events
	 */
	var _fnBindEvents = function() {

		// find containers
		$('body').on(_oEventsNames.reload, _sContainerSlct, function() {

			// current container
			var $this = $(this);
			// get data
			oData = {
				'count': $this.data('count')
			}

			// mark loading
			$this.addClass('lt-loading');

			// do ajax request
			$.ajax({
				url: _sGrabberUrl,
				dataType: "json",
				method: "POST",
				data: oData,
				context: $this
			})
			// tweets fetched correclty
			.done(function(data, textStatus, jqXHR) {

				// current container
				var $this = $(this),
					// get tweets from data
					aoTweets = 'tweets' in data ? data['tweets'] : [],
					// element constructor
					sElementConstructor = '';

				// detect type
				if ($this.is("ul")) {
					sElementConstructor = '<li />';
				}

				// check for data
				if (aoTweets.length && sElementConstructor.length) {

					// empty
					$this.empty();

					// cycle and append
					for (oTweet in aoTweets) {

						// get text
						var sText = aoTweets[oTweet]['text'];
						// format and link text
						if (_bLinkText) {
							sText = twttr.txt.autoLink(sText);
						}

						// get date
						var sTimestamp = aoTweets[oTweet]['created_timestamp'],
							sDate = LiveTweets.Date.fnFormat(_sDateFormat, sTimestamp);

						// prepare html
						var oHtml = _sTweetFormat.format({
							'date': sDate,
							'text': sText
						})

						// append
						$this.append($(sElementConstructor, {
							html: oHtml
						}));

					}

				}

			})
			// fail
			.fail(function(jqXHR, textStatus, errorThrown) {

			})
			// always
			.always(function() {

				// remove loading class
				$this.removeClass('lt-loading');

			});

		});

	},

		/**
		 * private: do request
		 */
		_fnReload = function() {

			// trigger reload
			$(_sContainerSlct).trigger(_oEventsNames.reload);

			// recall after 1000ms
			setTimeout(_fnReload, _iReloadTime);

		};

	/**
	 * document Ready
	 */
	$(document).ready(function() {

		// bind events
		_fnBindEvents();

		// call first request
		_fnReload();

	});

}(window.LiveTweets = window.LiveTweets || {}, jQuery));