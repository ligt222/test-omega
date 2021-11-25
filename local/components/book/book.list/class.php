<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if (!Loader::includeModule("highloadblock")) {
    return;
}

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Query\Join;

class book_list extends CBitrixComponent
{
    function executeComponent($id_group = false)
    {
        global $APPLICATION;

        $arParams = $this->arParams;
        if ($_POST['group']) {
            $filter = array(
                "ID" => $_POST['group']
            );
        } else {
            $filter = array();
        }
        // if ($this->startResultCache(FALSE, array('NAV_PAGE' => $nav->getCurrentPage()))) {
        $arItems = array();

        $entityDataClass = self::getEntity("test_tabl_1"); // был бы модуль сделал бы через отдельный класс там где создается hlbl
        $entityClass = $entityDataClass::getEntity();


        $entityDataClassParent = self::getEntity("test_tabl_2"); // был бы модуль сделал бы через отдельный класс там где создается hlbl
        $entityClass->addField(
            (new Bitrix\Main\ORM\Fields\Relations\OneToMany('BOOKS', $entityDataClassParent, 'LINK_CATALOG'))->configureJoinType('inner')
        );
        $entityClassParent = $entityDataClassParent::getEntity();
        $entityClassParent->addField(
            (new Bitrix\Main\ORM\Fields\Relations\Reference('LINK_CATALOG', $entityDataClass, Join::on('this.UF_ID_NAME_CATALOG', 'ref.ID')))->configureJoinType('inner')
        );

        $rsData = $entityDataClass::getList(array(
            "select" => array(
                "ID",
                "UF_NAME_CATALOG",
                "BOOKS",
            ),
            //'offset' => $nav->getOffset(),
            //"limit" => $nav->GetLimit(),
            "order" => array("ID" => "ASC"),
            "filter" => $filter,
        ));
        while ($arItem = $rsData->fetchObject()) {
            foreach ($arItem->getBooks() as $book){

                $arItems[$book->getId()]['ID'] = $book->getId();
                $arItems[$book->getId()]['NAME_BOOK'] = $book->getUfNameBook();
                $arItems[$book->getId()]['AUTHOR'] = $book->getUfAuthor();
                $arItems[$book->getId()]['ID_CATALOG'] = $arItem->getId();
                $arItems[$book->getId()]['NAME_CATALOG'] = $arItem->getUfNameCatalog();
            }

        }
        $this->arResult = array(
            'ITEMS' => $arItems,
            /*'NAV' => $nav,
            'SUCCESS' => $successMessage,*/
        );

        $this->IncludeComponentTemplate();
        //}
    }

    static function getHLBlockID($hlTableName)
    {
        $dbRes = HLBT::getList(array(
            'filter' => array(
                'TABLE_NAME' => $hlTableName,
            )
        ));
        if ($arRes = $dbRes->fetch()) {
            return $arRes['ID'];
        } else {
            return false;
        }
    }

    static function getEntity($hlTableName)
    {

        $hlID = self::getHLBlockID($hlTableName);
        if (empty($hlID)) {
            return null;
        }
        $arHLB = HLBT::getById($hlID)->fetch();

        if ($arHLB) {
            $obEntity = HLBT::compileEntity($arHLB);
            $entityDataClass = $obEntity->getDataClass();
            return $entityDataClass;
        } else {
            return NULL;
        }
    }
}