# Configuration of the Opensearch Client Bundle

The Configuration takes place in symfony configuration tree where multiple opensearch clients can be configured as follows. It is possible to configure one or more clients if necessary. By default, a default client with host set to localhost:9200 is available and can be customized.

Also see the [Opensearch Docs](https://opensearch.org/docs/latest/clients/php/) for more information.

```yaml
pimcore_open_search_client:
    clients:
        default:
            hosts: ['https://opensearch:9200']
            password: 'admin'
            username: 'somethingsecret'
            logger_channel: 'pimcore.opensearch'
            log_404_errors: true #Enable logging of 404 errors (default: false)
        statistics:
            hosts: ['https://statistics-node:9200']
            logger_channel: 'pimcore.statistics'

            # Optional options
            ssl_key: 'path/to/ssl/key'
            ssl_cert: 'path/to/ssl/cert'
            ssl_password: 'somethingsecret'
            ssl_verification: false #true is the default value
        aws:
            aws_region: 'eu-central-1'
            aws_service: 'es'
            aws_key: 'aws_key'
            aws_secret: 'aws_secret'
```