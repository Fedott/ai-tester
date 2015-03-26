<?php

namespace AI\Tester\Model;

class Buy
{
    public $id;
    public $title;
    public $price;
    public $rating;

    public static function parseAllFromJson($json)
    {
        $buys = [];
        foreach ($json as $buyJson) {
            $buys[] = self::parseFromJson($buyJson);
        }

        return $buys;
    }

    public static function parseFromJson($json)
    {
        $buy = new Buy();
        $buy->id = $json['id'];
        $buy->title = $json['title'];
        $buy->price = $json['price'];
        $buy->rating = $json['rating'];

        return $buy;
    }
}
