FROM node:15-alpine
ENV APP_ROOT /web

WORKDIR ${APP_ROOT}
ADD ./nuxt ${APP_ROOT}

RUN npm ci
RUN npm i nuxt --global
RUN npm run build

CMD ["npm", "run", "start"]
