#!/bin/bash

# Stop apache
httpd -k stop

# Make sure MySQL is up
if [ ! $(/etc/init.d/mysqld status | grep running) ]
then
   $(/etc/init.d/mysqld start)
fi

# Compile JS scripts
cd js/commonjs
make; make quick
cd ../..

# Start apache
httpd -k restart
