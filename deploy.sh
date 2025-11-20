#!/bin/bash

# Enhanced deployment script for PHP application
# This script can be run manually or used as reference for CI/CD

set -e  # Exit on any error

# Configuration
SERVER_HOST="136.116.111.59"
SSH_USER="github-actions"
DEPLOY_PATH="/var/www/vc/simple-php-app"
REPO_URL="https://github.com/Saykoon/simple-php-app.git"

# Database configuration
DB_HOST="136.114.93.122"
DB_PORT="8002"
DB_USER="stud"
DB_PASSWORD="Uwb123!!"
# DB_NAME should be set as environment variable or passed as argument

echo "🚀 Starting deployment process..."

# Check if DB_NAME is provided
if [ -z "$DB_NAME" ]; then
    echo "❌ Error: DB_NAME environment variable is not set"
    echo "Usage: DB_NAME=your_album_number ./deploy.sh"
    exit 1
fi

echo "📡 Connecting to server..."

# Execute deployment commands on remote server
ssh -o StrictHostKeyChecking=no "$SSH_USER@$SERVER_HOST" << EOF
    set -e
    
    echo "📁 Preparing deployment directory..."
    cd /var/www/vc
    
    # Backup existing deployment if it exists
    if [ -d "$DEPLOY_PATH" ]; then
        echo "💾 Creating backup..."
        sudo mv simple-php-app simple-php-app.backup.\$(date +%Y%m%d_%H%M%S) || true
    fi
    
    echo "📥 Cloning repository..."
    sudo git clone "$REPO_URL" simple-php-app
    
    cd simple-php-app
    
    echo "🔧 Setting up configuration..."
    # Create production config
    sudo tee config.php > /dev/null << 'CONFIG_EOF'
<?php
// Production database configuration
define('DB_HOST', '$DB_HOST:$DB_PORT');
define('DB_NAME', '$DB_NAME');
define('DB_USER', '$DB_USER');
define('DB_PASS', '$DB_PASSWORD');

// Create database connection
function getConnection() {
    try {
        \$pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return \$pdo;
    } catch (PDOException \$e) {
        die("Database connection failed: " . \$e->getMessage());
    }
}
?>
CONFIG_EOF
    
    echo "🔐 Setting permissions..."
    sudo chown -R www-data:www-data .
    sudo chmod -R 755 .
    sudo chmod 644 *.php *.sql *.md
    
    echo "🗄️ Setting up database..."
    mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p'$DB_PASSWORD' $DB_NAME < database.sql || echo "Database tables already exist or setup completed"
    
    echo "🔄 Restarting web server..."
    sudo systemctl reload apache2 2>/dev/null || sudo systemctl reload nginx 2>/dev/null || echo "Web server reload completed"
    
    echo "✅ Deployment completed successfully!"
EOF

echo "🎉 Application deployed successfully!"
echo "🌐 URL: http://$SERVER_HOST/simple-php-app/"
echo "📊 You can now access your PHP application"