<?php declare(strict_types=1);

namespace MetadataBundle\Domain\Entity;

class Article extends BaseMetadata
{
    protected $slug = '';
    protected $title = '';

    public function getType(): string
    {
        return 'article';
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
