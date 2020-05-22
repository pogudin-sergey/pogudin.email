<?php
namespace Pogudin\Email;

/*
 * Antispam core by Recaptcha3
 */

use Bitrix\Main\Config\Option as Option;

Class Recaptcha3 implements SpamEngine
{
	const VERIFY_FIELD_ID = 'INPUT_SECURITY_RE3';

	private static $score = 0;
	private static $settings = [];

	static function initOptions()
	{
		// todo to setting
		$recaptcha_success_score = floatval(Option::get(self::MODULE_ID, 'recaptcha_success_score', '50'));	// percent
		$recaptcha_success_score = $recaptcha_success_score / 100;

		self::$settings = [
			'SUCCESS_SCORE' => $recaptcha_success_score,
			'PRIVATE_KEY' => Option::get(self::MODULE_ID, 'recaptcha_private_key', ''),
			'PUBLIC_KEY' => Option::get(self::MODULE_ID, 'recaptcha_public_key', ''),
			'CSS_SELECTOR' => ['#contact-form .col-md-12:last-child'],
			'ACTION' => 'index',
		];
	}

	/**
	 * Add additional code
	 */
	static function render()
	{
		self::initOptions();

		$aCssQuery = ['#contact-form .col-md-12:last-child']; // todo
		$action = 'index';

		$asset = \Bitrix\Main\Page\Asset::getInstance();
		$asset->addJs('https://www.google.com/recaptcha/api.js?render='.self::PUBLIC_KEY);
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
					<?
					$recaptcha3_show_rights = Option::get(self::MODULE_ID, 'recaptcha3_show_rights', 'Y') === 'Y' ? true : false;
					if ($recaptcha3_show_rights) {
						?>
						var newP = document.createElement('p');
						newP.innerHTML = "This site is protected by reCAPTCHA and the Google <a href=\"https://policies.google.com/privacy\">Privacy Policy</a> and <a href=\"https://policies.google.com/terms\">Terms of Service</a> apply.";
						refElem.parentNode.insertBefore(newP, newInput.nextSibling);
						<?
					}
					?>
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
