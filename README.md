Behat-ScreenshotExtension
=========================
[![License](https://poser.pugx.org/bex/behat-screenshot/license)](https://packagist.org/packages/bex/behat-screenshot)
[![Latest Stable Version](https://poser.pugx.org/bex/behat-screenshot/version)](https://packagist.org/packages/bex/behat-screenshot)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/elvetemedve/behat-screenshot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/elvetemedve/behat-screenshot/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/elvetemedve/behat-screenshot/badges/build.png?b=master)](https://scrutinizer-ci.com/g/elvetemedve/behat-screenshot/build-status/master)
[![Build Status](https://travis-ci.org/elvetemedve/behat-screenshot.svg?branch=master)](https://travis-ci.org/elvetemedve/behat-screenshot)

Behat-ScreenshotExtension helps you debug Behat scenarios by taking screenshot of the failing steps.

By default the extension takes the screenshot and save it to the preconfigured directory (by default it will save the image to the default temporary system directory).

Also the extenstion allows you to specify an image driver which can upload the image to a host, in this case you will see the image url in the terminal right after the failing step. See available image drivers [below](#available-image-drivers).

You can also create your own image driver easily, for more information see [this section](#how-to-create-your-own-image-driver).

Installation
------------

Install by adding to your `composer.json`:

```bash
composer require --dev bex/behat-screenshot
```

Configuration
-------------

Enable the extension in `behat.yml` like this:

```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension: ~
```

You can configure the screenshot directory like this:
```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension:
      image_drivers:
        local:
          screenshot_directory: /your/desired/path/for/screenshots
          clear_screenshot_directory: true  # Enable removing all images before each test run. It is false by default.
```

If you are using another image driver you can enable it like this:
```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension:
      active_image_drivers: customdriver
      image_drivers: # this node and the driver subnodes are optional, if you remove it then the driver's default values will be used
        customdriver:
          #... custom driver config goes here ...
```

You can even enable more than one image driver at once:
```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension:
      active_image_drivers: [local, customdriver]
      image_drivers:
        local:
          #... local driver config goes here ...
        customdriver:
          #... custom driver config goes here ...
```

You can make a combined screenshot including previous steps:
```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension:
      screenshot_taking_mode: failed_scenarios
        # Available modes:
        #  - failed_steps: Image contains only the screenshot of the failed step. [Default]
        #  - failed_scenarios: Image contains screenshots of all steps in a failed scenario.
        #  - all_scenarios: Each scenario has a combined image created, regardless of failing or passing.
```

You can disable the extension by removing from the behat.yml or you can disable it for a profile by using the `enabled` parameter, e.g.:
```yml
ci:
  extensions:
    Bex\Behat\ScreenshotExtension:
      enabled: false
```

Usage
-----

When you run behat and a step fails then the extension will automatically take the screenshot and you will see the filepath or the image URL of the screenshot (based on the configured image driver). So you will see something like this:

```bash
  Scenario:                           # features/feature.feature:2
    Given I have a step               # FeatureContext::passingStep()
    When I have a failing step        # FeatureContext::failingStep()
      Error (Exception)
Screenshot has been taken. Open image at /tmp/behat-screenshot/i_have_a_failing_step.png
    Then I should have a skipped step # FeatureContext::skippedStep()
```

Available Image Drivers
-----
- [bex/behat-screenshot-image-driver-uploadpie](https://packagist.org/packages/bex/behat-screenshot-image-driver-uploadpie)
- [bex/behat-screenshot-image-driver-img42](https://packagist.org/packages/bex/behat-screenshot-image-driver-img42)
- [bex/behat-screenshot-image-driver-unsee](https://packagist.org/packages/bex/behat-screenshot-image-driver-unsee)

How to create your own image driver
-----
1. Implement the `Bex\Behat\ScreenshotExtension\Driver\ImageDriverInterface`
1. Put your class under the `Bex\Behat\ScreenshotExtension\Driver` namespace

Thats it!

See example here: https://github.com/tkotosz/behat-screenshot-image-driver-dummy
