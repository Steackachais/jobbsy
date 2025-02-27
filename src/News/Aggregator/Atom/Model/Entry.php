<?php

namespace App\News\Aggregator\Atom\Model;

final class Entry
{
    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly ?string $summary = null,
        public readonly ?string $content = null,
        public readonly ?\DateTimeImmutable $published = null,
    ) {
    }

    public static function create(\DOMXPath $xpath, \DOMNode $node): self
    {
        $pubDate = null;
        if (0 !== $xpath->query('./atom:published', $node)->count()) {
            $pubDate = \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::ATOM,
                trim($xpath->evaluate('./atom:published', $node)->item(0)->nodeValue)
            );
        }

        return new self(
            self::getNodeValue('./atom:title', $xpath, $node),
            self::getLink($xpath, $node),
            self::getNodeValue('./atom:summary', $xpath, $node),
            self::getNodeValue('./atom:content', $xpath, $node),
            $pubDate
        );
    }

    private static function getLink(\DOMXPath $xpath, \DOMNode $node): string
    {
        return $xpath->query('./atom:link', $node)->item(0)->attributes->getNamedItem('href')->nodeValue;
    }

    private static function getNodeValue(string $expression, \DOMXPath $xpath, \DOMNode $node): ?string
    {
        if (0 === $xpath->query($expression, $node)->count()) {
            return null;
        }

        return $xpath->evaluate($expression, $node)->item(0)->nodeValue;
    }
}
