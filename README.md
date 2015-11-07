TODO: 
- write proper readme file
- use 3rd-party service to store images temporarily (candidates: http://uploadpie.com/)
- multistep screenshot

Example configs:

Bex\Behat\ScreenshotExtension: ~

Bex\Behat\ScreenshotExtension:
  active_image_drivers: upload_pie

Bex\Behat\ScreenshotExtension:
  active_image_drivers: ~

Bex\Behat\ScreenshotExtension:
  active_image_drivers: [local, upload_pie, unsee, img42]
  image_drivers:
    local:
      screenshot_directory: /vagrant
    upload_pie:
      expire: 30


Example wrong config:
Bex\Behat\ScreenshotExtension:
  active_image_drivers: ~
  image_drivers:
    local:
      something: somevalue

Result should be: Unrecognized option "something" under "image_drivers.local"