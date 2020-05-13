<?php
namespace Pogudin\Email;

/*
 * Antispam core by Ajax
 */

Class Ajax
{
	const SESSION_KEY_NAME = 'pogudin_expansion_spam_key_value';

	/**
	 * Add additional input field in forms
	 */
	static function render() {
		?>
		<script>
			(function (window, document) {
				var input_name = "<?=self::getCurrentKeyValue()?>";

				function pogudinAntiSpam() {
					var forms = document.querySelectorAll("form");
					forms.forEach(function (form) {
						var input = form.querySelector("input[name='"+input_name+"']");
						if (input === null) {
							var new_input = document.createElement('input');
							new_input.name = input_name;
							new_input.type = "hidden";
							// TODO проверить работу на композитном режиме
							new_input.value = "<?=self::getRandString()?>";
							form.appendChild(new_input);
						}
					})
				}

				document.addEventListener("DOMContentLoaded", function () {
						pogudinAntiSpam();
				});

				if (typeof BX !== "undefined") {
						BX.addCustomEvent(window, 'onAjaxSuccess', function () {
								pogudinAntiSpam();
						});
				}

				if (typeof $ !== "undefined") {
						$(document).ajaxSuccess(function () {
								pogudinAntiSpam();
						});
				}
			})(window, document);
		</script>
		<?
	}

	/**
	 * Verify on spam
	 */
	static function verify()
	{
		return (
			array_key_exists(self::SESSION_KEY_NAME, $_SESSION) &&
			array_key_exists(self::getCurrentKeyValue(), $_POST)
		);
	}

	static function getRandString() {
		return randString(rand(6,32));
	}

	static function getCurrentKeyValue() {
		if (empty($_SESSION[self::SESSION_KEY_NAME])) {
			$_SESSION[self::SESSION_KEY_NAME] = self::getRandString();
		}

		return $_SESSION[self::SESSION_KEY_NAME];
	}
}
