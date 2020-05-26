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
		$recaptcha_success_score = floatval(Option::get(self::MODULE_ID, 'recaptcha_success_score', '60'));	// percent
		$recaptcha_success_score = $recaptcha_success_score / 100;

		self::$settings = [
			'SUCCESS_SCORE' => $recaptcha_success_score,
			'PRIVATE_KEY' => Option::get(self::MODULE_ID, 'recaptcha_private_key', ''),
			'PUBLIC_KEY' => Option::get(self::MODULE_ID, 'recaptcha_public_key', ''),
			'ACTION' => 'index',
			'VERIFY_FIELD_ID' => 'INPUT_SECURITY_RE3',
		];
	}

	/**
	 * Add additional code
	 */
	static function render()
	{
		self::initOptions();

		$asset = \Bitrix\Main\Page\Asset::getInstance();
		$asset->addJs('https://www.google.com/recaptcha/api.js?render=' . self::$settings['PUBLIC_KEY']);
		?>
		<style>
			.grecaptcha-badge {
				display: none;
			}
		</style>
		<script>
			BX.ready(function () {
				var forms = document.querySelectorAll("form");
				forms.forEach(function (form) {
					var newInput = document.createElement('input');
					newInput.type = 'hidden';
					newInput.className = '<?=self::$settings['VERIFY_FIELD_ID']?>';
					newInput.name = '<?=self::$settings['VERIFY_FIELD_ID']?>';
					//form.parentNode.insertBefore(newInput, refElem.nextSibling);
					form.appendChild(newInput);

					<?
					$recaptcha3_show_rights = Option::get(self::MODULE_ID, 'recaptcha3_show_rights', 'Y') === 'Y' ? true : false;
					if ($recaptcha3_show_rights) {
						?>
						var newP = document.createElement('p');
						newP.innerHTML = "This site is protected by reCAPTCHA and the Google <a href=\"https://policies.google.com/privacy\">Privacy Policy</a> and <a href=\"https://policies.google.com/terms\">Terms of Service</a> apply.";
            //form.parentNode.insertBefore(newP, newInput.nextSibling);
            form.appendChild(newP);
						<?
					}
					?>
				});

				grecaptcha.ready(function () {
					grecaptcha.execute('<?=self::$settings['PUBLIC_KEY']?>', {action: '<?=self::$settings['ACTION']?>'})
						.then(function (token) {
								var elements = document.querySelectorAll(".<?=self::$settings['VERIFY_FIELD_ID']?>");
                elements.forEach(function (element) {
                    element.value = token;
                });
						});
				});
			});
		</script>
		<?
	}
}
