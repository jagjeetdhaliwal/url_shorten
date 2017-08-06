url_shorten
===========

Designing a url shortening service like bit.ly

## Use Cases
The application can be divided into two parts
1. Shortening - take a url => return a much shorter url.
2. Redirection - take a short url => redirect to the corresponding destination url.

We are assuming that our application is dominated by the read operations (90% reads, 10% writes).

##Design

1. Application Service layer (serves the requests)
    For both Shortening and Redirection

2. Data Storage layer (We are making using Doctrine and Redis Cache Components in Symfony to handle this)
    Using MySQL as our primary RDBMS where we store the mapping of all short urls against the destination urls.
    Using Redis (via the Redis Cache Component in Symfony) as a caching layer on top of MySQL to enable much faster reads
    due to storage being in memory.
    
    So basically, for all write operations we write first to MySQL and then cache it on Redis for later usage.
    For all redirections, we first go and check if the url mapping exists in Redis. If not, we go and check the
    database and redirect depending on if we found the url mapping or not.
    
 3. For url generation, for now we are randomly constructing a string of length 6 out of a bucket of acceptable
    characters.
    
    We can also use md5 + a random salt, convert it to base 62 and extract 6 characters for the very same purpose
    as well.
    
 4. Used Materialize to come up with a quick interface.
    

## TO DO

 1. As we scale, traffic will be quite easy to handle, it will be data that is more interesting in this problem. We 
  might have to shard the database or handle redis storage in a more efficient way at a larger scale.
 
 2. Use redis for further analytics/tracking. For example - Things like how many urls are being shortened from a 
 particular ip address are very easy to implement now.
 
 3. Manage sessions better and store them to redis/database.

 4. Add a list of stop words for unacceptable words in urls :-)

