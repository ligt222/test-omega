<?php

namespace Neti\SimpleMail;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Highloadblock\HighloadBlockTable AS HLBT;



class SimpleMail
{

    const HL_TABLE_NAME = 'neti_simplemail';
    const HL_ENTITY_NAME = 'SimpleMail';

    /**
     * Возвращает идентификатор хайлоад блока, используемого для хранения изображений
     * @return int
     */
    static function getHLBlockID()
    {
        $dbRes = HLBT::getList(array(
            'filter' => array(
                'TABLE_NAME' => self::HL_TABLE_NAME,
            )
        ));
        if ($arRes = $dbRes->fetch()) {
            return $arRes['ID'];
        } else {
            return false;
        }
    }

    /**
     * Возвращает название сущности для работы с ХБ
     * @return mixed
     */
    static function getEntity()
    {
        static $entityDataClass = null;
        if (!empty($entityDataClass)) {
            return $entityDataClass;
        }

        $hlID = self::getHLBlockID();
        if (empty($hlID)) { return null; }

        $arHLB = HLBT::getById($hlID)->fetch();
        if($arHLB) {
            $obEntity = HLBT::compileEntity($arHLB);
            $entityDataClass = $obEntity->getDataClass();
            return $entityDataClass;
        } else {
            return NULL;
        }
    }

    /**
     * Возвращает текущее количество записей таблицы ХБ
     * @return int
     */
    static function getRecordCount()
    {
        $entity = self::getEntity();
        if (empty($entity)) { return 0; }

        $arParams = array(
            'select' => array('CNT'),
            'runtime' => array(
                new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
            ),
            'limit' => 1,
        );

        $dbResult = $entity::getList($arParams);
        if ($arRes = $dbResult->fetch()) {
            return $arRes['CNT'];
        } else {
            return 0;
        }
    }

    static function getPropIblock($iblockId)
    {
        if(\Bitrix\Main\Loader::includeModule('iblock'))
        {

            $arFields = [
                [
                    'NAME' => 'Количество редактирования элемента',
                    'ACTIVE' => 'Y',
                    'SORT' => 500,
                    'CODE' => 'SUMM_EDIT_ELEM',
                    'PROPERTY_TYPE' => 'N',
                    'IBLOCK_ID' => $iblockId,
                ],
                [
                    'NAME' => 'Максимальная длительность сохранения введенных значений',
                    'ACTIVE' => 'Y',
                    'SORT' => 500,
                    'CODE' => 'MAX_SAVE_VALUES',
                    'PROPERTY_TYPE' => 'S',
                    'USER_TYPE' => 'DateTime',
                    'IBLOCK_ID' => $iblockId,
                ],
                [
                    'NAME' => 'Среднее значение длительности сохранения введенных значений',
                    'ACTIVE' => 'Y',
                    'SORT' => 500,
                    'CODE' => 'AVERAGE_SAVE_ELEM',
                    'PROPERTY_TYPE' => 'S',
                    'USER_TYPE' => 'DateTime',
                    'IBLOCK_ID' => $iblockId,
                ],
                [
                    'NAME' => 'Запретить вести учет изменений',
                    'ACTIVE' => 'Y',
                    'SORT' => 500,
                    'CODE' => 'CHANGE_EDIT',
                    'PROPERTY_TYPE' => 'L',
                    'LIST_TYPE' => 'С',
                    'IBLOCK_ID' => $iblockId,
                    'VALUES' => array(
                        'n0' => array(
                            'ID' => 'n0',
                            'VALUE' => 'Y',
                            'DEF' => 'N'
                        )
                    )

                ],
            ];

            $propertyResult = \Bitrix\Iblock\PropertyTable::addMulti($arFields);
            if ($propertyResult->isSuccess()) {
                return $propertyResult->getId();
            } else {
                return false;
            }
        }
    }

    static function delPropIblock($idIb)
    {
        if(\Bitrix\Main\Loader::includeModule('iblock'))
        {

            $arCodeProps = array(
                1 => 'SUMM_EDIT_ELEM',
                2 => 'MAX_SAVE_VALUES',
                3 => 'AVERAGE_SAVE_ELEM',
                4 => 'CHANGE_EDIT',
            );

            $arProps = \Bitrix\Iblock\PropertyTable::getList(array(
                'select' => array('*'),
                'filter' => array('IBLOCK_ID' => $idIb)
            ));

            while ($arProp = $arProps->fetch()) {
                for ($i = 1; $i <= count($arCodeProps); $i++){
                    if ($arProp['CODE'] === $arCodeProps[$i]){

                        \Bitrix\Iblock\PropertyTable::delete($arProp['ID']);

                    }
                }

            }
        }
    }
}


