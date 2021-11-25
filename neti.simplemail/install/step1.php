<?
global $APPLICATION;

if(CModule::IncludeModule("iblock"))
{
    $arIBlockSelect = [];
    $res = CIBlock::GetList(
        array(),
        array(
            "ACTIVE" => "Y",
            "CNT_ACTIVE" => "Y",
        ), true
    );
    while ($arRes = $res->Fetch()) {
        $arIBlockSelect[$arRes['ID']] = $arRes['NAME'];
    }
}
if (!check_bitrix_sessid()) return;
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="neti_simplemail">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="hidden" name="id" value="neti.simplemail">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">
    <p><?echo GetMessage("MODULE_NETI_SIMPLEMAIL_INSTALL_IB")?></p>
    <select name="iblock">
        <option><?echo GetMessage("MODULE_NETI_SIMPLEMAIL_SELECT_IB"); ?></option>
        <? foreach ($arIBlockSelect as $key=>$item) { ?>
            <option value="<?=$key?>"><?=$item?></option>
        <? } ?>
    </select>
    <input type="submit" name="inst" value="<?echo GetMessage("MODULE_INSTEP_BTN")?>">
</form>
