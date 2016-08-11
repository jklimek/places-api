Places API
=========

REST API is built using Symfony2 framework. 
I didn't use any REST builder bundle (such as FOSRestBundle) for the sake of self improvement and code show off. Http requests are made with GuzzleHttp classes

REST API consists of 2 resources:
* ```/api/places``` + ```/places/{placeId}```
* ```/api/photos/{photoId}```

First one provides list and places' details around given (or default) location. 
Second one is for fetching places' photos

Responses are served in JSON format except for ```/photos``` resource, where photos are served as a image files.

Each request has to be supplied with a mandatory ```key```, parameter provided from Google Places API (https://developers.google.com/places/web-service/get-api-key)

More information about API parameters and usage is found in included ```./api_generated_doc.html``` file.

I decided not to cache or store responses on purpose because of Google Place API nature. Provided information is live and can vary at any time. Businesses can change their details in the Google DB so to not confuse possible API users every request is going through the Google Places API. Caching lists of places could be also redundant because of differing ```location``` parameter.

Tests were written using codecption tool (http://codeception.com/quickstart). After the installation use ```codecept run``` to run all the tests.


Places GUI
=========
Places GUI is also built using Symfony2 framework (API + GUI are built together as a single project) along with Bootstrap3. It consumes data from the API, and for the sake of presentation uses hardcoded (in ```.app/config/parameters.yml.dist```) Google Places API (with bumped daily limits)
GUI consists of 2 pages:

* index/listing page (```/```)
* place details page (```/place/{placeId}```)

Index page lists 20 bars nearest to Neptune's Fountain in Gdańsk by default; the navbar form allows to search for different places at user location.

Places' details page lists information about particular place (name, address, opening hours, contact info, photos and reviews).

A note on geolocation: Geolocation is depending on HTML Geolocation API. That indicates that there may be some problems with location on any Chrome browser (both mobile and desktop) due to insecure origins of a webpage. Switching to HTTPS should resolve this problem.

_Chrome: getCurrentPosition() and watchPosition() no longer work on insecure origins. To use this feature, one should consider switching the application to a secure origin, such as HTTPS. Cf https://goo.gl/rStTGz for more details._

Installation
=========

After pulling repository simply run ```composer install``` to install all the dependencies. 

After that you need to install all assets -- run ```php app/console assetic:dump``` to dump them.

Project is written in PHP7, and should work on any HTTP server supporting PHP7. As a standard Symfony2 project, a local php server is also sufficient (e.g. ``` php ./app/console server:start 127.0.0.1:8000``` command).

After that you should be ready to use the API, along with provided GUI application.

I also provided a working API and GUI at http://places.klemens.ninja/