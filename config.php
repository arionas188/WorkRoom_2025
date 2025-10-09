<?php
/**
 * Configuration File for Contact Form
 * Update these settings according to your needs
 */

return [
    // Email Settings
    'admin_email' => 'samalaigkonstantinos@gmail.com', // Αλλάξτε με το email σας
    'site_name' => 'WorkRoom W - Αρχιτεκτονικό Γραφείο',
    'site_url' => 'https://arionaskonstantinostest.xyz/', // Αλλάξτε με το domain σας
    
    // Rate Limiting
    'max_submissions_per_hour' => 5,  // Μέγιστο 5 μηνύματα ανά ώρα
    'max_submissions_per_day' => 20,  // Μέγιστο 20 μηνύματα ανά ημέρα
    
    // File Paths
    'log_file' => 'logs/contact_logs.txt',
    'rate_limit_file' => 'logs/rate_limit.json',
    
    // Security Settings
    'enable_csrf_protection' => true,
    'enable_honeypot' => true,
    'enable_spam_detection' => true,
    
    // GDPR Settings
    'data_retention_days' => 365, // Διατήρηση logs για 1 χρόνο
    'require_gdpr_consent' => true,
    'privacy_policy_url' => 'privacy.html',
    
    // Email Template Settings
    'email_template' => 'professional', // 'simple' ή 'professional'
    'include_technical_details' => true,
    'include_ip_address' => true,
    
    // Spam Detection
    'spam_keywords' => [
        'viagra', 'cialis', 'casino', 'poker', 'loan', 'debt',
        'free money', 'click here', 'buy now', 'limited time',
        'bitcoin', 'cryptocurrency', 'investment'
    ],
    'max_links_per_message' => 2,
    
    // Notification Settings
    'send_admin_notification' => true,
    'send_auto_reply' => false, // Προαιρετικό: αυτόματη απάντηση
    
    // Auto Reply Settings (αν enable_auto_reply = true)
    'auto_reply_subject' => 'Ευχαριστούμε για το μήνυμά σας - WorkRoom W',
    'auto_reply_message' => '
Αγαπητέ/ή {name},

Ευχαριστούμε που επικοινωνήσατε μαζί μας!

Το μήνυμά σας έχει ληφθεί και θα σας απαντήσουμε το συντομότερο δυνατό.

Με εκτίμηση,
Η ομάδα του WorkRoom W

---
Αυτό είναι ένα αυτόματο μήνυμα. Παρακαλώ μη απαντάτε σε αυτό το email.
    ',
    
    // Advanced Security
    'blocked_ips' => [], // IP addresses που θέλετε να μπλοκάρετε
    'allowed_countries' => [], // Κωδικοί χωρών που επιτρέπετε (π.χ. ['GR', 'CY'])
    'enable_geo_blocking' => false,
    
    // Logging
    'log_level' => 'info', // 'debug', 'info', 'warning', 'error'
    'log_rotation' => true, // Αυτόματη περιστροφή logs
    'log_max_size' => '10MB', // Μέγιστο μέγεθος log file
    
    // Performance
    'enable_caching' => false,
    'cache_duration' => 3600, // 1 ώρα σε δευτερόλεπτα
    
    // Backup Settings
    'enable_backup' => false,
    'backup_frequency' => 'daily', // 'daily', 'weekly', 'monthly'
    'backup_retention' => 30, // Ημέρες διατήρησης backup
];
?>
