<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule("pogudin.email"))
    return;

$aTypes = array(
    'TEXT' => GetMessage("POGUDIN_PROTECTED_EMAIL_PARAM_TYPE_TEXT"),
    'LINK' => GetMessage("POGUDIN_PROTECTED_EMAIL_PARAM_TYPE_LINK")
);

$arComponentParameters = array(
    "GROUPS" => array(
        "MAIN" => array(
            "NAME" => GetMessage("POGUDIN_PROTECTED_EMAIL_PARAM_MAINGROUP"),
        ),
    ),
    "PARAMETERS" => array(
        "EMAIL" => Array(
            "PARENT" => "MAIN",
            "NAME" => GetMessage("POGUDIN_PROTECTED_EMAIL_PARAM_EMAIL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ),
        "TYPE" => array(
            "PARENT" => "MAIN",
            "NAME" => GetMessage("POGUDIN_PROTECTED_EMAIL_PARAM_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $aTypes,
            "REFRESH" => "N",
        ),
        "RAND_STRING" => Array(
            "PARENT" => "MAIN",
            "NAME" => GetMessage("POGUDIN_PROTECTED_EMAIL_PARAM_RAND_STRING"),
            "TYPE" => "STRING",
            "DEFAULT" => randString(
                10,
                "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ",
                "0123456789"
            )
        ),
        //"CACHE_TIME"  =>  Array(),
    )
);
