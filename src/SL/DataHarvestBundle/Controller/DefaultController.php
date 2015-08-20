<?php

# src/SL/DataHarvestBundle/Controller/DefaultController.php

namespace SL\DataHarvestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use SL\DataHarvestBundle\Entity\Data;
use SL\DataHarvestBundle\Resources\Helpers\Trove as Trove_Helper;
use SL\DataHarvestBundle\Resources\Helpers\ACMS as ACMS_Helper;

class DefaultController extends Controller
{

    private $_searchTerm = 'main street';

    /**
     * The primary method responsible for handling the page callback, delegating control to the necessary handlers for harvesting the data from various sources. The handlers are private methods suffixed with an uppercase service name.
     *
     * @param string | $name
     * @return $response | object
     */

    public function harvestAction($source)
    {
        $function = '_harvest' . strtoupper($source);
        if(method_exists($this, $function)){
            return $this->$function();
        }
        // Throw a 'not found' exception.
        throw $this->createNotFoundException();
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
            'max' => 200,
            'tag' => $this->_searchTerm,
        );
        $output = '';
        if($objects = ($api->getObjectsFromAccount($params))){
            // Uncomment the following lines to use static data for debugging. Quicker than making a request to the API.
            // $contents = file_get_contents(__DIR__ . '/data.txt');
            // $objects = unserialize($contents);
            $model = $this->getDoctrine()->getManager();
            // Remove existing entries for eHive.
            $result = $model->createQuery("DELETE FROM SLDataHarvestBundle:Data sl WHERE sl.source = 'ehive'")
                            ->execute();
            if($result >= 0){
                $output =& $objects;
                $count = 0;
                foreach($objects as $object){
                    $data = new Data();
                    if(is_object($object)){
                        $data->setUrl($object->objectUrl);
                        $data->setDescription($object->simpleSummary);
                        if(isset($object->fieldSets)){
                            foreach($object->fieldSets as $fieldSet){
                                foreach($fieldSet->fieldRows[0]->fields[0]->attributes as $attribute){
                                    if($attribute->key == 'value'){
                                        switch($fieldSet->identifier){
                                            case 'date_made':
                                                $regex = preg_match('/[0-9]{4}/', $attribute->value, $matches);
                                                if(!empty($matches)){
                                                    ${$fieldSet->identifier} = new \DateTime('01-01-' . $matches[0]);
                                                }
                                                break;
                                            default:
                                                ${$fieldSet->identifier} = $attribute->value;
                                        }
                                    }
                                }
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
                        if(isset($date_made)){
                            $data->setDate($date_made);
                        }
                        $data->setSource('ehive');
                    }
                    $model->persist($data);
                }
                $model->flush();
            }
            $response = new Response(json_encode($output));
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
                "key=g7jcpc3coas4e7qs&q={$this->_searchTerm} date:[1910 TO 1920]&zone=newspaper&include=tags,workversions&reclevel=full&l-title=35",
                "key=g7jcpc3coas4e7qs&q={$this->_searchTerm} date:[1910 TO 1920]&zone=newspaper&include=tags,workversions&reclevel=full&l-title=1007",
            ),
            'max' => 100,
        );
        $output = '';
        $model = $this->getDoctrine()->getManager();
        $result = $model->createQuery("DELETE FROM SLDataHarvestBundle:Data sl WHERE sl.source = 'trove'")
                    ->execute();
        $helper = new Trove_Helper();
        if($result >= 0){
            if($objects = ($helper->getObjects($params))){
                $output = new \DomDocument();
                $output->formatOutput = TRUE;
                $output->loadXML('<responses></responses>');
                foreach($objects as $object){
                    // Create a new instance of the Symfony DOM Crawler, new XML will be loaded for each iteration.
                    $crawler = new Crawler();
                    // Create the XML structure from the response object.
                    $crawler->addContent($object);
                    // Filter the XML document by the records node.
                    $records = $crawler->filterXPath('//records');
                    // Filter the XML document by the zone node (parent to records).
                    $zones = $crawler->filterXPath('//zone');
                    // Create a new instance of the in-built (global namespace) DOM class.
                    $document = new \DomDocument();
                    // Load XML onto the object based on the filter applied by the crawler.
                    $document->loadXML($zones->html());
                    // Import the children of zone into the document to be used for output. There will only ever be one child 'records' node, therefore we can access it using a static index.
                    $node = $output->importNode($document->getElementsByTagName('records')->item(0), TRUE);
                    // Append the imported return node to the root element of the output document.
                    $output->documentElement->appendChild($node);
                    foreach($records->children() as $record){
                        $data = new Data();
                        $data->setTitle($record->getElementsByTagName('heading')->item(0)->nodeValue);
                        $data->setDescription($record->getElementsByTagName('snippet')->item(0)->nodeValue);
                        $data->setData(serialize(array('title' => $record->getElementsByTagName('title')->item(0)->nodeValue)));
                        $data->setDate(new \DateTime($record->getElementsByTagName('date')->item(0)->nodeValue));
                        $data->setUrl($record->getElementsByTagName('trovePageUrl')->item(0)->nodeValue);
                        $data->setSource('trove');
                        $model->persist($data);
                    }

                }
            }
        }
        $model->flush();
        $response = new Response($output->saveXML());
        $response->headers->set('Content-type', 'text/xml');
        return $response;
    }

