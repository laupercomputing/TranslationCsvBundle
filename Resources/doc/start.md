Start
=====

This bundle helps you to export all the translations you have done in a application into a excel readable format.

### Step 1: Download this bundle with composer

Tell composer to require this bundle by adding following to your composer.json:

    {
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/laupercomputing/TranslationCsvBundle"
            }
        ],
        "require": {
            "laupercomputing/translation-csv-bundle": "dev-master"
        }
    }


Composer will install the bundle to your project's vendor directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new LPC\TranslationCsvBundle\LPCTranslationCsvBundle()
    );
}
```

[Basic usage](usage.md)