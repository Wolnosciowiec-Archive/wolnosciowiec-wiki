Metadata extension
==================

Allows to create a metadata file per page.
A metadata can contain fields describing given page.

Example structure:
  - "news/next-anarchosyndycalist-workplace.md"
  - "news/.meta.next-anarchosyndycalist-workplace.yml"
  
  
news/.meta.next-anarchosyndycalist-workplace.yml:
```
meta:
    type: article
    alias: /article/syndicalism/next-anarchosyndycalist-workplace
    data:
        title: Next large workplace controlled by workers
        slug: next-anarchosyndycalist-workplace
        published: true
```

### Reference

- `type`: A name of metadata class. Its recognized by what the MetadataInterface::getType() will return.
- `alias`: A route to enter in browser to get the content of this page
- `data`: Metadata array - eg. title, dates, authors, tags etc. the fields are mapped into a class variables
  so should be matching the class that represents specified type. Example: `article` and `MetadataBundle\Domain\Entity\Article`

#### Creating metadata type

Metadata type is a set of fields that are describing the element.
For example for article let it be:
- title
- publication_date
- author_name
- icon_url

There is a possibility to define different types of metadata as not only
articles are going to be rendered.

At first create an entity class with all fields, getters and setters.
Next add a JMS Serializer definition.

See working examples:
`MetadataBundle\Domain\Entity\Article`
`src/MetadataBundle/Resources/config/serializer/Domain.Entity.Article.yml`

Last thing is to register the type to the container, to do that
just register a service tagged with "metadata".

Working example:
```
wiki.extension.metadata.article:
    class: MetadataBundle\Domain\Entity\Article
    tags:
        - { name: metadata }
```

