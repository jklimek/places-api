Places API
=========

REST API is built using Symfony2 framework. 
I didn't use any REST builder bundle (such as FOSRestBundle) for the sake of self improvement and code show off. Http requests are made with GuzzleHttp classes

REST API consists of 2 resources:
* ```/places``` + ```/places/{placeId}```
* ```/photos/{photoId}```

First one provides list and places details around given (or default) location. 
Second one is for fetching places photos

Responses are served in JSON format except for ```/photos``` resource, photos are served as a image files.

Each request has to be supplied with a mandatory ```key``` parameter provided from Google Places API (https://developers.google.com/places/web-service/get-api-key)

More information about API parameters and usage is found in included ```./api_generated_doc.html``` file.

I decided not to cache or store responses on purpose because of Google Place API nature. Provided information is live and can vary at any time. Businesses can change their details in Google DB so to not confuse possible API users every request is going through Google Places API. Caching places lists could be also redundant because of differing ```location``` parameter.

Tests were written using codecption tool (http://codeception.com/quickstart). After installing just use ```codecept run``` to run all tests.


Places GUI
=========

Places GUI is also built using Symfony2 framework (API + GUI are build together as a single project) along with Bootstrap3.
GUI consists of 2 pages:

* index/listing page (```/```)
* place details page (```/place/{placeId}```)

Index page lists 20 nearsest bars near Neptune's Fountain in Gdańsk by default but navbar form can be used to search for different places at user location.

Place details page lists information about particular place such as: name, address, opening hours, contact info, photos and reviews.

Note for geolocation: Geolocation is depending on HTML Geolocation API. That indicates that there could be some problems resolving location on any Chrome (mobile/desktop) browser due to insecure origins of a webpage. Switching to HTTPS can resolve this problem.

_Chrome: getCurrentPosition() and watchPosition() no longer work on insecure origins. To use this feature, you should consider switching your application to a secure origin, such as HTTPS. See https://goo.gl/rStTGz for more details._


Installation
=========

After pulling repository simply run ```composer install``` to install all required dependencies. 

After that you need to install all assets. Run ```php app/console assetic:dump``` to dump all assets.

Project is written in PHP7 so any suitable HTTP server supporting PHP7 will be suitable. As a standard Symfony2 project local php server is also sufficient (e.g. ``` php ./app/console server:start 127.0.0.1:8000``` command).

After that you should be ready to go and use API along with provided GUI application.