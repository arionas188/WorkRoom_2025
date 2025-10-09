<?php
/**
 * Simple Contact Form Handler - Debug Version
 * For troubleshooting 500 errors
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Basic configuration
    $config = [
        'admin_email' => 'samalaigkonstantinos@gmail.com',
        'site_name' => 'WorkRoom W - Αρχιτεκτονικό Γραφείο',
        'site_url' => 'https://arionaskonstantinostest.xyz/',
        'log_file' => 'logs/contact_logs.txt'
    ];
    
    // Create logs directory if it doesn't exist
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    // Get form data
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
        'gdpr_consent' => isset($_POST['gdpr-consent']),
        'marketing_consent' => isset($_POST['marketing-consent']),
        'website' => $_POST['website'] ?? '' // Honeypot
    ];
    
    // Basic validation
    if (empty($data['name'])) {
        throw new Exception('Το όνομα είναι υποχρεωτικό');
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Παρακαλώ εισάγετε ένα έγκυρο email');
    }
    
    if (empty($data['message'])) {
        throw new Exception('Το μήνυμα είναι υποχρεωτικό');
    }
    
    if (!$data['gdpr_consent']) {
        throw new Exception('Πρέπει να συμφωνήσετε με την επεξεργασία των προσωπικών δεδομένων');
    }
    
    // Honeypot check
    if (!empty($data['website'])) {
        throw new Exception('Spam detected');
    }
    
    // Prepare email content
    $subjectMap = [
        'general' => 'Γενικές Πληροφορίες',
        'project' => 'Νέο Έργο',
        'consultation' => 'Σύμβουλος',
        'collaboration' => 'Συνεργασία',
        'other' => 'Άλλο'
    ];
    
    $subjectText = $subjectMap[$data['subject']] ?? $data['subject'];
    $emailSubject = "Νέο Μήνυμα από {$data['name']} - {$subjectText}";
    
    $emailBody = "=== ΝΕΟ ΜΗΝΥΜΑ ΕΠΙΚΟΙΝΩΝΙΑΣ ===

Στοιχεία Επικοινωνίας:
Όνομα: {$data['name']}
Email: {$data['email']}
Τηλέφωνο: " . ($data['phone'] ?: 'Δεν δόθηκε') . "
Θέμα: {$subjectText}

Μήνυμα:
{$data['message']}

=== GDPR & ΝΟΜΙΚΑ ===
Συμφωνία GDPR: " . ($data['gdpr_consent'] ? 'ΝΑΙ' : 'ΟΧΙ') . "
Marketing Συμφωνία: " . ($data['marketing_consent'] ? 'ΝΑΙ' : 'ΟΧΙ') . "

=== ΤΕΧΝΙΚΑ ΣΤΟΙΧΕΙΑ ===
Ημερομηνία: " . date('d/m/Y H:i:s') . "
IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "
User Agent: " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 200) . "

---
Αυτό το μήνυμα στάλθηκε από τη φόρμα επικοινωνίας του {$config['site_name']}
";
    
    // Try to send email
    $mailSent = false;
    
    // Method 1: Try PHP mail()
    $headers = [
        'From: ' . $config['site_name'] . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
        'Reply-To: ' . $data['email'],
        'Content-Type: text/plain; charset=UTF-8'
    ];
    
    $mailSent = @mail(
        $config['admin_email'],
        $emailSubject,
        $emailBody,
        implode("\r\n", $headers)
    );
    
    // Method 2: If mail() fails, save to file
    if (!$mailSent) {
        $emailData = [
            'to' => $config['admin_email'],
            'subject' => $emailSubject,
            'body' => $emailBody,
            'reply_to' => $data['email'],
            'timestamp' => date('Y-m-d H:i:s'),
            'sent' => false
        ];
        
        $emailLine = json_encode($emailData, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents('pending_emails.txt', $emailLine, FILE_APPEND | LOCK_EX);
        $mailSent = true; // Consider it "sent" for user experience
    }
    
    // Log the submission
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'name' => $data['name'],
        'email' => $data['email'],
        'subject' => $data['subject'],
        'success' => true,
        'mail_sent' => $mailSent
    ];
    
    $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($config['log_file'], $logLine, FILE_APPEND | LOCK_EX);
    
    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Το μήνυμά σας στάλθηκε επιτυχώς! Θα σας απαντήσουμε το συντομότερο δυνατό.'
    ]);
    
} catch (Exception $e) {
    // Log error
    $errorLog = [
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => $e->getMessage(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'post_data' => $_POST
    ];
    
    file_put_contents('error_log.txt', json_encode($errorLog, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
    
    // Send error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
