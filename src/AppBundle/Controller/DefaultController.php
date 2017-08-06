<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Url;
use AppBundle\Entity\ShortUrl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $url = new Url();

        // generate a form
        $form = $this->createFormBuilder($url)
            ->add('url', UrlType::class)
            ->add('save', SubmitType::class, array('label' => 'Shorten'))
            ->getForm();

        // get historic urls from cache
        $cache = $this->get('cache.app');
        $cachedData = $cache->getItem('historic_urls');

        // check if we found the item in cache (redis is being used at the moment)
        if ($cachedData->isHit()) {
            $historic_urls = $cachedData->get(); // found item in cache
        } else {
            // fetch last 5 generated short urls
            $historic_urls = $this->getDoctrine()
                    ->getRepository(ShortUrl::class)
                    ->findLastFiveShortUrls();

            // save in cache for next hit to minimise db hits as we have a lot more reads than writes.
            $cachedData->set($historic_urls);
            $cache->save($cachedData);
        }

        // render twig for home page
        return $this->render('default/index.html.twig', [
            'csrf_token' => '',
            'historic_urls' => $historic_urls,
            'form' => $form->createView()
        ]);
    }
}
