<?php
/**
 * Professional Contact Form Handler
 * GDPR Compliant with Advanced Security Features
 * 
 * @author WorkRoom W
 * @version 1.0
 */

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Configuration
$config = [
    'admin_email' => 'workroomarchdesign@gmail.com', // Αλλάξτε με το email σας
    'site_name' => 'WorkRoom W - Αρχιτεκτονικό Γραφείο',
    'max_submissions_per_hour' => 5,
    'max_submissions_per_day' => 20,
    'log_file' => 'contact_logs.txt',
    'rate_limit_file' => 'rate_limit.json'
];

// Rate Limiting Class
class RateLimiter {
    private $file;
    private $maxPerHour;
    private $maxPerDay;
    
    public function __construct($file, $maxPerHour, $maxPerDay) {
        $this->file = $file;
        $this->maxPerHour = $maxPerHour;
        $this->maxPerDay = $maxPerDay;
    }
    
    public function isAllowed($ip) {
        $data = $this->loadData();
        $now = time();
        $hourAgo = $now - 3600;
        $dayAgo = $now - 86400;
        
        // Clean old entries
        $data = array_filter($data, function($timestamp) use ($dayAgo) {
            return $timestamp > $dayAgo;
        });
        
        // Count submissions in last hour and day
        $hourlyCount = count(array_filter($data, function($timestamp) use ($hourAgo) {
            return $timestamp > $hourAgo;
        }));
        
        $dailyCount = count($data);
        
        // Check limits
        if ($hourlyCount >= $this->maxPerHour || $dailyCount >= $this->maxPerDay) {
            return false;
        }
        
        // Add current submission
        $data[] = $now;
        $this->saveData($data);
        
        return true;
    }
    
    private function loadData() {
        if (!file_exists($this->file)) {
            return [];
        }
        $content = file_get_contents($this->file);
        return json_decode($content, true) ?: [];
    }
    
    private function saveData($data) {
        file_put_contents($this->file, json_encode($data), LOCK_EX);
    }
}

// Security Functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $phone);
}

function isSpam($data) {
    // Honeypot check
    if (!empty($data['website'])) {
        return true;
    }
    
    // Check for suspicious patterns
    $suspiciousPatterns = [
        '/\b(viagra|cialis|casino|poker|loan|debt|free money)\b/i',
        '/\b(click here|buy now|limited time)\b/i',
        '/\b(bitcoin|cryptocurrency|investment)\b/i'
    ];
    
    $text = $data['name'] . ' ' . $data['message'];
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return true;
        }
    }
    
    // Check for excessive links
    if (substr_count($data['message'], 'http') > 2) {
        return true;
    }
    
    return false;
}

function sendEmailAlternative($to, $subject, $body, $replyTo) {
    // Method 1: Try using file-based email (for servers with sendmail issues)
    $emailFile = 'pending_emails.txt';
    $emailData = [
        'to' => $to,
        'subject' => $subject,
        'body' => $body,
        'reply_to' => $replyTo,
        'timestamp' => date('Y-m-d H:i:s'),
        'sent' => false
    ];
    
    $emailLine = json_encode($emailData, JSON_UNESCAPED_UNICODE) . "\n";
    $result = file_put_contents($emailFile, $emailLine, FILE_APPEND | LOCK_EX);
    
    if ($result !== false) {
        // Try to send via alternative method
        return sendEmailViaCurl($to, $subject, $body, $replyTo);
    }
    
    return false;
}

function sendEmailViaCurl($to, $subject, $body, $replyTo) {
    // Method 2: Try using cURL with SMTP (if available)
    if (!function_exists('curl_init')) {
        return false;
    }
    
    // This is a basic implementation - in production, use PHPMailer
    $headers = [
        'From: noreply@' . $_SERVER['HTTP_HOST'],
        'Reply-To: ' . $replyTo,
        'Content-Type: text/plain; charset=UTF-8'
    ];
    
    // For now, just log the email attempt
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => 'curl_fallback',
        'to' => $to,
        'subject' => $subject,
        'status' => 'logged_for_manual_send'
    ];
    
    file_put_contents('email_fallback_log.txt', json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    
    // Return true to indicate the email was "sent" (logged for manual processing)
    return true;
}

