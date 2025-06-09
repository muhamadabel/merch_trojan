<?php
// Script untuk membuat folder upload jika belum ada
$upload_dirs = [
    '../assets/uploads/posts/',
    '../assets/uploads/products/',
    '../assets/uploads/profiles/'
];

foreach ($upload_dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "Created directory: $dir\n";
        } else {
            echo "Failed to create directory: $dir\n";
        }
    } else {
        echo "Directory already exists: $dir\n";
    }
}

// Buat file .htaccess untuk keamanan
$htaccess_content = "# Prevent direct access to PHP files\n<Files \"*.php\">\nOrder Deny,Allow\nDeny from all\n</Files>";

foreach ($upload_dirs as $dir) {
    $htaccess_file = $dir . '.htaccess';
    if (!file_exists($htaccess_file)) {
        file_put_contents($htaccess_file, $htaccess_content);
        echo "Created .htaccess in: $dir\n";
    }
}

echo "Upload directories setup complete!\n";
?>
