FROM nginx:1.19.7

RUN apt-get update && apt-get install && apt-get -y install mc curl vim

ADD .docker/nginx/vhost.conf /etc/nginx/conf.d/default.conf

# Clean up
RUN apt-get clean
RUN apt-get -y autoremove
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 80
