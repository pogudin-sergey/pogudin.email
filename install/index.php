<?php
IncludeModuleLangFile(__FILE__);

if(class_exists("pogudin_email")) return;

class pogudin_email extends CModule
{
	const MODULE_ID = "pogudin.email";
	const MODULE_CODE_LANG = "POGUDIN_EMAIL_";
	const MODULE_STRUCTURE = "\\Pogudin\\Email";

	var $MODULE_PATH;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	var $errors;

	function __construct()
	{
		$arModuleVersion = array();

        $this->MODULE_PATH = realpath(__DIR__.'/../');

		include($this->MODULE_PATH."/install/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = $this->GetMessage("MODULE_NAME");
		$this->MODULE_DESCRIPTION = $this->GetMessage("MODULE_DESC");
		//$this->MODULE_CSS = $this->MODULE_PATH . "/styles.css";

        $this->PARTNER_NAME = GetMessage("POGUDIN_EMAIL_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("POGUDIN_EMAIL_PARTNER_URI");
	}

    function GetMessage($name) {
        return GetMessage(self::MODULE_CODE_LANG . $name);
    }

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else
		{
			RegisterModule(self::MODULE_ID);

			$eventManager = \Bitrix\Main\EventManager::getInstance();

			// Spam
			$eventManager->registerEventHandler("form", "onBeforeResultAdd", self::MODULE_ID, self::MODULE_STRUCTURE . "\\Spam", "formOnBeforeResultAdd");
			$eventManager->registerEventHandler("main", "OnEpilog", self::MODULE_ID, self::MODULE_STRUCTURE . "\\Spam", "OnEpilog");

			return true;
		}
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->unRegisterEventHandler("form", "onBeforeResultAdd", self::MODULE_ID, self::MODULE_STRUCTURE . "\\Spam", "formOnBeforeResultAdd");
		$eventManager->unRegisterEventHandler("main", "OnEpilog", self::MODULE_ID, self::MODULE_STRUCTURE . "\\Spam", "OnEpilog");

		UnRegisterModule(self::MODULE_ID);

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($this->MODULE_PATH . "/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		//DeleteDirFiles($this->MODULE_PATH."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		return true;
	}

	function DoInstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$POST_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if($POST_RIGHT == "W")
		{
			$step = IntVal($step);
			if($step < 2)
			{
				$APPLICATION->IncludeAdminFile($this->GetMessage("MODULE_INST_TITLE"), $this->MODULE_PATH."/install/inst1.php");
			}
			elseif($step==2)
			{
				if($this->InstallDB())
				{
					$this->InstallFiles(array());
				}
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile($this->GetMessage("MODULE_INST_TITLE"), $this->MODULE_PATH."/install/inst2.php");
			}
		}
	}

	function DoUninstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$POST_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if($POST_RIGHT == "W")
		{
			$step = IntVal($step);
			if($step < 2)
			{
				$APPLICATION->IncludeAdminFile($this->GetMessage("MODULE_UNINST_TITLE"), $this->MODULE_PATH."/install/uninst1.php");
			}
			elseif($step == 2)
			{
				$this->UnInstallDB(array(
					"save_tables" => $_REQUEST["save_tables"],
				));
				$this->UnInstallFiles();
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile($this->GetMessage("MODULE_UNINST_TITLE"), $this->MODULE_PATH."/install/uninst2.php");
			}
		}
	}
}
?>