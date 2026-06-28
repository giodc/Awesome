/**
 * Lightweight post sharing — copy link and Web Share API.
 */
(function () {
	'use strict';

	var shareBlock = document.querySelector('.single-post__share');

	if (!shareBlock) {
		return;
	}

	var copyBtn = shareBlock.querySelector('[data-share="copy"]');
	var nativeBtn = shareBlock.querySelector('[data-share="native"]');
	var nativeItem = shareBlock.querySelector('.single-post__share-native');
	var feedback = shareBlock.querySelector('.single-post__share-feedback');
	var strings = window.awesomeShare || {};
	var copiedText = strings.copied || 'Link copied';
	var copyErrorText = strings.copyError || 'Could not copy link';

	if (nativeItem && nativeBtn && navigator.share) {
		nativeItem.hidden = false;
	}

	function showFeedback(message) {
		if (!feedback) {
			return;
		}

		feedback.textContent = message;
		feedback.hidden = false;

		window.setTimeout(function () {
			feedback.hidden = true;
			feedback.textContent = '';
		}, 2500);
	}

	function copyToClipboard(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}

		return new Promise(function (resolve, reject) {
			var textarea = document.createElement('textarea');
			textarea.value = text;
			textarea.setAttribute('readonly', '');
			textarea.style.position = 'absolute';
			textarea.style.left = '-9999px';
			document.body.appendChild(textarea);
			textarea.select();

			try {
				document.execCommand('copy');
				document.body.removeChild(textarea);
				resolve();
			} catch (error) {
				document.body.removeChild(textarea);
				reject(error);
			}
		});
	}

	if (copyBtn) {
		copyBtn.addEventListener('click', function () {
			var url = copyBtn.getAttribute('data-url') || window.location.href;

			copyToClipboard(url)
				.then(function () {
					showFeedback(copiedText);
				})
				.catch(function () {
					showFeedback(copyErrorText);
				});
		});
	}

	if (nativeBtn && navigator.share) {
		nativeBtn.addEventListener('click', function () {
			var url = nativeBtn.getAttribute('data-url') || window.location.href;
			var title = nativeBtn.getAttribute('data-title') || document.title;

			navigator.share({ title: title, url: url }).catch(function () {
				// User cancelled or share failed silently.
			});
		});
	}
})();
