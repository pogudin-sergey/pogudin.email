<?php
namespace Pogudin\Email;

/*
 * Antispam system
 */

use Bitrix\Main as Main;
use Bitrix\Main\Config\Option as Option;

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

	static function OnEpilog()
	{
		if (
				self::isEnable()
				&& !(defined('ADMIN_SECTION') && ADMIN_SECTION === true)
		) {
			self::getProtectorClass()::render();
		}
	}

	/**
	 * Reject forms without valid key
	 */
	static function formOnBeforeResultAdd($WEB_FORM_ID, &$arFields, &$arrVALUES)
	{
		global $APPLICATION;

		if (!self::getProtectorClass()::verify()) {
			if(Option::get(self::MODULE_ID, 'log', 'N') === 'Y') {
				$filename = Option::get(self::MODULE_ID, 'log_filename', '__spam_form.log');
				Main\Diag\Debug::writeToFile($_POST, "Blocked spamer [$WEB_FORM_ID]", $filename);
			}

			$APPLICATION->ThrowException(self::getSpamMessage());
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
