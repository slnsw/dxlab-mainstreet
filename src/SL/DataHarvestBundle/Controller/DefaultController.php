<?php

# src/SL/DataHarvestBundle/Controller/DefaultController.php

namespace SL\DataHarvestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use SL\DataHarvestBundle\Entity\Data;
use SL\DataHarvestBundle\Resources\Helpers\Trove as Trove_Helper;

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
            // Uncomment the following lines to use static data for debugging. Quicker than making a request to the API.
            // $contents = file_get_contents(__DIR__ . '/data.php');
            // $objects = unserialize($contents);
            $model = $this->getDoctrine()->getManager();
            $output =& $objects;
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
            $response = new Response($output);
            $response->headers->set('Content-type', 'application/json');
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
        $params = array(
            'paths' => array(
                'key=g7jcpc3coas4e7qs&q=main street date:[1910 TO 1920]&zone=newspaper&include=tags,workversions&reclevel=full&l-title=35&n=100',
            ),
            'max' => 100,
        );
        $helper = new Trove_Helper();
        $model = $this->getDoctrine()->getManager();
        if($objects = ($helper->getObjects($params))){
            $output = '';
            $crawler = new Crawler();
            $data = new Data();
            foreach($objects as $object){
                $crawler->addContent($object);
                $output .= $object;
                foreach($crawler->filterXPath('//records')->children() as $record){
                    $data->setTitle($record->getElementsByTagName('heading')->item(0)->nodeValue);
                    $data->setDescription($record->getElementsByTagName('snippet')->item(0)->nodeValue);
                    $data->setDate(new \DateTime($record->getElementsByTagName('date')->item(0)->nodeValue));
                    $data->setSource($record->getElementsByTagName('trovePageUrl')->item(0)->nodeValue);
                }
                $model->persist($data);
            }
        }
        $model->flush();
        $response = new Response($output);
        $response->headers->set('Content-type', 'text/xml');
        return $response;
    }
}
