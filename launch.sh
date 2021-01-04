#!/bin/bash

cd wordpress-escape/src/www/localhost/
sudo docker-compose up -d
cd ..
cd nginx-proxy/
sudo docker-compose up -d
