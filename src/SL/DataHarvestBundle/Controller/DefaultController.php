<?php

# src/SL/DataHarvestBundle/Controller/DefaultController.php

namespace SL\DataHarvestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use SL\DataHarvestBundle\Entity\Data;
use SL\DataHarvestBundle\Resources\Helpers\Data as Helper;

class DefaultController extends Controller
{

    /**
     * The primary method responsible for handling the page callback, delegating control to the necessary handlers for harvesting the data from various sources. The handlers are private methods suffixed with an uppercase service name.
     *
     * @param string | $name
     */

    public function harvestAction($name)
    {
        $function = '_harvest' . strtoupper($name);
        if(method_exists($this, $function)){
            return $this->$function();
        }
    }

    /**
     * A method acting as a harvesting handler for the eHive service.
     *
     * @return object | $response
     */

    private function _harvestEHIVE()
    {
        $api = new \EHive_EHive();
        $params = array(
            'query' => '',
            'account' => 5051,
            'max' => 10,
            'tag' => 'main street',
        );
        if($objects = ($api->getObjectsFromAccount($params))){
            $contents = file_get_contents(__DIR__ . '/data.php');
            $objects = unserialize($contents);
            $model = $this->getDoctrine()->getManager();
            $count = 0;
            foreach($objects as $object){
                $data = new Data();
                if(is_object($object)){
                    $data->setSource($object->objectUrl);
                    $data->setDescription($object->simpleSummary);
                    if(isset($object->fieldSets)){
                        foreach($object->fieldSets as $fieldSet){
                            ${$fieldSet->identifier} = $fieldSet->fieldRows[0]->fields[0]->attributes[0]->value;
                        }
                    }
                    if(isset($object->mediaSets)){
                        foreach($object->mediaSets as $mediaSet){
                            if($mediaSet->identifier == 'image'){
                                foreach($mediaSet->mediaRows[0]->media as $mediaRows){
                                    if($mediaRows->identifier == 'image_m'){
                                        foreach($mediaRows->attributes as $attribute){
                                            if($attribute->key == 'url'){
                                                $data->setMedia($attribute->value);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(isset($name)){
                        $data->setTitle($name);
                    }
                }
                $model->persist($data);
            }
            $model->flush();
            $response = new JsonResponse();
            $response->setData($objects);
            return $response;
        }
    }

    /**
     * A method acting as a harvesting handler for the eHive service.
     *
     * @return object | $response
     */

    private function _harvestTROVE()
    {
        if(TRUE){
        }
        $data = new Data();
        $model = $this->getDoctrine()->getManager();
        $model->persist($data);
        $model->flush();
        $response = new JsonResponse();
        $response->setData($objects);
    }
}
