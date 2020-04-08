<?php
namespace Pogudin\Email;

/*
 * Antispam system
 */

use Bitrix\Main\Context;
use Bitrix\Main as Main;

Class Spam
{
    const FORM_INPUT_NAME = 'spam_key_svc514vd9ivj5o4xzfg9swj';
    const SESSION_KEY_NAME = 'pogudin_expansion_spam_key_value';

    function getCurrentKeyValue() {
        if (empty($_SESSION[self::SESSION_KEY_NAME])) {
            $_SESSION[self::SESSION_KEY_NAME] = randString(16);
        }

        return $_SESSION[self::SESSION_KEY_NAME];
    }

	static function OnEpilog()
    {
        if (!defined('ADMIN_SECTION') || ADMIN_SECTION !== true)
	        static::render();
	}

    /**
     * Add additional input field in forms
     */
    static function render() {
        ?>
        <script>
            (function (window, document) {
                var input_name = "<?=self::FORM_INPUT_NAME?>";

                function pogudinAntiSpam() {
                    var forms = document.querySelectorAll("form");
                    forms.forEach(function (form) {
                        var input = form.querySelector("input[name='"+input_name+"']");
                        if (input === null) {
                            var new_input = document.createElement('input');
                            new_input.name = input_name;
                            new_input.type = "hidden";
                            new_input.value = "<?=self::getCurrentKeyValue()?>";
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
		    !array_key_exists(self::FORM_INPUT_NAME, $_POST) ||
		    $_SESSION[self::SESSION_KEY_NAME] !== $_POST[self::FORM_INPUT_NAME]
	    ) {
		    Main\Diag\Debug::writeToFile($_POST, "Blocked spamer [$WEB_FORM_ID]", "__spam_form.log");
		    $APPLICATION->ThrowException('You are spamer!');
	    }
    }
}
