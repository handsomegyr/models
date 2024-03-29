<?php

namespace App\Common\Models\Base\Mongodb;

trait BaseTrait
{

    protected function getMongoId4Query($_id)
    {
        if (is_array($_id)) {
            $list = array();
            foreach ($_id as $item) {
                if ($item instanceof \MongoId) {
                    $list[] = $item->__toString();
                } else {
                    $list[] = $item;
                }
            }
            return $list;
        } else {
            if ($_id instanceof \MongoId) {
                return $_id->__toString();
            } else {
                return $_id;
            }
        }
    }

    protected function changeToBoolean($field)
    {
        if (empty($field)) {
            return false;
        }
        return boolval($field);
    }

    protected function changeToArray($field)
    {
        if (empty($field)) {
            return array();
        }
        if (is_array($field)) {
            return $field;
        } else {
            return json_decode($field, true);
        }
    }

    protected function changeToValidDate($field)
    {
        if (empty($field)) {
            return $field;
        }
        if ($field == '0000-00-00 00:00:00') {
            $field = '0001-01-01 00:00:00';
        }
        return \App\Common\Utils\Helper::getCurrentTime(strtotime($field));
    }

    protected function getColumns(array $fields = array())
    {
        $ret = array();
        if (!empty($fields)) {
            $ret['column'] = implode(',', $fields);
        }
        return $ret;
    }

    protected function getGroups(array $groups = array())
    {
        $ret = array();
        if (!empty($groups)) {
            $ret['group'] = implode(',', $groups);
        }
        return $ret;
    }
}
