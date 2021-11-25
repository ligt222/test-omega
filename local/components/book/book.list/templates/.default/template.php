<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form action="<?=$APPLICATION->GetCurPage();?>" method="post" id="form_pl">
    <input type="text" id="id_group" name="group" value="" placeholder="id группы">
    <input type="submit" value="Найти" class="sub">
    <div style="width: 100%">
        <table style="width: 100%">
            <thead>
            <tr>
                <td align="center" style="color: darkgrey">ID</td>
                <td align="center" style="color: darkgrey">Название книги</td>
                <td align="center" style="color: darkgrey">Автор</td>
                <td align="center" style="color: darkgrey">Название каталога</td>

            </tr>
            </thead>
            <tbody>
            <?foreach ($arResult["ITEMS"] as $item) { ?>
                <tr>
                    <td align="center"><?=$item["ID"]; ?></td>
                    <td align="center"><?=$item["NAME_BOOK"]; ?></td>
                    <td align="center"><?=$item["AUTHOR"]; ?></td>
                    <td align="center"><?=$item["NAME_CATALOG"]; ?> (ID - <?=$item["ID_CATALOG"]?>)</td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>

</form>




