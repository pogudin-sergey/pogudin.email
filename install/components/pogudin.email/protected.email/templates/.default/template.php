<?php
$this->setFrameMode(true);
$strContID = 'contprotect_'.$this->randString();
$class = (strlen($arParams['CLASS']) > 0) ? " class=\"{$arParams['CLASS']}\"" : '';

if ($arParams['TYPE'] == 'LINK') {
    ?><a href="#"<?=$class?> id="<?=$arResult['VARNAME']?>"></a><?
} else {
    ?><span id="<?=$arResult['VARNAME']?>"<?=$class?>></span><?
}
?>
<script>
    var e<?=$arResult['VARNAME']?>F = "<?=$arResult['EMAIL_ENCODE_USERNAME']?>";
    var e<?=$arResult['VARNAME']?>S = "<?=$arResult['EMAIL_ENCODE_DOMAIN']?>";
    EProtected.setEP("<?=$arResult['VARNAME']?>", <? echo ($arParams['TYPE'] == 'LINK') ? 'true' : 'false' ?>);
</script>
