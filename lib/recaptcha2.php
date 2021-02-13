<?php
namespace Pogudin\Email;

/**
 * Antispam core by Recaptcha2
 * @see https://developers.google.com/recaptcha/docs/display?hl=ru
 */

use Bitrix\Main\Config\Option as Option;

Class Recaptcha2 implements SpamEngine
{
	private static $settings = [];

	static function initOptions()
	{
		self::$settings = [
			'PUBLIC_KEY' => Option::get(self::MODULE_ID, 'recaptcha2_public_key', ''),
		];
	}

	static function render()
	{
		self::initOptions();
		?>
		<script>
			var verifyCallback = function(response) {
				alert(response);
			};

			var re2onloadCallback = function() {
				var forms = document.querySelectorAll("form");
				var counter = 0;
				forms.forEach(function (form) {
					counter++;
					var newDiv = document.createElement('div');
					newDiv.id = "recaptcha2_div" + counter;
					newDiv.className = 'g-recaptcha';
					//form.parentNode.insertBefore(newDiv, refElem.nextSibling);
					form.appendChild(newDiv);

					widgetId1 = grecaptcha.render(newDiv.id, {
						'sitekey' : '<?=self::$settings['PUBLIC_KEY']?>',
						'callback' : verifyCallback,
						'theme' : 'light'	// dark, to options
					});
				}
			};
		</script>
		<script data-skip-moving="true" src="https://www.google.com/recaptcha/api.js?onload=re2onloadCallback&render=explicit" async defer></script>
		<?php
	}

	static function verify()
	{

	}
}
