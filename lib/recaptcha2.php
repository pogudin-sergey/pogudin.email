<?php
namespace Pogudin\Email;

/*
 * Antispam core by Recaptcha2
 * See: https://developers.google.com/recaptcha/docs/display
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
		<script src="//www.google.com/recaptcha/api.js" async defer data-skip-moving="true"></script>
		<script>
			BX.ready(function() {
				var forms = document.querySelectorAll("form");
				forms.forEach(function (form) {
					var newDiv = document.createElement('div');
					newDiv.className = 'g-recaptcha';
					newDiv['data-sitekey'] = '<?=self::$settings['PUBLIC_KEY']?>';
					//form.parentNode.insertBefore(newDiv, refElem.nextSibling);
					form.appendChild(newDiv);
				}
				?>
			});
		</script>
		<?php
	}

	static function verify()
	{

	}
}
