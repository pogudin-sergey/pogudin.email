<?php
use \Bitrix\Main;

IncludeModuleLangFile(__FILE__);

class CBitrixComponentPogudinEmail extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams['RUN_COMPONENT'] = true;

        if(!CModule::IncludeModule('pogudin.email')) {
            ShowError(GetMessage('POGUDIN_PROTECTED_EMAIL_CLASS_NOMODULE'));
            $arParams['RUN_COMPONENT'] = false;
        }

        $aAllowType = array('TEXT', 'LINK');

        if (!check_email($arParams['EMAIL'])) {
            ShowError(GetMessage('POGUDIN_PROTECTED_EMAIL_CLASS_EMAILMISS'));
            $arParams['RUN_COMPONENT'] = false;
        }

        if (!in_array($arParams['TYPE'], $aAllowType))
            $arParams['TYPE'] = $aAllowType[0];

        return $arParams;
    }

    public function executeComponent()
    {
        if (!$this->arParams['RUN_COMPONENT'])
            return false;

        $sSiteId = $this->getSiteId();
        $salt = Main\Config\Option::get('main', 'server_uniq_id', $sSiteId.SITE_TEMPLATE_PATH, $this->getSiteId());
        $this->arResult["VARNAME"] = md5(serialize($this->arParams) . $salt);

        $sEmailExplode = explode('@', $this->arParams['EMAIL']);
        $this->arResult["EMAIL_ENCODE_USERNAME"] = base64_encode($sEmailExplode[0]);
        $this->arResult["EMAIL_ENCODE_DOMAIN"] = base64_encode($sEmailExplode[1]);
        $this->includeComponentTemplate();
    }
}
