# wolnosciowiec-wiki

Converts git repository into a website.

### How it works?

1. A git push on github is made, then the github notifies the service
2. The service is compiling source files eg. Markdown to HTML
3. A website is ready at given address of the service

### Setup

- Clone the repository.
- composer install --no-dev
- Create the `app/config/wiki.yml`, here is the example structure:

```
wiki:
    repositories:
        anarchifaq:
            address: "https://github.com/Wolnosciowiec/anarchi-faq-pl"
            branch:  "master"
            fetcher: "git"
```

- Configure and start the webserver
For development use `php bin/console server:start` and point your browser to the http://localhost:8000
