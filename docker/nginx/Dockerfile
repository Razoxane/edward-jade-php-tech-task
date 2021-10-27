FROM nginx:1.15.8-alpine

RUN apk add --no-cache bash

RUN rm -rf /var/cache/apk/*

WORKDIR /app

COPY public/index.php /app/public/index.php

COPY docker/nginx/app.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

CMD ["nginx", "-g", "daemon off;"]
