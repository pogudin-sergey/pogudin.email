<?php
namespace Pogudin\Email;

/*
 * Antispam core by Recaptcha3
 */

use Bitrix\Main\Config\Option as Option;

Class Recaptcha3 implements SpamEngine
{
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

	static function verify() {
		self::initOptions();

		if (isset($_POST[self::$settings['VERIFY_FIELD_ID']]) && strlen($_POST[self::$settings['VERIFY_FIELD_ID']]) > 0) {
			$decoded_response = self::getResult();

			if ($decoded_response && $decoded_response->success
					&& $decoded_response->action == self::$settings['ACTION']
					&& $decoded_response->score > 0)
			{
				$result = (floatval($decoded_response->score) >= self::$settings['SUCCESS_SCORE']);

				// Debug
				Spam::log("reCaptcha score." .
					"\nRESPONSE: " . print_r($decoded_response, true) .
					"\nRESULT: " . print_r($result, true)
				);

				return $result;

			} else if (
				is_array($decoded_response)
				&& is_array($decoded_response['error-codes'])
				&& in_array('timeout-or-duplicate', $decoded_response['error-codes'])
			) {
				return true;
			} else {
				return true;
			}
		}

		return false;
	}

	private static function getResult() {
		$post_data = [
			'secret' => self::$settings['PRIVATE_KEY'],
			'response' => $_POST[self::$settings['VERIFY_FIELD_ID']],
			'remoteip' => $_SERVER['REMOTE_ADDR']
		];
		$url = 'https://www.google.com/recaptcha/api/siteverify';

		if (function_exists('curl_init')) {
			return self::sendReauestCurl($url, $post_data);
		} else {
			return self::sendReauestAlternate($url, $post_data);
		}
	}

	private static function sendReauestCurl($url, $post_data) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		if (!empty($response)) {
			$decoded_response = json_decode($response);
			if (json_last_error() === JSON_ERROR_NONE) {
				return $decoded_response;
			}
		}

		return false;
	}

	private static function sendReauestAlternate($url, $post_data) {
		$post_data = http_build_query($post_data);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $post_data
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents($url, false, $context);

		if (!empty($response)) {
			$decoded_response = json_decode($response);
			if (json_last_error() === JSON_ERROR_NONE) {
				return $decoded_response;
			}
		}

		return false;
	}

}
