<?php

namespace SL\DataHarvestBundle\Resources\Helpers;

class ACMS
{

    public function __construct(){
    }

    public function getObjects($params)
    {
        $objects = array();
        extract($params);
        if(file_exists($path)
            && is_readable($path)){
            if(($fh = fopen($path, 'r')) !== FALSE){
                $row = 0;
                $headings = array();
                while(($data = fgetcsv($fh, 1000, ',')) !== FALSE
                        && $row <= $params['max']){
                    $row++;
                    if($row === 1){
                        $headings = $data;
                        array_walk($headings, function(&$value, $key){
                            $value = preg_replace(array('/\s+/', '/[^a-zA-Z\s\_]+/'), array('_', ''), $value);
                        });
                        continue;
                    }
                    $object = new \stdClass();
                    foreach($headings as $k => $v){
                        if(isset($data[$k])){
                            $object->$v = $data[$k];
                        }else{
                            $object->$v = '';
                        }
                    }
                    $objects[] = $object;
                }
            }
        }
        return $objects;
    }
}