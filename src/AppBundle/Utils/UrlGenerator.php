<?php
// src/AppBundle/Utils/UrlGenerator.php
namespace AppBundle\Utils;

class UrlGenerator
{
	const SHORT_URL_LENGTH = 6;

    // Creates a random short string that we can use as a short url.
    // Can also replace this with an algorithm where we can directly decode the id from the string
    // example - http://kvz.io/blog/2009/06/10/create-short-ids-with-php-like-youtube-or-tinyurl/
	public function generate() {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num_characters = strlen($characters);

		$short_url = '';
		for ($i = 0; $i < self::SHORT_URL_LENGTH; $i++) {
			$short_url .= $characters[rand(0, $num_characters - 1)];
		}

		return $short_url;
	}
}