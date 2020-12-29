# FROM node:12.20.0-alpine3.10

# WORKDIR /
# RUN npm install
# COPY . ./

# EXPOSE 3000

# CMD ["npm", "run", "dev"]

FROM mangoweb/mango-cli

WORKDIR /var/www/html
COPY . ./
RUN npm install

EXPOSE 3000

CMD ["dev"]
