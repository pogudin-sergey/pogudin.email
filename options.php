<?
##############################################
# Author: Pogudin Sergey                     #
# Copyright (c) 1984-2020                    #
# http://pogudin.pro                         #
# mailto:dev@pogudin.pro                     #
##############################################

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

$module_id = 'pogudin.email';
CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);

if($MOD_RIGHT>='R') {

	// set up form
	$arAllOptions = array(
		array('allow', Loc::getMessage('POGUDIN_EMAIL_OPTIONS_ALLOW'), 'Y', array('checkbox')),
		array(
			'type',
			Loc::getMessage("POGUDIN_EMAIL_OPTIONS_TYPE"),
			'AJAX',
			array("selectbox", array(
				'AJAX' => Loc::getMessage('POGUDIN_EMAIL_OPTIONS_TYPE_AJAX'),
				'RECAPCHA' => Loc::getMessage('POGUDIN_EMAIL_OPTIONS_TYPE_RECAPTCHA3'),
			))
		),
		array('log', Loc::getMessage('POGUDIN_EMAIL_OPTIONS_LOG'), 'N', array('checkbox')),
		array('log_filename', Loc::getMessage('POGUDIN_EMAIL_OPTIONS_LOG_FILENAME'), '__spam.log', array('text')),
	);
	
	$arRecaptchaOptions = array(
		array('recaptcha_success_score', Loc::getMessage('POGUDIN_EMAIL_OPTIONS_RECAPTCHA3_SUCCESS_SCORE'), 60, array('text')),
		array('recaptcha3_show_rights', Loc::getMessage('POGUDIN_EMAIL_OPTIONS_RECAPTCHA3_SHOW_RIGHTS'), 'Y', array('checkbox')),
	);

	if ($MOD_RIGHT >= 'Y' || $USER->IsAdmin()) {

		if ($REQUEST_METHOD == 'GET'
				&& strlen($RestoreDefaults) > 0
				&& check_bitrix_sessid()
		) {
			COption::RemoveOption($module_id);

			$z = CGroup::GetList($v1 = 'id', $v2 = 'asc', array('ACTIVE' => 'Y', 'ADMIN' => 'N'));
			while ($zr = $z->Fetch()) {
				$APPLICATION->DelGroupRight($module_id, array($zr['ID']));
			}
		}

		if ($REQUEST_METHOD == 'POST'
				&& strlen($Update) > 0
				&& check_bitrix_sessid()
		) {
			foreach (array_merge($arAllOptions, $arRecaptchaOptions) as $option) {
				$name = $option[0];
				$val = ${$name};

				// Allow only 1-100 value
				if ($name === 'recaptcha_success_score') {
					if ($val > 100) {
						$val = 100;
					} else if ($val < 1) {
						$val = 1;
					}
				}

				if ($option[3][0] == 'checkbox' && $val != 'Y')
					$val = 'N';

				COption::SetOptionString($module_id, $name, $val, $option[1]);
			}
		}
	}

	$aTabs = array();
	$aTabs[] = array('DIV' => 'set', 'TAB' => Loc::getMessage('MAIN_TAB_SET'), 'ICON' => 'wiki_settings', 'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'));

	if (COption::GetOptionString($module_id, 'type') === 'RECAPCHA') {
		$aTabs[] = array('DIV' => 'recaptcha3', 'TAB' => Loc::getMessage('RECAPTCHA3_TAB_SET'), 'ICON' => 'wiki_settings', 'TITLE' => Loc::getMessage('RECAPTCHA3_TAB_SET'));
	}

	$aTabs[] = array('DIV' => 'rights', 'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'), 'ICON' => 'wiki_settings', 'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS'));

	$tabControl = new CAdminTabControl('tabControl', $aTabs);
	$tabControl->Begin();
	?>
	<form method="POST"
				action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>"
				name="wiki_settings">
		<?
		$tabControl->BeginNextTab();
		__AdmSettingsDrawList('pogudin.email', $arAllOptions);

		if (COption::GetOptionString($module_id, 'type') === 'RECAPCHA') {
			$tabControl->BeginNextTab();
			__AdmSettingsDrawList('pogudin.email', $arRecaptchaOptions);
		}

		$tabControl->BeginNextTab();
		require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');

		$tabControl->Buttons();
		?>
		<script language="JavaScript">
			function RestoreDefaults() {
					if (confirm('<?echo AddSlashes(Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>'))
							window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo rawurlencode($mid) . "&" . bitrix_sessid_get();?>";
			}
		</script>
		<input type="submit" name="Update" <? if ($MOD_RIGHT < 'W') echo "disabled" ?>
					 value="<? echo Loc::getMessage('MAIN_SAVE') ?>">
		<input type="reset" name="reset" value="<? echo Loc::getMessage('MAIN_RESET') ?>">
		<input type="hidden" name="Update" value="Y">
		<?= bitrix_sessid_post(); ?>
		<input type="button" <? if ($MOD_RIGHT < 'W') echo "disabled" ?>
			title="<? echo Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS') ?>" OnClick="RestoreDefaults();"
			value="<? echo Loc::getMessage('MAIN_RESTORE_DEFAULTS') ?>">
		<? $tabControl->End(); ?>
	</form>
	<?
}
