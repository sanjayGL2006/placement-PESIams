FROM python:3.9-slim

# Install system dependencies for psycopg2 and other binary packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

ENV FLASK_ENV=production
ENV DATABASE_URL="dbname=placement_pro user=postgres password=postgres host=db"
ENV SECRET_KEY="a-long-random-string"
ENV JWT_SECRET="another-long-random-string"
ENV CORS_ORIGINS="http://localhost:8000,http://127.0.0.1:8000"

CMD ["python", "app.py"]
