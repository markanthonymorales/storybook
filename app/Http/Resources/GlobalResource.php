<?php

namespace App\Http\Resources;

// use App\Helper\Hasher;

class GlobalResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  $collection
     * @param  $properties
     * @return array
     */
    public static function toManyArray($collections, array $properties)
    {
        try {
            $toManyArray = array();
            foreach ($collections as $c => $collection) {
                foreach ($properties as $p => $property) {
                    $key = explode('|', $p);

                    if(!isset($key[1]))
                        $toManyArray[$c][$property] = $collection[$property];

                    if(isset($key[1])){
                        self::callBackCondition($key, $toManyArray[$c][$key[0]], $collection, $property);
                    }
                }
            }

            return $toManyArray;
        } catch (\Exception $e) {
            dd('Whoops: '. $e->getMessage());
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param  $collection
     * @param  $properties
     * @return array
     */
    public static function toArray($collection, array $properties)
    {
        try {
            $toArray = array();

            foreach ($properties as $p => $property) {
                
                $key = explode('|', $p);
                if(!isset($key[1])){
                    $toArray[$property] = $collection[$property];
                }

                if(isset($key[1])){
                    self::callBackCondition($key, $toArray[$key[0]], $collection, $property);
                }
            }
            return $toArray;
        } catch (\Exception $e) {
            dd('Whoops: '. $e->getMessage());
        }
    }

    public static function callBackCondition($condition = '', &$toArray, $collection, $property)
    {
        // if($condition[1] == 'hash'){
        //     $toArray = Hasher::encode($collection[$property]);
        // }

        if($condition[1] == 'inline'){
            $props = explode('|', $property);
            $toArray = '';

            if(strpos($condition[2], ':')){
                $arg = explode(':', $condition[2]);
                $collection2 = self::getSpecificCollection($arg, $collection);
            }else
                $collection2 = $collection[$condition[2]];

            foreach ($props as $s => $prop){

                if($s > 0 && isset($condition[3]))
                    $toArray .= ' ';

                $toArray .= $collection2[$prop];
            }
        }

        if($condition[1] == 'function'){
            $toArray = $collection->{$property}();
        }

        if($condition[1] == 'null')
            $toArray = $property;

        if($condition[1] == 'alias')
            $toArray = self::toArray($collection, $property);

        if($condition[1] == 'one')
            $toArray = self::toArray($collection[$condition[0]], $property);

        if($condition[1] == 'many')
            $toArray = self::toManyArray($collection[$condition[0]], $property);
    }

    public static function getSpecificCollection($arg, $collection, $length = 0){
        $currentLength = $length;
        if($currentLength == count($arg) - 1){
            return $collection[$arg[$currentLength]];
        }
        return self::getSpecificCollection($arg, $collection[$arg[$currentLength]], $length+=1);
    }

    /**
     * Transform the resource into an object.
     *
     * @param  $collection
     * @param  $properties
     * @return object
     */
    public static function toObject($collection, array $properties)
    {
        $toArray = self::toArray($collection, $properties);
        return (object)$toArray;
    }

    /**
     * Transform the resource into an json.
     *
     * @param  $collection
     * @param  $properties
     * @return json
     */
    public static function toJson($collection, array $properties)
    {
        $toArray = self::toArray($collection, $properties);
        return json_encode($toArray);
    }
}
