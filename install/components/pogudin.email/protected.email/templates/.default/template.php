<?php
$this->setFrameMode(true);

if ($arParams['TYPE'] == 'LINK') {
    ?><a href="#" id="<?=$arResult['VARNAME'] ?>"></a><?
} else {
    ?><span id="<?=$arResult['VARNAME'] ?>"></span><?
}
?>
<script>
    var e<?=$arResult['VARNAME']?>F = "<?=$arResult['EMAIL_ENCODE_USERNAME']?>";
    var e<?=$arResult['VARNAME']?>S = "<?=$arResult['EMAIL_ENCODE_DOMAIN']?>";
    EProtected.setEP("<?=$arResult['VARNAME']?>", <? echo ($arParams['TYPE'] == 'LINK') ? 'true' : 'false' ?>);
</script>
