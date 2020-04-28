<?php
namespace Pogudin\Email;

/*
 * Antispam system
 */

use Bitrix\Main\Context;
use Bitrix\Main as Main;

Class Spam
{
	const SESSION_KEY_NAME = 'pogudin_expansion_spam_key_value';

	static function getRandString() {
		return randString(rand(6,32));
	}

	static function getCurrentKeyValue() {
		if (empty($_SESSION[self::SESSION_KEY_NAME])) {
			$_SESSION[self::SESSION_KEY_NAME] = self::getRandString();
		}

		return $_SESSION[self::SESSION_KEY_NAME];
	}

	static function isEnable()
	{
		static $allow = null;

		if (is_null($allow)) {
			$allow = Main\Config\Option::get('pogudin.email', 'allow', 'N');
		}

		return ($allow === 'Y');
	}

	static function OnEpilog()
	{
		if (
				self::isEnable()
				&& !(defined('ADMIN_SECTION') && ADMIN_SECTION === true)
		) {
			static::render();
		}
	}

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
	 * Reject forms without Javascript User Support
	 */
	static function formOnBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
	{
		global $APPLICATION;

		if (
			!array_key_exists(self::SESSION_KEY_NAME, $_SESSION) ||
			!array_key_exists(self::getCurrentKeyValue(), $_POST)
		) {
			// todo on/off debug
			Main\Diag\Debug::writeToFile($_POST, "Blocked spamer [$WEB_FORM_ID]", "__spam_form.log");
			$APPLICATION->ThrowException('You are spamer!');
		}
	}
}
