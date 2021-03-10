FROM node:15-alpine
# Configure
ENV APP_ROOT /var/www/client
ENV HOST 0.0.0.0

WORKDIR ${APP_ROOT}
ADD ./client ${APP_ROOT}

RUN npm ci
RUN npm i nuxt --global
RUN npm run build

EXPOSE 3000

#CMD ["npm", "run", "dev"]