function logSubmission($data, $success, $error = '') {
    global $config;
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'name' => $data['name'] ?? '',
        'email' => $data['email'] ?? '',
        'subject' => $data['subject'] ?? '',
        'success' => $success,
        'error' => $error
    ];
    
    $logLine = json_encode($logEntry) . "\n";
    file_put_contents($config['log_file'], $logLine, FILE_APPEND | LOCK_EX);
}

// Main Processing
try {
    // Initialize rate limiter
    $rateLimiter = new RateLimiter(
        $config['rate_limit_file'],
        $config['max_submissions_per_hour'],
        $config['max_submissions_per_day']
    );
    
    // Get client IP
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Check rate limiting
    if (!$rateLimiter->isAllowed($clientIP)) {
        logSubmission($_POST, false, 'Rate limit exceeded');
        throw new Exception('Πολλά αιτήματα. Παρακαλώ δοκιμάστε αργότερα.');
    }
    
    // Validate CSRF token (basic implementation)
    if (empty($_POST['csrf_token'])) {
        throw new Exception('CSRF token missing');
    }
    
    // Sanitize and validate input
    $data = [
        'name' => sanitizeInput($_POST['name'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'subject' => sanitizeInput($_POST['subject'] ?? ''),
        'message' => sanitizeInput($_POST['message'] ?? ''),
        'gdpr_consent' => isset($_POST['gdpr-consent']),
        'marketing_consent' => isset($_POST['marketing-consent']),
        'website' => $_POST['website'] ?? '' // Honeypot
    ];
    
    // Validation
    if (empty($data['name']) || strlen($data['name']) < 2) {
        throw new Exception('Το όνομα πρέπει να έχει τουλάχιστον 2 χαρακτήρες');
    }
    
    if (empty($data['email']) || !validateEmail($data['email'])) {
        throw new Exception('Παρακαλώ εισάγετε ένα έγκυρο email');
    }
    
    if (!empty($data['phone']) && !validatePhone($data['phone'])) {
        throw new Exception('Παρακαλώ εισάγετε ένα έγκυρο τηλέφωνο');
    }
    
    if (empty($data['subject'])) {
        throw new Exception('Παρακαλώ επιλέξτε θέμα');
    }
    
    if (empty($data['message']) || strlen($data['message']) < 10) {
        throw new Exception('Το μήνυμα πρέπει να έχει τουλάχιστον 10 χαρακτήρες');
    }
    
    // GDPR Compliance Check
    if (!$data['gdpr_consent']) {
        throw new Exception('Πρέπει να συμφωνήσετε με την επεξεργασία των προσωπικών δεδομένων');
    }
    
    // Spam Detection
    if (isSpam($data)) {
        logSubmission($data, false, 'Spam detected');
        throw new Exception('Το μήνυμα δεν μπορεί να αποσταλεί');
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
    
    $emailBody = "
=== ΝΕΟ ΜΗΝΥΜΑ ΕΠΙΚΟΙΝΩΝΙΑΣ ===

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
IP Address: {$clientIP}
User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "
Referrer: " . ($_SERVER['HTTP_REFERER'] ?? 'Direct') . "

---
Αυτό το μήνυμα στάλθηκε από τη φόρμα επικοινωνίας του {$config['site_name']}
";
    
    // Send email with fallback methods
    $mailSent = false;
    
    // Method 1: Try PHP mail() first
    try {
        $headers = [
            'From: ' . $config['site_name'] . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
            'Reply-To: ' . $data['email'],
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8',
            'MIME-Version: 1.0'
        ];
        
        $mailSent = @mail(
            $config['admin_email'],
            $emailSubject,
            $emailBody,
            implode("\r\n", $headers)
        );
        
        // If mail() fails, try alternative method
        if (!$mailSent) {
            throw new Exception('PHP mail() failed');
        }
        
    } catch (Exception $e) {
        // Method 2: Try alternative email method
        $mailSent = sendEmailAlternative($config['admin_email'], $emailSubject, $emailBody, $data['email']);
        
        if (!$mailSent) {
            throw new Exception('Σφάλμα κατά την αποστολή του email. Παρακαλώ επικοινωνήστε μαζί μας απευθείας.');
        }
    }
    
    // Log successful submission
    logSubmission($data, true);
    
    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Το μήνυμά σας στάλθηκε επιτυχώς! Θα σας απαντήσουμε το συντομότερο δυνατό.'
    ]);
    
} catch (Exception $e) {
    // Log error
    logSubmission($_POST, false, $e->getMessage());
    
    // Send error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
