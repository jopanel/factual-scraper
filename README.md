#Introduction
This is the official [PHP driver](https://github.com/Factual/factual-php-driver) for the [Factual API](http://developer.factual.com). It is crafted with artisanal skill from native hardwoods.

The driver allows you to create an authenticated handle to Factual. With a Factual handle, you can send queries and get results back as PHP Arrays and Objects. 

Many Bothamns died to bring you this documentation.

#PHP Specifics 
##Dependencies
* PHP >=5.1.2 is required. 
* The php5-curl module is required. 
* SPL is required for autoloading.
* JSON is required.  Some distributions as of PHP 5.5rc2 lack the previously included JSON extension due to a license conflict.  Use <tt>sudo apt-get install php5-json</tt>.

The package includes lightly modified [Google's oauth libraries](http://code.google.com/p/oauth-php/)

##Autoloading
All classes are autoloaded.  Just <tt>require_once("Factual.php")</tt> and you're set.

The PHP <tt>__autoload()</tt> method is deprecated; this library uses <tt>spl_autoload_register()</tt>.  The Factual Autoload will not mess with other libraries or frameworks.

#Getting Started
## Get an Oauth Key & Secret
Obtain an oauth key and secret from Factual at https://www.factual.com/api-keys/request.  Do not expose your _secret_ to third-parties or  distribute it in PHP code (reminder: that's why it is called 'secret').

## Test Your Integration and Environment
Run <tt>test.php</tt> on the *command line*: 

	'php test.php yourFactualKey yourFactualSecret [logfile]'
	
On windows remember to use the <tt>-f</tt> switch:
	
	'php -f test.php yourFactualKey yourFactualSecret [logfile]'  

This checks your PHP install environment and performs a number of unit tests. The script takes your key as parameter one, your secret is parameter two, and an optional output file as parameter three. By default it echoes to stdout.

## Using the Driver
Require the file 'Factual.php, and instantiate a <tt>factual</tt> object with the key and secret as parameters'
```php    
    //setup
    require_once('Factual.php');
    $factual = new Factual("yourOauthKey","yourOauthSecret");
```
The driver creates an authenticated handle to Factual and configures class loading on instantiation, so be sure to always instantiate a Factual object first.
    
## Simple Query Example

(Remember, first create a Factual object as we've done above.)
```php
    // Find 3 random records 
    $query = new FactualQuery;
    $query->limit(3);
    $res = $factual->fetch("places", $query);
	print_r($res->getData());
```	
## Full Text Search Example
```php
    // Find entities that match a full text search for Sushi in Santa Monica:
    $query = new FactualQuery;
	$query->search("Sushi Santa Monica");
    $res = $factual->fetch("places", $query);
	print_r($res->getData());
```
See <a href="https://github.com/Factual/factual-php-driver/wiki/Working-with-Query-Results">Working with Query Results</a> for details on iterating through the results of your query, and obtaining query metadata.

Unnecessary Reminder: we use <tt>print_r()</tt> in these examples so you can review the output visually.  Obviously, but worth a reminder nonetheless, you do not want to use <tt>print_r()</tt> in production.  

A boatload of tools are available in the driver to help you understand your request, and what the server is (or is not) returning:

#Debugging and Support
## Where to Get Help
If you have a question or are having any other kind of issue, such as unexpected data or strange behaviour from Factual's API (or you're just not sure WTF is going on), please hit us up on [Factual Support](http://support.factual.com/factual). Be sure to include the debug information, and what driver you are using; help us help you.  Provide as much information as you can, including:

  * All of the debug info output by the exception (above)
  * What you did to surface the bug -- specific code with values rather than variables please
  * What you expected to happen & what actually happened
  * Detailed stack trace and/or line numbers

##Debug Mode
The Factual object can be switched to debug mode, which will echo (to stderr) the cURL process and any other information we can provide:
```php
	$factual = new Factual($key,$secret);
	$factual->debug();
```
When debug mode is enabled, cURL status and exceptions are also output to stderr:

###cURL Debug Output
    * About to connect() to api.v3.factual.com port 80 (#0)
    *   Trying 107.20.247.254... * connected
    * Connected to api.v3.factual.com (107.20.247.254) port 80 (#0)
    > POST /t/2EH4Pz/f33527e0-a8b4-4808-a820-2686f18cb00c/submit HTTP/1.1
    User-Agent: anyMeta/OAuth 1.0 - ($LastChangedRevision: 174 $)
    Host: api.v3.factual.com
    Accept: */*
    X-Factual-Lib: factual-php-driver-v1.4.3
    Content-Type: application/x-www-form-urlencoded
    Authorization: OAuth realm="", oauth_signature_method="HMAC-SHA1",      
    oauth_signature="fjnDOdqNS8vYHxmalIy%2BS9dC2%2Bw%3D", 
    oauth_nonce="500c7f9dd13fc",                 
    oauth_timestamp="1342996381", oauth_token="", 
    oauth_consumer_key="9AlBWqvNukzwsv8qu4RTPztAKcTLYohAMxHmxtQl",                             
    oauth_version="1.0"
    Content-Length: 102

    < HTTP/1.1 400 Bad Request
    < access-control-allow-origin: *
    < age: 0
    < cache-control: max-age=2592000
    < Content-Type: application/json; charset=utf-8
    < Date: Sun, 22 Jul 2012 22:33:02 GMT
    < Server: nginx/1.0.15
    < Content-Length: 196
    < Connection: keep-alive
    < 
    * Connection #0 to host api.v3.factual.com left intact
    * Closing connection #0

###Exception Debug Output
    Array
    (
    [code] => 400
    [version] => 3
    [status] => error
    [error_type] => InvalidJsonArgument
    [message] => Parameter 'values' contains an error in its JSON syntax.  
                 For documentation, please see: http://developer.factual.com.
    [request] => http://api.v3.factual.com/t/2EH4Pz/f33527e0-a8b4-4808-a820-2686f18cb00c/submit
    [returnheaders] => Array
        (
            [access-control-allow-origin] => *
            [age] => 0
            [cache-control] => max-age=2592000
            [content-type] => application/json; charset=utf-8
            [date] => Sun, 22 Jul 2012 22:33:02 GMT
            [server] => nginx/1.0.15
            [content-length] => 196
            [connection] => keep-alive
        )

    [driver] => factual-php-driver-v1.4.3
    [method] => POST
    [body] => Array
        (
            [user] => testUser
            [values] => %7B%22factual_id%22%3A%22f33527e0-a8b4-4808-a820-2686f18cb00c%22%7D
        )

    )

##Exception Handling
If Factual's API indicates an error, a <tt>FactualApiException</tt> unchecked Exception will be thrown. Detailed debug information can be studied using debug mode (above) or calling FactualApiException::debug().  Here is an example of catching a <tt>FactualApiException</tt> and inspecting it:
```php
    try{
    	$query->field("badFieldName")->notIn("Los Angeles"); //this line borks 
    	$res = $factual->fetch("places", $query);
    } catch (FactualApiException $e) {
      	print_r($e->debug());
    }
```

##Call Introspection
A number of `Result` object methods allow introspection into the call itself.  These can be accessed at any time and do not require the `Factual` object to be set in Debug mode:

| Method        | Function    | Notes|
| ------------- |-------------|-------|
|`getCode()`    |Get http status code returned by Factual| Useful for re-directs from deprecation|
|`getHeaders()`    |Get http headers returned by Factual||
|`getTable()`    |Get table name queried||
|`getRawRequest()`    |Get url-encoded request string|Does not include auth component|
|`getRequest()`    |Get url-decoded request string|(As above)|
|`isEmpty()`    |Checks whether data was returned by Factual||
|`size()`    |Gets count of elements returned in this page of result set (not the total count)|
|`getVersion()`    |Get Factual API version||
|`getStatus()`    |Get the status returned by the Factual API server|e.g. "OK"|
|`getJSON()`    |Gets entire JSON string returned by Factual||

#Schema
The schema endpoint returns table metadata:
```php  
	$res = $factual->schema("places");
	print_r($res->getColumnSchemas());
```
Schema API Documentation: http://developer.factual.com/api-docs/#Schema

# Read
Use the read API call to query data in Factual tables with any combination of full-text search, parametric filtering, and geo-location filtering.

Read API documentation: http://developer.factual.com/api-docs/#Read

Related place-specific documentation:
* Categories: http://developer.factual.com/working-with-categories/
* Placerank, Sorting: http://developer.factual.com/search-placerank-and-boost/

##Read Filters
Use the follow syntax to perform searches against Factual data:

<table>
  <tr>
    <th>Parameter</th>
    <th>Description</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>filters</td>
    <td>Restrict the data returned to conform to specific conditions.

	For all possible Row Filters, see the eponymous section, below.

    </td>
    <td><tt>$query->field("name")->beginsWith("Starbucks")</tt></td>
  </tr>
  <tr>
    <td>include count</td>
    <td>Include a count of the total number of rows in the dataset that conform to the request based on included filters. Requesting the row count will increase the time required to return a response. The default behavior is to NOT include a row count. When the row count is requested, the Response object will contain a valid total row count via <tt>.getTotalRowCount()</tt>.</td>
    <td><tt>$query->includeRowCount()</tt></td>
  </tr>
  <tr>
    <td>geo</td>
    <td>Restrict data to be returned to be within a geographical range based.</td>
    <td>(See the section on Geo Filters)</td>
  </tr>
  <tr>
    <td>limit</td>
    <td>Maximum number of rows to return. Default is 20. The system maximum is 50. For higher limits please contact Factual, however consider requesting a download of the data if your use case is requesting more data in a single query than is required to fulfill a single end-user's request.</td>
    <td><tt>$query->limit(10)</tt></td>
  </tr>
  <tr>
    <td>search</td>
    <td>Full text search query string.</td>
    <td>
      Find "sushi":<br><tt>$query->search("sushi")</tt><p>
      Find "sushi" or "sashimi":<br><tt>$query->search("sushi, sashimi")</tt><p>
      Find "sushi" and "santa" and "monica":<br><tt>$query->search("sushi santa monica")</tt>
    </td>
  </tr>
  <tr>
    <td>offset</td>
    <td>Number of rows to skip before returning a page of data. Maximum value is 500 minus any value provided under limit. Default is 0.</td>
    <td><tt>$query->offset(150)</tt></td>
  </tr>
  <tr>
    <td>select</td>
    <td>What fields to include in the query results.  Note that the order of fields will not necessarily be preserved in the resulting JSON response due to the nature of JSON hashes.</td>
    <td><tt>$query->only("name,tel,category")</tt> or <tt>$query->select(array("name","tel","category"))</tt></td>
  </tr>
  <tr>
    <td>sort</td>
    <td>The field (or fields) to sort data on, as well as the direction of sort.  Supports $distance as a sort option if a geo-filter is specified.  Supports $relevance as a sort option if a full text search is specified either using the q parameter or using the $search operator in the filter parameter.  By default, any query with a full text search will be sorted by relevance.  Any query with a geo filter will be sorted by distance from the reference point.  If both a geo filter and full text search are present, the default will be relevance followed by distance.</td>
    <td><tt>$query->sortAsc("name")</tt></td>
  </tr>
  <tr>
    <td>threshold</td>
    <td> Set a threshold for filtering on the level of confidence that Factual has that places exist. Valid values are confident, default, or comprehensive. If the threshold parameter is not specified, it uses the value default.</td>
    <td><tt>$query->threshold("confident");</tt></td>
  </tr>
</table>  

##Extracting Data from Query Results
The drivers parse the JSON for you and return a _result_ object as a result of factual::fetch(), but you can work directly with JSON, Arrays, or Objects.  In these examples, $res is the _result_ object returned by an API query:
```php 
	//Get the original JSON (includes status and metadata)
	$res = $res->getJson();
	
	//Get the entities as array of arrays
	$res = $res->getData();
	
	//Get the entities as a JSON array
	$res = $res->getDataAsJSON();	

	//iterate through the result records, just like an array
	foreach ($res as $entity){
	//your code
	}
```	
To help with debugging, we also provide in the response object metadata about the query and the response.  See the section on Call Introspection, above.

##Field Selection
By default your queries will return all fields in the table. You can use the only modifier to specify the exact set of fields returned. For example:
```php
    // Build a Query that only gets the name, tel, and category fields:
	$query = new FactualQuery;
	$query->limit(10);    
    $query->only("name,tel,category");
	$res = $factual->fetch("places", $query);
	print_r($res->getData());  
```
##Row Filters
The driver supports various row filter logic. See the [Row Filter API documentation](http://developer.factual.com/display/docs/Core+API+-+Row+Filters).

Examples:
```php
    // Build a query to find places whose name field starts with "Starbucks"
    $query = new FactualQuery;
    $query->field("name")->beginsWith("Starbucks");
    $res = $factual->fetch("places", $query);
	print_r($res->getData());  

    // Build a query to find places with a blank telephone number
    $query = new FactualQuery;
    $query->field("tel")->blank();
    $res = $factual->fetch("places", $query);
	print_r($res->getData());
```
### Supported Row Filter Logic

<table>
  <tr>
    <th>Predicate</th>
    <th>Description</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>equal</td>
    <td>equal to</td>
    <td><tt>$query->field("region")->equal("CA")</tt></td>
  </tr>
  <tr>
    <td>notEqual</td>
    <td>not equal to</td>
    <td><tt>$query->field("region")->notEqual("CA")</tt></td>
  </tr>
  <tr>
    <td>search</td>
    <td>full text search</td>
    <td><tt>$query->field("name")->search("fried chicken")</tt></td>
  </tr>
  <tr>
    <td>in</td>
    <td>equals any of. Requires array.</td>
    <td><tt>$query->field("region")->in(array("MA", "VT", "NH", "RI", "CT"))</tt></td>
  </tr>
  <tr>
    <td>notIn</td>
    <td>does not equal any of. Requires array.</td>
    <td><tt>$query->field("locality")->notIn(array("Los Angeles","Philadelphia")</tt></td>
  </tr>
  <tr>
    <td>beginsWith</td>
    <td>begins with</td>
    <td><tt>$query->field("name")->beginsWith("b")</tt></td>
  </tr>
  <tr>
    <td>notBeginsWith</td>
    <td>does not begin with</td>
    <td><tt>$query->field("name")->notBeginsWith("star")</tt></td>
  </tr>
  <tr>
    <td>beginsWithAny</td>
    <td>begins with any of. Requires array.</td>
    <td><tt>$query->field("name")->beginsWithAny(array("star", "coffee", "tull"))</tt> </td>
  </tr>
  <tr>
    <td>notBeginsWithAny</td>
    <td>does not begin with any of. Requires array.</td>
    <td><tt>$query->field("name")->notBeginsWithAny(array("star", "coffee", "tull"))</tt></td>
  </tr>
  <tr>
    <td>blank</td>
    <td>is blank or null</td>
    <td><tt>$query->field("tel")->blank()</tt></td>
  </tr>
  <tr>
    <td>notBlank</td>
    <td>is not blank or null</td>
    <td><tt>$query->field("tel")->notBlank()</tt></td>
  </tr>
  <tr>
    <td>greaterThan</td>
    <td>greater than</td>
    <td><tt>$query->field("rating")->greaterThan(7.5)</tt></td>
  </tr>
  <tr>
    <td>greaterThanOrEqual</td>
    <td>greater than or equal to</td>
    <td><tt>$query->field("rating")->greaterThanOrEqual(7.5)</tt></td>
  </tr>
  <tr>
    <td>lessThan</td>
    <td>less than</td>
    <td><tt>$query->field("rating")->lessThan(7.5)</tt></td>
  </tr>
  <tr>
    <td>lessThanOrEqual</td>
    <td>less than or equal to</td>
    <td><tt>$query->field("rating")->lessThanOrEqual(7.5)</tt></td>
  </tr>
    <tr>
    <td>includes</td>
    <td>includes this value</td>
    <td><tt>$query->field("category_ids")->includes(10)</tt></td>
  </tr>
    </tr>
    <tr>
    <td>includesAny</td>
    <td>includes any of these values. Requires array.</td>
    <td><tt>$query->field("cuisine")->includesAny(array("sushi","bistro"))</tt></td>
  </tr>
</table>

### AND
Queries support logical AND'ing your row filters. For example:
```php
    // Build a query to find entities where the name begins with "Coffee" AND the telephone is blank:
    $query = new FactualQuery;
    $query->_and(
    	array(
       		$query->field("name")->beginsWith("Coffee"),
  	   		$query->field("tel")->blank()
  	   	)
	);
	$res = $factual->fetch("places", $query);
	print_r($res->getData());
```    
Note that all row filters set at the top level of the Query are implicitly AND'ed together, so you could also do this:
```php	
    //Combined query alternative syntax
    $query = new FactualQuery;
    $query->field("name")->beginsWith("Coffee");
    $query->field("tel")->blank();
    $res = $factual->fetch("places", $query);
	print_r($res->getData());
```
### OR
Queries support logical OR'ing your row filters. For example:
```php
    // Build a query to find entities where the name begins with "Coffee" OR the telephone is blank:
    $query = new FactualQuery;
    $query->_or(array(
       	$query->field("name")->beginsWith("Coffee"),
  	   	$query->field("tel")->blank()
  	   )
	);	
	$res = $factual->fetch("places", $query);
	print_r($res->getData());
```	
### Combined ANDs and ORs
You can nest AND and OR logic to whatever level of complexity you need. For example:
```php
    // Build a query to find entities where:
    // (name begins with "Starbucks") OR (name begins with "Coffee")
    // OR
    // (name full text search matches on "tea" AND tel is not blank)
    $query = new FactualQuery;    
    $query->_or(array(
        $query->_or(array(
            $query->field("name")->beginsWith("Starbucks"),
            $query->field("name")->beginsWith("Coffee")
            )
        ),
        $query->_and(array(
            $query->field("name")->search("tea"),
            $query->field("tel")->notBlank()
        	)
        )
      )
    );
	$res = $factual->fetch("places", $query);
	print_r($res->getData());
```
##Geo Filters
Geo Filters provide the means to query Factual for entities located within a circle, rectangle, or near a point:

### Circle (Point/Radius)
```php
	// Find entities located within 5000 meters of a latitude, longitude
	$query->within(new FactualCircle(34.06018, -118.41835, 5000)); //lat, lon, radius
```
* When using a point/radius geo filter, distance (in meters) from the point will be returned in the response packet under the $distance key. This distance is calculated as the crow flies.
* Point/radius queries are implemented as a point at the center of a square with sides twice the radius.
* The radius for point/radius queries is limited to 15 km.

### Point
```php
	// Find entities located adjacent to a latitude, longitude
	$query->at(new FactualPoint(34.06018, -118.41835));
```
* Point queries are just shortcuts for a circle query, with an implied radius of 500m.

### Rectangle
```php
	// Find entities located within a box over LA
	$query->within(new FactualRectangle(34.06110,-118.42283,34.05771,-118.41399)); 
```
* Points order is [top,left],[bottom,right]
* Points are always ordered as [latitude, longitude].

### Notes on Geo Filters
* Distance is always specified in meters
* The maximum area that any geo query can encompass is 900 km2
* Sorting by distance requires a special $distance operator.  Be sure to escape the dollar sign:
```php
	$query->sortAsc("\$distance"); //order results 
```
##Search By Factual ID (FetchRow)
The <tt>fetchrow()</tt> method retrieves entities by Factual ID.  Is a simple shortcut equivalent to a filter on Factual ID, and returns an array with one element like a regular read:
```php
	//get started
	require_once('Factual.php');
	$factual = new Factual($key,$secret);
	//assign vars
	$factualID = "03c26917-5d66-4de9-96bc-b13066173c65";
	$tableName = "places";
	//fetch row
	$res = $factual->fetchRow($tableName, $factualID);
	print_r($res->getData());
```
##Paging Through Results: Limit and Offset
You can use limit and offset to support basic results paging. For example:
```php
    // Build a Query with offset of 150, limiting the page size to 10:
    $query = new FactualQuery;
	$query->limit(10);
	$query->offset(150);
	$res = $factual->fetch("places", $query);
	print_r($res->getData());
```
NOTE: the driver is designed to access Factual's API at runtime.   We enforce a deep paging limit of 500 rows for any unique combination of filters: http://developer.factual.com/data-docs/

This is the polite way of saying we'd rather you did not use our API to scrape Factual data for permanent retention.  We do provide downloads of the entire dataset: contact partnership@factual.com

##Total Row Count
Factual does not return the total number of records matching your filter by default -- there is a modest overhead in calculating this. We do however provide you the option of retrieving it explicitly.

To obtain the total number of all entities that meet your query criteria, set the parameter in the query object using <tt>FactualQuery::includeRowCount()</tt> method:
```php
	$query = new FactualQuery;
	$query->field("postcode")->equal("95008");
	$query->includeRowCount();
```
After you've made the query using <tt>Factual::fetch()</tt>, the resultant number can be obtained with the <tt>ReadResponse::getTotalRowCount()</tt> call on the response object:
```php
	$res = $factual->fetch("places", $query); 
	print_r($res->getTotalRowCount()); 
```
API Documentation:  https://github.com/Factual/factual-php-driver/wiki/Total-Row-Count

##Sorting Results
Factual will sort your query results for you, on a field-by-field basis. Simple examples:
```php
    // Build a Query to find 10 random entities and sort them by name, ascending:
    $query = new FactualQuery;
    $query->limit(10);
    $query->sortAsc("name");
    $res = $factual->fetch("places", $query);
	print_r($res->getData());  
``` 
You can specify more than one sort, and the results will be sorted with the first sort as primary, the second sort or secondary, and so on:
```php
    // Build a Query to find 20 random entities, sorted ascending primarily by region, then by locality, then by name:
	$query = new FactualQuery;
	$query->limit(10);
	$query->sortAsc("region");
	$query->sortAsc("locality");
	$query->sortDesc("name");
	$res = $factual->fetch("places", $query);
	print_r($res->getData());
```	
Sorting by distance requires a special $distance operator.  Be sure to escape the dollar sign in PHP:
```php
	$query->sortAsc("\$distance"); //order results 
```
Read API Documentation: http://developer.factual.com/api-docs/#Read

## Facets
Facets is a special call that returns summary row counts grouped by values of a specific attribute -- think of this as a combined <tt>COUNT()</tt> and <tt>GROUP BY</tt> query in SQL.  

Use Facets to analyze the results of your query by count: for example, you may wish to query all businesses within 500m of a location, group those businesses by category, and get a count of each.  

### Facets Example
```php
//Finds the top twenty-five countries containing places with the string 'Starbucks'
$query = new FacetQuery("country"); //name the field to facet on in the constructor
$query->search("starbucks"); //search on 'Starbucks' using the usual paramateric filters
$query->limit(15); //show no more than 15 results
$query->minCountPerFacet(10); //only show countries with more than 10 results
$res = $factual->fetch("global", $query); //perform the query using Factual::fetch() as usual
print_r($res->getData()); //dump results out as an array
```	
The response looks like:

	Array
	(
	    [country] => Array
		(
		    [us] => 11019
		    [ca] => 902
		    [gb] => 434
		    [cn] => 194
		    [de] => 174
		    [tw] => 121
		    [ph] => 78
		    [au] => 69
		    [tr] => 68
		    [id] => 55
		    [fr] => 47
		    [sg] => 41
		    [mx] => 33
		    [ch] => 31
		    [hk] => 27
		)
	)

You cannot facet on all fields, only those configured for faceting by Factual.  Use the <tt>schema</tt> call to determine which fields can be faceted: if the faceted attribute of the schema is <tt>true</tt>, you can facet. 

### Facets Parameters

<table>
  <tr>
    <th>Parameter</th>
    <th>Description</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>select</td>
    <td>Array of comma-delimited string of field names on which facets should be generated, included as the constructor parameter to the FacetQuery.  The response will not necessarily be ordered identically to this list, nor will it reflect any nested relationships between fields.</td>
    <td><tt>$query = new FacetQuery("region,locality");</tt></td>
  </tr>
  <tr>
    <td>min_count</td>
    <td>Include only facets that have this minimum count. Must be zero or greater. The default is 1. </td>
    <td><tt>$query->minCountPerFacet(2)</tt></td>
  </tr>
  <tr>
    <td>limit</td>
    <td>The maximum number of unique facet values that can be returned for a single field. Range is 1-250. The default is 20.</td>
    <td><tt>$query->limit(10)</tt></td>
  </tr>
</table>  

You can also employ the filters, include count, geo and search parameters with Facets, like any other Read query.

Facets API Documentation: http://developer.factual.com/api-docs/#Facets

#Write
The Submit endpoint allows you to add a record to Factual, or to update an existing record.  To delete a record, see the flag() method, below.  

Unverified accounts are restricted from making submit API calls.  Log in to Factual, and verify your account at  http://www.factual.com/keys/verify.

## Syntax
Strictly speaking, we do an 'UPSERT' when you contribute data: we determine if the entity already exists, and update it appropriately; if not we create a new entity.  This avoids dupes and allows you to contribute data even if you do not know the Factual ID.  However, if you do, please include it to remove any ambiguity using the <tt>FactualSubmittor::setFactualID()</tt> method.  The only difference between updating an extant record and adding a new one is this inclusion of the Factual ID:
```php
	$submitterator->setFactualID("f33527e0-a8b4-4808-a820-2686f18cb00c");
```
You can determine whether the entity you submitted is new:
```php
	//is the submission a new entity?
	$isNew = $res->isNew();
```
However, It's always a good idea to obtain the Factual ID from a Submit Result, and store it against the submitted entity:
```php
	//get Factual ID of submitted entity
	$factualID = $res->getFactualID();
```
We attempt to return a Factual ID with every Submit Result; it is good practice to make a note of this and store it, and verify it against the ID you submitted.  In a few cases (such as if the entity you submitted has been deprecated), we may return a Factual ID different from the one you submitted.  In very limited circumstances, submissions may not be matched to records in realtime, and thus no factual_id will be provided.

## Submit Parameters

<table>
  <tr>
    <th>Parameter</th>
    <th>Description</th>
    <th>Required?</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>user</td>
    <td>An arbitrary token representing the end user who is submitting the data. best to keep this annymous; we don't want to know who your users are, but do use this token to model and weigh the quality of their individual contributions</td>
    <td>Yes</td>
    <td><tt>setUserToken("387523")</tt></td>
  </tr>
  <tr>
    <td>values</td>
    <td>The data to submit; field names from the table schema, mapped to values</td>
    <td>Yes</td>
    <td><tt>setValue("locality","Palo Alto")</tt><br/><tt>setValue("address","425 Sherman Ave.")</tt></td>
  </tr>
  <tr>
    <td>comment</td>
    <td>Any english text comment that may help explain the submit</td>
    <td>No</td>
    <td><tt>setComment("New Office")</tt></td>
  </tr>
  <tr>
    <td>reference</td>
    <td>A reference to a URL, title, person, or other source of the submitted data</td>
    <td>No</td>
    <td><tt>setReference("http://www.factual.com/contact/new")</tt></td>
  </tr>
  <tr>
    <td>strict</td>
    <td>If set to true, Factual will reject submissions that contain invalid fields. (default is false.) See below for more.</td>
    <td>No</td>
    <td><tt>strict=true</tt></td>
  </tr>  
  <tr>
    <td>clear_blanks</td>
    <td>If set to true, any field hashed to "" will be cleared. Use with care. (default is false.) See below for more. </td>
    <td>No</td>
    <td><tt>strict=true</tt></td>
  </tr>  
</table>

## Strict Mode
By default, Factual's API will optimistically accept all field provided in the values parameter (with the exception of fields that have explicitly been marked as "unwriteable"). This makes it easier to pass what data you have to Factual, regardless of how precisely well it fits the schema of the table you are correcting.  

But by being cool and froody, we introduce a downside: you will not be warned if data you are providing may be discarded due to simple mismatches between field names and what you've put in your values parameter.  For example, if you misspell "category" as "catogory".  

Setting the strict parameter to true will cause the system to automatically verify the names of the values you provided against the Factual schema.  Any fields that do not match will cause your entire submission to automatically be rejected with a 400 error.
```php
	//create submittor object and set strictMode to true
	$submitterator = new FactualSubmittor;
	$submitterator->strictMode(); //sets to true
	
	//set Factual ID and set new values
	$submitterator->setFactualID("03c26917-5d66-4de9-96bc-b12066172c65");
	$submitterator->setValue("locality","Salaberry-de-Valleyfield");    
	$submitterator->setValue("addresss","80 Rue Masson"); //this will bork the query b/c the field name is invalid
	
	//make request
	$res = $factual->submit($submitterator);
```
## Clear Blanks
By default Factual employs a separate call to clear a field of information.  This is because it is so easy to send empty values with out really intending to clear the corresponding attribute.  Using clearBlanks() circumvents this safeguard so that the clearing of fields can be accomplished in a single API call.  For example:
```php
	//create submittor object and set clearBlanks to true
	$submitterator = new FactualSubmittor;
	$submitterator->clearBlanks(); //sets to true
	
	//set Factual ID and set new values
	$submitterator->setFactualID("03c26917-5d66-4de9-96bc-b12066172c65"); //the Factual ID for our LA office
	$submitterator->setValue("locality","Salaberry-de-Valleyfield");    
	$submitterator->setValue("address","80 Rue Masson");
	$submitterator->setValue("address_extended","");
	
	//make request
	$res = $factual->submit($submitterator);
```	
will behave identically to sending a submit request with the name, address and a clear request for the address_extended, to update the address and remove the existing extended address.

##Delayed Writes
Sometimes, if rarely, the write is cached and not written directly.  In these instances, neither a commitID nor a Factual ID will be returned.

As this is an expected, if unusual program flow, it is best to check submissions with the isDelayed() method:

	//make request
	$res = $factual->submit($submitterator);
	if (!$res->isDelayed()){
		//store Factual ID and Commit ID
	} else {
		//do omething else
	}

##Submit Examples

<b><ex>Add data to Factual's Places table:</ex></b><br>
```php
	//Create new submittor object and assign table to write to
	$submitterator = new FactualSubmittor;
	$tableName = "us-sandbox"; //the table we are writing to

	//add individual user token & table name (required)
	$submitterator->setUserToken("1235");   //this your assigned the token of the individual user
	$submitterator->setTableName($tableName); //the table name containing the record

	//add the values to update
	$submitterator->setValue("locality","Salaberry-de-Valleyfield");	
	$submitterator->setValue("address","80 Rue Masson");

	//this other metadata is optional, but welcome
	$submitterator->setComment("This is a test update");
	$submitterator->setReference("http://example.com/");

	//make request
	$res = $factual->submit($submitterator);

	//confirm status of submission
	if ($res->success()){
		if ($res->isDelayed){
			echo "OK, but delayed write\n";
		} else {
			echo "OK\n";
		}
	} else {
		echo "Borked\n";
	}
```
<b><ex>Add data as array:</ex></b><br>
This does the same as the previous example, but takes an associative array as parameter:
```php
	//Create new submittor object and assign table to write to
	$submitterator = new FactualSubmittor;
	$tableName = "us-sandbox"; //the table we are writing to

	//add individual user token & table name (required)
	$submitterator->setUserToken("1235");   //this your assigned the token of the individual user
	$submitterator->setTableName($tableName); //the table name containing the record

	//add the values to update
	$data = array(
		'locality' => "Salaberry-de-Valleyfield",
		'address' => "80 Rue Masson"
	);
	$submitterator->setValues($data);

	//this other metadata is optional, but welcome
	$submitterator->setComment("This is a test update");
	$submitterator->setReference("http://example.com/");

	//make request
	$res = $factual->submit($submitterator);

	//confirm status of submission
	if ($res->success()){
		echo "OK\n";
	} else {
		echo "Borked\n";
	}
```
<b><ex>Determine whether Factual considered your Submit to be a new entity:</ex></b><br>
Use <tt>SubmitResponse::isNew()</tt>:
```php
	echo "New Response?:". (bool)$res->isNew();
```
<b><ex>Correct the latitude and longitude of a specific entity in Factual's Places table:</ex></b><br>
```php
	//Create new submittor object and assign table to write to
	$submitterator = new FactualSubmittor;
	$tableName = "us-sandbox"; 

	//add individual user token & table name (required)
	$submitterator->setUserToken("1235");   //this your assigned the token of the individual user
	$submitterator->setTableName($tableName); //the table name containing the record

	$submitterator->setFactualID("03c26917-5d66-4de9-96bc-b13066173c65"); //the Factual ID for our LA office
	$submitterator->setValue("longitude",-118.41822);	
	$submitterator->setValue("latitude",34.06025);

	//make request
	$res = $factual->submit($submitterator);
```
<b><ex>Correct the business name of a specific entity in Factual's Places table:</ex></b><br>
```php
	//Create new submittor object and assign table to write to
	$submitterator = new FactualSubmittor;
	$tableName = "us-sandbox"; 

	//add individual user token & table name (required)
	$submitterator->setUserToken("1235");   //this your assigned the token of the individual user
	$submitterator->setTableName($tableName); //the table name containing the record

	//set values against specific Factual ID
	$submitterator->setFactualID("0cb6c5b0-cd40-012e-5616-003048cad9da"); //the Factual ID of the entity to change
	$submitterator->setValue("name", "W Austin");

	//make request
	$res = $factual->submit($submitterator);
```
<b><ex>Add a neighborhood to a specific entity in Factual's Places table:</ex></b><br>
```php
	//Create new submittor object and assign table to write to
	$submitterator = new FactualSubmittor;
	$tableName = "us-sandbox"; 

	//add individual user token & table name (required)
	$submitterator->setUserToken("1235");   //this your assigned the token of the individual user
	$submitterator->setTableName($tableName); //the table name containing the record

	//set values against specific Factual ID
	$submitterator->setFactualID("0cb6c5b0-cd40-012e-5616-003048cad9da"); //the Factual ID of the entity to change
	$submitterator->setValue("neighborhood", "Downtown Austin");
		
	//make request
	$res = $factual->submit($submitterator);
```	
<b><ex>Delete the neighborhood of a specific entity in Factual's Places table:</ex></b><br>	
```php	
	//Create new submittor object and assign table to write to
	$submitterator = new FactualSubmittor;
	$tableName = "us-sandbox"; 

	//add individual user token & table name (required)
	$submitterator->setUserToken("1235");   //this your assigned the token of the individual user
	$submitterator->setTableName($tableName); //the table name containing the record

	//set values against specific Factual ID
	$submitterator->setFactualID("0cb6c5b0-cd40-012e-5616-003048cad9da"); //the Factual ID of the entity to change
	$submitterator->removeValue("neighborhood"); //yoink
		
	//make request
	$res = $factual->submit($submitterator);	
```
Submit API Documentation: http://developer.factual.com/api-docs/#Submit
Submit API documentation for Places: http://developer.factual.com/write-api/

##Flagging Data for Editorial Attention
The Flag feature provides developers and editorial teams the ability to 'flag' problematic entities in tables for Factual editorial review. Use this feature to request an entity be deleted, flag an entity as a dupe or spam, note it does not exist, or just ask the Factual editors to check it out.

### Flag Example
```php
	//get started
	require_once('Factual.php');
	$factual = new Factual($key,$secret);
	//create a new flagger object to hold our parameters
	$flagger = new FactualFlagger;
	//add required parameters
	$flagger->setFactualID("f33527e0-a8b4-4808-a820-2686f18cb00c"); //ID to check
	$flagger->setTableName("2EH4Pz"); //name of table
	$flagger->setUserToken("testUser"); //arbitrary token of individual user
	$flagger->setProblem("duplicate");
	//add optional parameters
	$flagger->setComment("Found by user");
	$flagger->setReference("Original entity on http://example.com");
	//make request
	$res = $factual->flag($flagger);
	//check for success
	if ($res->success()){
		echo "OK\n";
	} else {
		echo "Borked\n";
	}
```
Flag API Documentation: http://developer.factual.com/api-docs/#Flag

##Clearing Attribute Values
The Clear() method allows you to clear or remove attribute values from a Factual record.

### Clear Parameters

<table>
  <tr>
    <th>Parameter</th>
    <th>Description</th>
    <th>Required?</th>
    <th>Example</th>
  </tr>
  <tr>
    <td>user</td>
    <td>An arbitrary token representing the end user who is submitting the data.</td>
    <td>Yes</td>
    <td><tt>$clear->setUserToken("twb")</tt></td>
  </tr>
  <tr>
    <td>fields</td>
    <td>The attribute fields to be cleared.</td>
    <td>Yes</td>
    <td><tt>$clear->clearValues(array("longitude","latitude"))</tt> or <tt>$clear->clearValue("name")</tt></td>
  </tr>
  <tr>
    <td>comment</td>
    <td>Any text that may help explain the clear/submission. English only please.</td>
    <td>No</td>
    <td><tt>$clear->setComment("submitted via email by owner");</tt></td>
  </tr>
  <tr>
    <td>reference</td>
    <td>A reference to a URL, title, person, etc. that is the source of the submitted data.</td>
    <td>No</td>
    <td><tt>setReference("http://www.factual.com/contact/new")</tt></td>
  </tr>
</table>

### Clear Examples
<b><ex>Clear the value of the longitude and latitude in an existing entity:</ex></b><br>
```php
	//create clearor object
	$clearor = new FactualClearor;
	
	//assign the ID and se the values to clear/wipe
	$clearor->setFactualID("1d93c1ed-8cf3-4d58-94e0-05bbcd827cba");//this is required
  	$clearor->clearValue("longitude");
  	$clearor->clearValue("latitude");
  	
  	//set tablename and other metadata about this submission
  	$clearor->setTableName("us-sandbox"); //where can we find this entity
  	$clearor->setUserToken("8363b7"); //the user/editor who is writing to us through you
  	
	//make request
	$res = $factual->clear($clearor);
```
As an alternative you can use <tt>clearValues()<tt> to clear multiple attributes:
```php
	$values = array("longitude","latitude");
	$clearor->clearValues($values);
```
Clear API Documentation: http://developer.factual.com/api-docs/#Clear

# Crosswalk
Factual's Crosswalk feature lets you "crosswalk" the web by looking up Factual entities by the URL of other web authorities.  

Note that as of v1.4.3, crosswalk requests are treated as any other table read -- this means that you can access the Crosswalk table using normal search and filters, as in the example below. As of v.1.5.0 all deprecated Crosswalk functions have been removed.

## Places Crosswalk Example
```php
    // Get all Crosswalk data for a specific Place, using its Factual ID:
    $query = new FactualQuery;    
	$query->field("factual_id")->equal("97598010-433f-4946-8fd5-4a6dd1639d77");	 
	$res = $factual->fetch("crosswalk", $query);
	print_r($res->getData());
```
Crosswalk API Documentation: http://developer.factual.com/places-crosswalk/

#Match
Factual Match allows you to match your own data against Factual's.  We return the ID of the matching entity (only the ID) if we are certain of a match.

If you are looking to enrich your entities with additional data on matching, see the [Factual Resolve](https://github.com/Factual/factual-php-driver/wiki/Resolve).  This service is designed for high-volume entity matching only.  Match is almost identical to Resolve, but we only return Factual IDs, and queries do not count against users' quotas.

Generally the more information you provide the service, the better we are able to make a match.

## Finding a Match
Use the common query structure to add known attributes to the query:
```php
    //Build the query
	$query = new MatchQuery();
	$query->add("name", "Buena Vista Cigar Club");
	$query->add("latitude", 34.06);
	$query->add("longitude", -118.40);
	//perform the query
	$res = $factual->fetch("places", $query);
```
And then see if we found a match:	
```php	
	$match = $res->getMatched()); //FALSE == no match, Factual ID == match	
```
## Shortcut Method
Alternatively use the shortcut method in the <tt>Factual</tt> object:
```php	
	//assing tablename
	$tableName = "places";
	//create values array
	$vars = array(
		"name"=>"Buena Vista Cigar Club",
		"latitude"=>34.06,
		"longitude"=>-118.40
	);
	$res = $factual->match($tableName,$vars);
	$match = $res->getMatched()); //FALSE == no match, Factual ID == match	
```	
Match API Documentation: http://developer.factual.com/api-docs/#Match

#Resolve
Use Resolve to match your data against Factual's: we return everything we know about the entity allowing you to enrich or dedupe your own content.

Note that we provide a separate endpoint that returns Factual IDs only: Factual Match, above.  Use Match when you want to attach Factual IDs to your entities at high volume and call quotas; use Resolve when you want to enrich your entities with our data.
 
## Resolve Example
Use the common query structure to add known attributes to the query:
```php
    // Get all entities that are possibly a match
	$query = new ResolveQuery();
	$query->add("name", "Buena Vista Cigar Club");
	$query->add("latitude", 34.06);
	$query->add("longitude", -118.40);
	$res = $factual->fetch("places", $query);	
```   
And then use methods on the result object to determine resolution:
```php
    //Did the entity resolve? (returns bool)
    $isResolved = $res->isResolved();
    
    //If so, get it:
    $resolvedEntity = $res->getResolved();
```   
### Shortcut Method    
Alternatively use the shortcut to return the resolved entity OR null if no resolution:
```php
	//Resolve and return
	$tableName = "places";
	$vars = array(
		"name"=>"Buena Vista Cigar Club",
		"latitude"=>34.06,
		"longitude"=>-118.40
	);
	$res = $factual->resolve($tableName,$vars);
	print_r($res->getResolved()); //FALSE == no match, Array of entity data == resolved
```      
Resolve API Documentation: http://developer.factual.com/api-docs/#Resolve

# World Geographies
While Factual's <tt>places</tt> table provides access to the world's business and landmarks, our <tt>world-geographies</tt> table provides structured access to over 5.2 million geographies with 8.3 million name variants in 250 countries.  Use our World Geographies table to lookup placenames, see how one place relates to another, and translate placenames between multiple languages.

## World Geographies Example
```php
	//find all localities (towns and cities) called "Wayne" in the US 
	$query = new FactualQuery;
	$query->field("name")->equal("wayne");
	$query->field("country")->equal("us");
	$query->field("placetype")->equal("locality");	//we don't want counties, etc.
	$query->only("name,placetype,longitude,latitude"); //"take only what you need from me.."(singing)
	$res = $factual->fetch("world-geographies", $query);
	print_r($res->getData()); 
```
World Geographies Data Documentation: http://www.factual.com/products/world-geographies

# Place Categorization
All Factual Places are classified into one of over 400 categories. 

## Searching by Categories
Search the category_id field:
```php
	$category = "107"; //landmarks
	$query = new FactualQuery;	
	$query->field("category_ids")->in($category); //retrieves this category and all its descendants
	$res = $factual->fetch("places-us", $query); 	
	print_r($res->getData());
```
Places Category Documentation: http://developer.factual.com/working-with-categories/

# Global Products
Factual <tt>Global Products</tt> provides detailed product data for over 500,000 of the most popular consumer packaged goods in the US, including your favorite health, beauty, food, beverage, and household products. With Global Products, you can access key product attributes, find products using powerful search tools or UPC lookup, and connect to product
pages across the web.  See the [Global Products API documentation](http://developer.factual.com/display/docs/Products+-+CPG) for details on this fully operational battlestation.

## Global Products Examples
```php
	$tableName = "products-cpg";

	//Search for products containing the word "shampoo"
	$query = new FactualQuery;
	$query->search("shampoo");
	$res = $factual->fetch($tableName, $query); 
	print_r($res->getData());
	
	//Same search as above, but filter the search results to include only the brand "pantene"
	$query = new FactualQuery;
	$query->search("shampoo");
	$query->field("brand")->equal("pantene"); //don't hate me because I'm beautiful
	$res = $factual->fetch($tableName, $query); 
	print_r($res->getData());	
	
	//Same search as above, with added filter for products that are 12.6 oz.
	$query = new FactualQuery;
	$query->search("shampoo");
	$query->field("brand")->equal("pantene");
	$query->field("size")->search("12.6 oz"); //we use 'search' b/c sometime it is 'fl oz' or just 'oz'
	$res = $factual->fetch($tableName, $query); 
	print_r($res->getData());	
	
	//Search on UPC
	$query = new FactualQuery;
	$query->field("upc")->equal("052000131512"); 
	$res = $factual->fetch($tableName, $query); 
	print_r($res->getData());
	
	//Find all beverages (filter by category)
	$query = new FactualQuery;
	$query->field("category")->equal("beverages"); 
	$res = $factual->fetch($tableName, $query); 
	print_r($res->getData());	
	
	//Count all beverage products
	$query = new FactualQuery;
	$query->field("category")->equal("lip makeup"); 	
	$query->includeRowCount(); //this tells the API to calculate this value (modest overhead)
	$res = $factual->fetch($tableName, $query); 
	print_r($res->getTotalRowCount());	
```
#Raw Requests
This driver primarily offers convenience: it signs requests, builds conformant queries, and structures responses. 

However we do provide an option where the PHP Driver will perform OAuth authentication and parameter encoding only, allowing you to pass 'raw' key/value parameters to our API for debugging, implementing API features not yet codified in the driver, or simply satisfying masochistic tendencies.

These methods sign, encode, and submit the request. Responses are raw JSON, not a Factual response object.

## Raw GET
Use for all GET operations, basically all queries and other read-only calls to the Factual service.
```php
	//Raw Get Test
	$path = "t/global";
	$params['filters'] = "{\"\$and\":[{\"country\":{\"\$eq\":\"CA\"}},{\"locality\":{\"\$eq\":\"Toronto\"}}]}";
    $params['limit'] = 50;
    $params['include_count'] = true;
	$res = $factual->rawGet($path,$params);
	print_r($res);
```
In the above example we've escaped the JSON so it parses.  As a cheeky but more lengthly alternative, you can create the filter as a nested array and json encode it, obviating the need to escape the JSON string:
```php
	//create each filter as an array
	$countryFilter = array (
			'country'=> array (
				'$eq'=>"CA"
			)
		);
	$localityFilter = array (
			'locality'=> array (
				'$eq'=>"Toronto"
			)
		);		
	//combine filters
	$filter = array(
		'$and' => array($countryFilter,$localityFilter)	
	);
	$params['filters'] = json_encode($filter); //json encode
```
Generally tho, stick with the parametric filter methods; the raw reqest mode is really for debugging.

##Raw POST
Use for all POST operations: primarily the Submit and Flag APIs.
```php
	//Raw POST Test
	$path = "t/2EH4Pz/f33527e0-a8b4-4808-a820-2686f18cb00c/flag";
	$post['user'] = "testUser";
	$post['problem'] = "spam";
	$post['comment'] = "What do you mean 'Urgghh'? I don't like spam!";
	$res = $factual->rawPost($path,$post);
	print_r($res);
```
#Boost
The Boost API helps Factual address the stateless aspect of HTTP to improve search results.  It enables you to signal to Factual that a specific row returned by full-text search in a read API call should be a prominent result for that search. The Factual ID of the specified row does not need to be in the response to a read request. E.g., you may use boost to signal that a desired search result (identified by its Factual ID) is preferred.

This code picks up after the search.  You'll need to squirrel away the query in the user session or similar:

```php
  $factual = new Factual($key,$secret); 
	$boost = new FactualBoost; //create the boost object for populating
	$boost->setQueryString("chipotle palo alto"); //use for free text queries only
	$boost->setUserToken("user1234"); //anon. user token for this session. Optional, but helps us if poss.
	$boost->setFactualID("71df2b80-bef8-012e-5614-003048cad9da"); //the ID of the entity selected by the user
	$boost->setTableName("places-us"); //table the query was made against
	$res = $factual->boost($boost);
	//test for success. Just for debugging, of course.
	if ($res->success()){
		echo "Boosted";
	} else {
		echo "Borked";
	}

```

Note that the boost API will not result in a real-time refinement of search results or a user-customized search experience. Boost simple enables longer term enhancement of overall search result quality through the Factual API.

Boost API Documentation: http://developer.factual.com/api-docs/#Boost

#Diffs
The Diffs API is provided by Factual for use by our Download Data Partners -- those who are using a complete copy of Factual data remotely.  The Diffs API produces atomic JSON updates to the remote dataset.  See the [Factual Diffs API documentation]("http://example.com") for a complete overview.

## A Single Diff
A single diff consists of a complete JSON array.  The diffs feed is a stream of these encapusltated diffs.  A single diff:

	Array
	(
	    [factual_id] => 0249bc16-8912-4653-81a5-e07e0964c343
	    [type] => update
	    [payload] => Array
	        (
	            [region] => ON
	            [geocode_accuracy] => 7.5
	            [fax] => (416) 781-2744
	            [website] => http://www.tasco.net
	            [tel] => (416) 781-9145
	            [postcode] => M6B 3T8
	            [country] => ca
	            [category] => Services and Supplies > Home Improvement > Home Appliances
	            [address] => 11160 Yonge St
	            [name] => Tasco Distributors
	            [locality] => Richmond Hill
	            [longitude] => -79.4543685913
	            [latitude] => 43.7132034302
	        )
	    [changed] => Array
	        (
	            [0] => tel
	        )
	    [timestamp] => 1339136961334
	)

The <tt>payload</tt> contains the entire record, while the <tt>changed</tt>element highlights only those items that have been modified.

## Getting Diffs
```php
	//Getting started
	require_once('Factual.php');
	$factual = new Factual($key,$secret);

	//Getting Diffs
	$query = new DiffsQuery;
	$query->setStart(1339123455775); //starting time for updates window. Milliseconds timestamp.
	//$query->setEnd(1339136968687); //this is optional. Otherwise defaults to current time
	$res = $factual->fetch("2EH4Pz", $query);	//run query
```	
Having run the Diffs query, there are a few things you can do with the resultant object:

## Working with the Diffs Response Object
### Getting Summary Statistics	
```php
	//show summary stats
	print_r($res->getStats());
```
returns an overview of the update counts and window duration:
	
	Array
	(
	    [insert] => 78
	    [update] => 705
	    [delete] => 56
	    [deprecate] => 0
	    [total] => 839
	    [duration] => 09:35:19
   		[start] => Thu, 07 Jun 2012 19:44:15 -0700
    	[end] => Sun, 29 Jul 2012 01:20:13 -0700
	)

### Iterate Through the Diffs	
The result object is an ArrayIterator, so you can walk through the results:
```php
	foreach ($res as $diff){
		print_r($diff);
	}
```	
outputs the diffs as an array, one at a time.  You can grab all diffs as an array of diff arrays:
```php
	$allDifs = $res->getDiffs();
```
### Get Start and End Window Times
The result object provides additional methods to determine the specific start, end, and duration timestamps for the diffs window:
```php
	$end = $res->getEnd() //get close of window as timestamp
	$duration = $res->getDuration() //get duration of window as timestamp
	$start = $res->getStart() //get start of window as timestamp
```
Include <tt>true</tt> as the parameter to get a human readable version:
```php
	$end = $res->getEnd(true) //get close of window as human-readable RFC 2822
	$duration = $res->getDuration() //get duration of window as H:m:s
	$start = $res->getStart() //get start of window as tRFC 2822
```
Diffs API Documentation: http://developer.factual.com/api-docs/#Diffs

#Multi Queries
Our 'Multi' feature allows you to make up to three queries via a single http request.  Multi can combine queries of different types (for example, a geopulse and reverse geocoder request), but they all must be GET requests.  See the [Multi API documentation](http://developer.factual.com/display/docs/Core+API+-+Multi) for details.

## A Multi Example
Create your query objects as usual, and add them to the query queue using <tt>multiQueue()</tt>:
```php
	//create first query and add to queue
	$query1 = new FactualQuery;
	$query1->limit(3);
	$query1->only("factual_id,name");
	$factual->multiQueue("global", $query1, "globalQ"); //'globalQ' is the arbitrary handle for this query

	//create second query and add to queue
	$query2 = new FactualQuery;
	$query2->limit(3);
	$query2->only("factual_id,name");
	$factual->multiQueue("world-geographies", $query2, "worldGeo"); //'worldGeo' is the arbitrary handle for this query
```
Note that <tt>multiQueue()</tt> parameters are just like those of the <tt>fetch()</tt> method but include a required third parameter: an arbitrary string that you use to identify the results from each query. These must be unique to each query.

Use <tt>multiFetch()</tt> to send your request:
```php
	//make multi request
	$res = $factual->multiFetch();
```
and iterate through the response to obtain each response object:
```php
	//iterate through response objects
	foreach ($res as $queryResponse){
	    print_r($queryResponse->getData());
	} 
```
Multi API Documentation: http://developer.factual.com/api-docs/#Multi

#Working with Factual Data Files
Some Factual data partners work with our data drops rather than the API.  Factual data files are provided in compressed, tab-delimited format and the filename includes a UNIX timestamp in milliseconds, ex:

	us_places.developer_preview.1345052880000.tab.gz
	
Compression is gzip format -- it can be opened with most Windows, Linux, and Mac compression utilities.	
	
This timestamp should be used for your first [Diffs API](us_places.developer_preview.1345052880000.tab.gz) call.	
	
## File Size
Factual files contain millions of records.  If you're trying to use Excel, don't.  Do not load the entire file into memory, and don't try to view with an editor - use head, tail, sed, awk, grep, and other utilities. These are all available for Windows at http://gnuwin32.sourceforge.net/

## Loading to SQL or Other System
Loading data into a database is pretty straightforward using the <tt>fgetcsv</tt> command:
```php
	//get file pointer
	filename = "path/to/data/file";
	$fp = fopen($filename, "r");
	
	//iterate row-by-row
	while ($row = fgetcsv($fp,0, $delimiter)){
		//$row is now your first row of data
		// insert ROW into your db w/ SQL
	}
```
## Determining Field Width
Factual does not have formal specifications around field lengths -- there is always the possibility that field lengths will change between versions (but rarely by much).  However, we do [provide a CSV analysis script](https://github.com/Factual/places/tree/master/csv) that allows you to
calculate value lengths for any CSV.

If you are on Windows and don't want to run a PHP script, you can use a [third-party Windows utility](http://www.marcusnyberg.com/2011/08/11/analyzing-column-sizes-in-csv-files/) that does the same thing.

#Wordpress
Brief instructions on how to install the Factual PHP driver as Wordpress plugin:

1. Download the [zip archive](https://github.com/Factual/factual-php-driver/archive/master.zip) of the driver from Factual or **git clone** from https://github.com/Factual/factual-php-driver and then **zip archive** it yourself.
1. Go to the **plugins page** of the Wordpress Manager (/wordpress/wp-admin/plugins.php)
1. Click *Add New*
1. Click *Upload*
1. Select the .zip archive of the driver (note: the plug-in manager will ONLY accept .zip files)
1. Click *Activate Plugin*
1. Create a test page, something like this:

	<pre>
	&lt;html&gt;
	&lt;body&gt;
	&lt;?php
	/** load factual driver. Make sure the path below matches what you have! */
	require_once('./wp-content/plugins/factual-php-driver-master/Factual.php');
    $factual = new Factual("YOUR_API_KEY","YOUR_API_SECRET");
    $query = new FactualQuery;
    $res = $factual->fetch("places-us", $query);
    $data = $res->getData();
    ?&gt;
    &lt;h1&gt;Hello. I found a place at:
    &lt;?php&gt;
    /** print the address of the first place */
    print_r($data[0][address]);
    ?&gt;
    &lt;/h1&gt;
    &lt;/body&gt;
    &lt;/html&gt;
	</pre>

* Make sure you've put your own **key** and **secret** when you create the $factual object.
* Make sure the import path matches the path to your plugin.
* You'll need to pass a **zip** parameter to the URL with a US postcode.

If all goes well, you should get something like this:

![Hello World Screenshot](http://developer.factual.com/images/HelloWorld.png)!

