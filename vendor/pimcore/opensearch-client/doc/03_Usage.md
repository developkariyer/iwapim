# Usage of the Opensearch Client Bundle

The Opensearch Client can  be injected into any service setting the argument of your service:

```yaml
    App\Service\MyService:
        arguments:
            $openSearchClient: '@pimcore.open_search_client.<client_name>'
```

The Client can then be used in your service:

```php
<?php

namespace App\Service;

use OpenSearch\Client;

final class MyService
{
    public function __construct(
        private reasonly Client $openSearchClient
    )
    {
        //...
    }
    
    // ...
}

```