const fs = require('fs');
const path = require('path');

// Create uploads directory if it doesn't exist
const uploadsDir = path.join(__dirname, 'public', 'images', 'uploads');
if (!fs.existsSync(uploadsDir)) {
    fs.mkdirSync(uploadsDir, { recursive: true });
    console.log('Created uploads directory:', uploadsDir);
}

// Create default product image HTML
const defaultImagePath = path.join(__dirname, 'public', 'images', 'default-product.html');
const defaultImageContent = `
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="#f8f9fa"/>
    <text x="50%" y="50%" font-family="Arial" font-size="14" fill="#6c757d" text-anchor="middle">
        No Image
    </text>
</svg>
`;

fs.writeFileSync(defaultImagePath, defaultImageContent);
console.log('Created default product image placeholder:', defaultImagePath);

// Set directory permissions
try {
    fs.chmodSync(uploadsDir, 0o777);
    console.log('Set permissions for uploads directory');
} catch (error) {
    console.error('Error setting permissions:', error);
}