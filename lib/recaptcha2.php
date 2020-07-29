<?php
namespace Pogudin\Email;

/*
 * Antispam core by Recaptcha2
 */

use Bitrix\Main\Config\Option as Option;

Class Recaptcha2 implements SpamEngine
{
	private static $settings = [];

	static function initOptions()
	{
		$recaptcha_success_score = floatval(Option::get(self::MODULE_ID, 'recaptcha2_success_score', '60'));	// percent
		$recaptcha_success_score = $recaptcha_success_score / 100;

		self::$settings = [
			'SUCCESS_SCORE' => $recaptcha_success_score,
			'PRIVATE_KEY' => Option::get(self::MODULE_ID, 'recaptcha2_private_key', ''),
			'PUBLIC_KEY' => Option::get(self::MODULE_ID, 'recaptcha2_public_key', ''),
			'ACTION' => 'index',
			'VERIFY_FIELD_ID' => 'INPUT_SECURITY_RE2',
		];
	}

	static function render()
	{
		//
	}

	static function verify()
	{

	}
}
