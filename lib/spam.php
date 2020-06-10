<?php
namespace Pogudin\Email;

/*
 * Antispam system
 */

use Bitrix\Main as Main;
use Bitrix\Main\Config\Option as Option;

interface SpamEngine
{
	const MODULE_ID = 'pogudin.email';
	static function render();
	static function verify();
}

Class Spam
{
	const MODULE_ID = 'pogudin.email';
	static $protectedObject = null;

	static function isEnable()
	{
		static $allow = null;

		if (is_null($allow)) {
			$allow = Option::get(self::MODULE_ID, 'allow', 'N');
		}

		return ($allow === 'Y');
	}

	static function getProtectorClass() {
		static $currentObject = null;

		if (empty($currentObject)) {
			switch (Option::get(self::MODULE_ID, 'type', 'AJAX')) {
				case 'RECAPCHA':
					$currentObject = Recaptcha3::class;
					break;

				case 'AJAX':
				default:
					$currentObject = Ajax::class;
			}
		}

		return $currentObject;
	}

	static function OnEndBufferContent(&$content)
	{
		if (
				self::isEnable()
				&& !(defined('ADMIN_SECTION') && ADMIN_SECTION === true)
		) {
			ob_start();
			self::getProtectorClass()::render();
			$result = ob_get_clean();

			$content = str_replace("</body>", "$result\n</body>", $content);
		}
	}

	/**
	 * Reject forms without valid key
	 */
	static function formOnBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
	{
		global $APPLICATION;

		if (!self::getProtectorClass()::verify()) {
			self::log($_POST, "Blocked spamer [$WEB_FORM_ID]");
			$APPLICATION->ThrowException(self::getSpamMessage());
		}
	}

	static function log($message, $title = '') {
		if(Option::get(self::MODULE_ID, 'log', 'N') === 'Y') {
			$filename = Option::get(self::MODULE_ID, 'log_filename', '__spam_form.log');
			Main\Diag\Debug::writeToFile($message, $title, $filename);
		}
	}

	/**
	 * Return message for spammers
	 */
	static function getSpamMessage()
	{
		return 'You are spamer!';
	}
}
