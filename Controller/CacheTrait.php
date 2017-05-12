<?php

/*
 * This file is part of Twigony.
 *
 * © Timon F <dev@timonf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twigony\Bundle\FrameworkBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

trait CacheTrait
{
    /*
     * This method shares code from "TemplateController::templateAction"
     * (c) Fabien Potencier <fabien@symfony.com>
     * License information: https://github.com/symfony/framework-bundle/blob/v3.2.7/LICENSE
     */

    /**
     * Applies cache parameters to a response object.
     *
     * @param Response  $response  Target response to apply cache headers
     * @param int|null  $maxAge    Max age for client caching
     * @param int|null  $sharedAge Max age for shared (proxy) caching
     * @param bool|null $private   Whether or not caching should apply for client caches only
     */
    protected function applyCacheParameters(Response $response, $maxAge, $sharedAge, $private)
    {
        if ($maxAge !== null) {
            $response->setMaxAge($maxAge);
        }

        if ($sharedAge !== null) {
            $response->setSharedMaxAge($sharedAge);
        }

        if ($private) {
            $response->setPrivate();
        } elseif ($private === false || (null === $private && ($maxAge !== null || $sharedAge !== null))) {
            $response->setPublic();
        }
    }

    /**
     * Applies cache parameters by an array to a response object.
     *
     * @param Response $response Target response to apply cache headers
     * @param array    $options
     */
    protected function applyCacheOptions(Response $response, $options)
    {
        $maxAge = array_key_exists('maxAge', $options) ? $options['maxAge'] : null;
        $sharedAge = array_key_exists('sharedAge', $options) ? $options['sharedAge'] : null;
        $private = array_key_exists('private', $options) ? boolval($options['private']) : null;

        $this->applyCacheParameters($response, $maxAge, $sharedAge, $private);
    }
}
