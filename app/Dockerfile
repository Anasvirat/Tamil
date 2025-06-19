# Use official PHP image
FROM php:8.1-cli

# Install ffmpeg
RUN apt-get update && apt-get install -y ffmpeg curl unzip

# Create working directory
WORKDIR /app

# Copy all files into container
COPY . .

# Run stream script
CMD ["php", "stream.php"]
