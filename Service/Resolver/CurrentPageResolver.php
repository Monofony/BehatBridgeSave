<?php

/*
 * This file is part of the Monofony package.
 *
 * (c) Monofony
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Monofony\Bridge\Behat\Service\Resolver;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Webmozart\Assert\Assert;

final class CurrentPageResolver implements CurrentPageResolverInterface
{
    private Session $session;

    private UrlMatcherInterface $urlMatcher;

    public function __construct(Session $session, UrlMatcherInterface $urlMatcher)
    {
        $this->session = $session;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function getCurrentPageWithForm(array $pages)
    {
        $routeParameters = $this->urlMatcher->match(parse_url($this->session->getCurrentUrl(), PHP_URL_PATH));

        Assert::allIsInstanceOf($pages, SymfonyPageInterface::class);

        foreach ($pages as $page) {
            if ($routeParameters['_route'] === $page->getRouteName()) {
                return $page;
            }
        }

        throw new \LogicException('Route name could not be matched to provided pages.');
    }
}
