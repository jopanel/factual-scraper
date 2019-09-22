### Introduction
This is a very basic implementation of a place search app in Wordpress. The example was thrown together to clarify handling of:
* full-text-search
* row filtering
* category filtering
* paging
* putting stuff on a map using [MapBox](https://www.mapbox.com/)

### Before you get started
1. [Get a Factual API key](https://www.factual.com/contact#free_api_access) if you haven't already
2. [Register for a Mapbox account](https://www.mapbox.com/signup/) if you haven't already
3. [Get your Mapbox access token](https://www.mapbox.com/account/apps/). (You'll need to make sure you've created an _app_ first)
4. [Get your Mapbox map Id](https://www.mapbox.com/projects/). (You'll need to make sure you've created a _project_ first)

### Installation
1. Install the [PHP driver for Wordpress](https://github.com/Factual/factual-php-driver/wiki/Wordpress)
2. Copy the provided **index.php** file to your desired location
3. Update **index.php** with your [API key and secret](https://github.com/Factual/factual-php-driver/blob/master/wordpress/index.php#L21)
4. Update **index.php** with your [Mapbox access token and map Id](https://github.com/Factual/factual-php-driver/blob/master/wordpress/index.php#L23)
5. Make sure that **index.php** is pointing at the proper location [where you installed the driver](https://github.com/Factual/factual-php-driver/blob/master/wordpress/index.php#L18)
6. Run