    /**
     * A method acting as a harvesting handler for the eHive service.
     *
     * @return object | $response
     */

    private function _harvestACMS()
    {
        $params = array(
            'path' => $this->get('kernel')->locateResource('@SLDataHarvestBundle') . 'Resources/files/acms.csv',
            'max' => 100,
        );
        $model = $this->getDoctrine()->getManager();
        $result = $model->createQuery("DELETE FROM SLDataHarvestBundle:Data sl WHERE sl.source = 'acms'")
                        ->execute();
        $helper = new ACMS_Helper();
        $output = '';
        if($result >= 0){
            if($objects = ($helper->getObjects($params))){
                $output =& $objects;
                foreach($objects as $object){
                    $data = new Data();
                    $data->setTitle($object->description);
                    $data->setDescription($object->description);
                    $regex = preg_match('/[0-9]{4}/', $object->date, $matches);
                    if(!empty($matches[0])){
                        $data->setDate(new \DateTime('01-01-' . $matches[0]));
                    }
                    $data->setUrl($object->record_url);
                    $data->setMedia($object->image_url);
                    $data->setSource('acms');
                    $model->persist($data);
                }
            }
            $model->flush();
        }
        $response = new Response(json_encode($output));
        $response->headers->set('Content-type', 'application/json');
        return $response;
    }

    /**
     * A method to extract the most commonly used words across articles from the same decade.
     *
     * @param $string | $year
     * @return $response | object
     */

    public function filterTagsAction($year)
    {
        $helper = new Trove_Helper();
        $end = ($year + 10);
        $start = new \DateTime('01-01-' . $year);
        $end = new \DateTime('01-01-' . (string)$end);
        $model = $this->getDoctrine()->getManager();
        $repository = $model->getRepository('SLDataHarvestBundle:Data');
        $query = $repository->createQueryBuilder('d');
        $query->where('d.date >= :start')
              ->andWhere('d.date <= :end')
              ->andWhere('d.source = :source')
              ->setParameters(array(
                    'start' => $start->format('c'),
                    'end' => $end->format('c'),
                    'source' => 'trove',
                ));
        $store = array();
        $output = '';
        if($objects = ($query->getQuery()->getArrayResult())){
            foreach($objects as $object){
                $store[$object['date']->format('Y')][] = $object;
            }
            if(!empty($store)){
                foreach($store as $k => $v){
                    $text = '';
                    $v = array_walk($v, function($value, $key) use (&$text){
                        $text .= $value['description'];
                    });
                    if(strlen($text) > 0){
                        $output = $helper::extractCommonWords($text);
                    }
                }
            }
        }
        $response = new Response(json_encode($output));
        $response->headers->set('Content-type', 'application/json');
        return $response;
    }

    /**
     * A method to fetch the entities in the database by source.
     *
     * @param $string | $source
     * @return $response | object
     */

    public function fetchAction($source)
    {
        $output = '';
        $model = $this->getDoctrine()->getManager();
        $repository = $model->getRepository('SLDataHarvestBundle:Data');
        $objects = $repository->findBy(array(
            'source' => $source
        ), array(
            'date' => 'ASC',
        ));
        $output = new \DomDocument();
        $output->formatOutput = TRUE;
        $output->loadXML('<root></root>');
        if(is_array($objects)
            && !empty($objects)){
            foreach($objects as $object){
                try{
                    $node = $output->createElement('object');
                    $title = $output->createElement('title', htmlspecialchars(strip_tags($object->getTitle())));
                    $node->appendChild($title);
                    $description = $output->createElement('description', htmlspecialchars(strip_tags($object->getDescription())));
                    $node->appendChild($description);
                    $location = $output->createElement('location', $object->getLocation());
                    $node->appendChild($location);
                    $media = $output->createElement('media', $object->getMedia());
                    $node->appendChild($media);
                    $date = $output->createElement('date', $object->getDate()->format('Y'));
                    $node->appendChild($date);
                    $url = $output->createElement('url', $object->getUrl());
                    $node->appendChild($url);
                    $output->documentElement->appendChild($node);
                }catch(Exception $e){
                }
            }
        }
        $response = new Response($output->saveXML());
        $response->headers->set('Content-type', 'text/xml');
        return $response;
    }

}
