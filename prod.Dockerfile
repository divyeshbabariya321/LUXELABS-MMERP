FROM luxelabs/mmerp-base-php-8.2:latest
COPY . /var/www/
WORKDIR /var/www
RUN mkdir -p /etc/nginx/conf.d
RUN cp ./nginx/default.conf /etc/nginx/conf.d/default.conf
#RUN mkdir -p /etc/varnish
#RUN cp ./default.vcl /etc/varnish/default.vcl
#RUN mkdir -p /efs-mount-point

#RUN ln -s ../../efs-mount-point/erp/config/.env .env
#RUN ln -s ../../../efs-mount-point/erp/config/queues.txt public/queues.txt
RUN cp ./horizon.conf /etc/supervisor/conf.d/horizon.conf

RUN mv entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh
