<?php
namespace Pogudin\Email;

/*
 * Antispam core by Recaptcha3
 */

Class Recaptcha3
{
	const VERIFY_FIELD_ID = 'INPUT_SECURITY_RE3';

	private static $score = 0;
	/**
	 * Add additional code
	 */
	static function render()
	{
		?>
		<style>
			.grecaptcha-badge {
				display: none;
			}
		</style>
		<script>
			function addCaptchaHTML(container) {
				var refElem = document.querySelector(container);
				if (refElem) {
					var newInput = document.createElement('input');
					newInput.type = 'hidden';
					newInput.className = '<?=self::VERIFY_FIELD_ID?>';
					newInput.name = '<?=self::VERIFY_FIELD_ID?>';
					refElem.parentNode.insertBefore(newInput, refElem.nextSibling);

					// todo setting option add text
					var newP = document.createElement('p');
					newP.innerHTML = "This site is protected by reCAPTCHA and the Google <a href=\"https://policies.google.com/privacy\">Privacy Policy</a> and <a href=\"https://policies.google.com/terms\">Terms of Service</a> apply.";
					refElem.parentNode.insertBefore(newP, newInput.nextSibling);
				}
			}

			BX.ready(function () {
				var addTo = <?=\CUtil::PhpToJSObject($aCssQuery)?>;
				addTo.forEach(function (selector) {
					addCaptchaHTML(selector);
				});

				grecaptcha.ready(function () {
					grecaptcha.execute('<?=self::PUBLIC_KEY?>', {action: '<?=$action?>'})
						.then(function (token) {
								var elements = document.querySelectorAll(".<?=self::VERIFY_FIELD_ID?>");
								for (let key in elements) {
									elements[key].value = token;
								}
						});
				});
			});
		</script>
		<?
	}
}
