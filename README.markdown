# phpRedis — PHP extension for communicating with Redis database

This extension provides an API for communicating with Redis database, a persistent 
key-value database with built-in net interface written in ANSI-C for Posix systems.

Forked from [Alfonso Jimenez](http://www.alfonsojimenez.com/)'s implementation, changes method names in accordance with Redis API Reference.

## Installation

phpize
./configure
make && make install

Then enable extension=redis.so in your php.ini.

If you are using MacPorts, instead of editing the master php.ini you can copy the bundled redis.ini into /opt/local/var/db/php5/ and PHP will pick it up.

## See also

 * [Redis product page](http://code.google.com/p/redis/)

 * [Redis Command Reference](http://code.google.com/p/redis/wiki/CommandReference)

