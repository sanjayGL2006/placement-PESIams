FROM php:8.2-alpine

WORKDIR /var/www/html

COPY . .

EXPOSE 8000

ENV PLACEMENT_API_BASE="http://localhost:5000/api"

CMD ["php", "-S", "0.0.0.0:8000"]
