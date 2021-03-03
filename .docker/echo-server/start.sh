#!/bin/bash

#/usr/bin/del /app/laravel-echo-server.lock
dockerize -no-overwrite -template /etc/laravel-echo-server.tmpl:/app/laravel-echo-server.json /usr/local/bin/laravel-echo-server start
