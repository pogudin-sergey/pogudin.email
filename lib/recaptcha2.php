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
			var re2onloadCallback = function() {
				var forms = document.querySelectorAll("form");
				forms.forEach(function (form) {
					var newDiv = document.createElement('div');
					newDiv.id = 0;	// todo unique
					newDiv.className = 'g-recaptcha';
					newDiv['data-sitekey'] = '<?=self::$settings['PUBLIC_KEY']?>';
					//form.parentNode.insertBefore(newDiv, refElem.nextSibling);
					form.appendChild(newDiv);

					widgetId1 = grecaptcha.render('exampleid1', {
						'sitekey' : 'your_site_key',
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
