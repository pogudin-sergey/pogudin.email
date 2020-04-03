<?
namespace \Pogudin\Email\Expansion;

/*
 * Antispam system
 */

use Bitrix\Main\Context;
use Bitrix\Main as Main;

Class Spam
{
    const FORM_INPUT_NAME = 'spam_key_svc514vd9ivj5o4xzfg9swj';
    const SESSION_KEY_NAME = 'pogudin_expansion_spam_key_value';
    const FORM_ALLOW = array(3);

    function getCurrentKeyValue() {
        if (empty($_SESSION[self::SESSION_KEY_NAME])) {
            $_SESSION[self::SESSION_KEY_NAME] = randString(16);
        }

        return $_SESSION[self::SESSION_KEY_NAME];
    }

	function OnEpilog()
    {

	}

    /**
     * Add additional input field in forms
     */
    function render() {
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
}
