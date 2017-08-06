<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Url;
use AppBundle\Entity\ShortUrl;
use AppBundle\Utils\UrlGenerator;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

class ShortenController extends Controller
{
    /**
     * @Route("/shorten", name="shortenHandler")
     */
    public function shortenAction(Request $request, UrlGenerator $generator)
    {
        $output = array();
        $output['status'] = 'failed';

        if ($request->isXmlHttpRequest()) { // only allow ajax requests
            $url = new Url();

            $form = $this->createFormBuilder($url)
                ->add('url', UrlType::class)
                ->add('save', SubmitType::class, array('label' => 'Shorten'))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) { // if form values are valid.
                $url = $form->getData();

                // adding sanitisation to be extra sure. 
                $destination_url = null !== $url->getUrl() ? trim(filter_var($url->getUrl(), FILTER_SANITIZE_URL)) : '';
                
                // validate destination url that we are shortening. redundant given form validation but just in case.
                if (!$destination_url || !filter_var($destination_url, FILTER_VALIDATE_URL)) {
                    $output['status'] = 'failed';
                    $output['message'] = 'Please pass a valid URL';
                } else {
                    // repository functions for ShortUrls can be accessed using this.
                    $repository = $this->getDoctrine()->getRepository(ShortUrl::class);
                    $unique_url_found = false;
                    $cache = $this->get('cache.app');

                    // keep generating a new short url until we find one that doesn't exist in the database.
                    while (!$unique_url_found) {
                        $short_url = $generator->generate();

                        // check in cache. if it exists in cache, we generate a new url again
                        $cachedData = $cache->getItem($short_url);
                        if ($cachedData->isHit()) {
                            continue;
                        }

                        // check in database. if it exists in database, we generate a new url again.
                        // if it doesn't exist in database, we have found a new short url to use.
                        if (empty($repository->findOneByShortUrl($short_url))) {
                            $unique_url_found = true;
                        }
                    }

                    // store in database
                    $em = $this->getDoctrine()->getManager();

                    $shortUrl = new ShortUrl();                    
                    $shortUrl->setShortUrl($short_url);
                    $shortUrl->setDestinationUrl($destination_url);
                    $shortUrl->setCreatedAt(new \DateTime()); // set created time. might want to take care of timezone

                    // tells Doctrine you want to (eventually) save the shortUrl (no queries yet)
                    $em->persist($shortUrl);

                    // actually executes the queries (i.e. the INSERT query)
                    try {
                        $em->flush();
                    } catch (Exception $e) {
                        // return error in case we can't save to database.
                        $output['message'] = 'An error occured while generating your url. Please try again later';
                        return $this->json($output);
                    }

                    // save to cache for future usage
                    $cacheData = $cache->getItem($short_url);
                    $cacheData->set($destination_url);
                    $cache->save($cacheData);

                    $cache->deleteItem('historic_urls');

                    // return success and html to be inserted in the dom
                    $output['status'] = 'success';
                    $output['html'] = $this->render('url.html.twig', [
                            'source' => $short_url,
                            'destination' => $destination_url
                        ])
                        ->getContent();
                }
            }
        }

        return $this->json($output);
    }
}
