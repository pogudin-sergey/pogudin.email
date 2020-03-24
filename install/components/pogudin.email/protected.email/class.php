<?php
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

        if (strlen($arParams['RAND_STRING']) == 0) {
            ShowError(GetMessage('POGUDIN_PROTECTED_EMAIL_CLASS_RANDSTRINGMISS'));
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

        $sEmailExplode = explode('@', $this->arParams['EMAIL']);
        $this->arResult["EMAIL_ENCODE_USERNAME"] = base64_encode($sEmailExplode[0]);
        $this->arResult["EMAIL_ENCODE_DOMAIN"] = base64_encode($sEmailExplode[1]);
        $this->arResult["VARNAME"] = md5($this->arParams['RAND_STRING']);
        $this->includeComponentTemplate();
    }
}
