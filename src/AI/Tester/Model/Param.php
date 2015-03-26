<?php

namespace AI\Tester\Model;

class Param
{
    public $id;
    public $name;
    public $value;
    public $unit;

    public $buy;

    public static function parseFromJson($json, Buy $buy)
    {
        $param = new self();
        $param->id = $json['id'];
        $param->name = $json['name'];
        $param->value = $json['value'];
        $param->unit = $json['unit'];

        $param->buy = $buy;

        return $param;
    }

    /**
     * @param array $json
     * @param Buy $buy
     * @return Buy[]
     */
    public static function parseAllFromJson(array $json, Buy $buy)
    {
        $params = [];
        foreach ($json as $paramArray) {
            $params[] = self::parseFromJson($paramArray, $buy);
        }

        return $params;
    }
}
