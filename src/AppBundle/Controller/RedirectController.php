<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShortUrl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class RedirectController extends Controller
{
    /**
     * @Route("/r/{slug}", name="redirectHandler")
     */
    public function redirectAction($slug)
    {
        // redirect to homepage if slug isn't a valid alphanumeric string
        $slug = trim($slug);

        // check if slug is valid and base 62
        if ($slug && ctype_alnum($slug)) {
            $cache = $this->get('cache.app');

            // get item from cache
            $cachedData = $cache->getItem($slug);

            // check if we found the item in cache (redis is being used at the moment)
            if ($cachedData->isHit()) {
                $destination_url = $cachedData->get(); // found item in cache
            } else {
                // fallback to database in case we can't find item in cache
                $shortUrl = $this->getDoctrine()
                    ->getRepository(ShortUrl::class)
                    ->findOneByShortUrl($slug);

                // if we can't find the url fail softly by redirecting to home page
                if (!$shortUrl) {
                    return $this->redirectToRoute('homepage');
                }

                $destination_url = $shortUrl->getDestinationUrl();
                
                // save in cache for next hit.
                $cachedData->set($destination_url);
                $cache->save($cachedData);
            }

            // redirect to destination url if found.
            return $this->redirect($destination_url);
        }

        // fallback to home page if not a valid alphanumeric slug
        return $this->redirectToRoute('homepage');
    }
}
