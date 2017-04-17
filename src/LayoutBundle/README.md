Layout extension
================

Renderer extension that allows to wrap every rendered page in a layout.
A layout may be rendered by any handler, the most extensible for this purpose
is Twig.

## How it works?

Simply - put a `.layout.html.twig` in the directory where are files to
wrap in a layout.

Example structure:
```
    /pages/
    /pages/articles
    /pages/articles/.layout.html.twig
    /pages/articles/why-anarchism.md
```

`/pages/articles/.layout.html.twig`

```html
<html>
    <head>
        <title>Articles</title>
    </head>
    <body>
        {{ layout_body_content }}
    </body>
</html>
```

In case you would prefer for example a plain HTML format for templating
then you can use a `{%wiki layout_body_content %wiki}` expression which
will be replaced with content after the rendering of the layout (in the output).

### Naming

Layout file names:
- .layout.html.twig
- .layout.html
- .layout.j2
- .layout.html
- .layout.tpl
- .layout.blade

Variables accessible in the layout to get the page content:
- `{%wiki layout_body_content %wiki}` (after render - post render)
- layout_body_content (variable available in the templating engine)

## Configuration

The extension does not require configuration. Only one thing is to create
a proper named file in a directory where files are going to be packed into a layout.

## Extending

The extension works very good in composition of `Metadata` and `MetadataCollection`.
`Metadata` provides information about current page such as title, modification date, author, if its publicated and other
fields.

`MetadataCollection` is able to show a paginated list of eg. articles, news, texts from given directory called `collection`.
