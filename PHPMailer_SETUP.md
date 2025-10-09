# 📧 PHPMailer Setup - Εναλλακτική Λύση για Email

## 🚨 **Το Πρόβλημα με PHP 8+**

Σε αρκετά cPanel setups με **PHP 8+**, η `mail()` function πετάει **500 errors** επειδή:
- Δεν υπάρχει σωστά ρυθμισμένο `sendmail_path`
- Ο Exim έχει quotas/limits
- Security restrictions

## ✅ **Η Λύση - PHPMailer**

Το **PHPMailer** είναι η καλύτερη λύση για αξιόπιστη αποστολή emails.

## 🔧 **Εγκατάσταση PHPMailer**

### **Βήμα 1: Download PHPMailer**
```bash
# Download από GitHub
wget https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip
unzip master.zip
```

### **Βήμα 2: Upload στον Server**
1. **Ανεβάστε** τον φάκελο `PHPMailer-master` στον server
2. **Μετονομάστε** τον σε `PHPMailer`
3. **Βεβαιωθείτε** ότι είναι στον ίδιο φάκελο με το `process-contact.php`

### **Βήμα 3: Ενημέρωση process-contact.php**

Αντικαταστήστε το email section με:

```php
// Add PHPMailer
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Send email with PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // ή το SMTP server σας
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your-email@gmail.com'; // Το email σας
    $mail->Password   = 'your-app-password'; // App password (όχι το κανονικό password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('noreply@' . $_SERVER['HTTP_HOST'], $config['site_name']);
    $mail->addAddress($config['admin_email']);
    $mail->addReplyTo($data['email'], $data['name']);

    // Content
    $mail->isHTML(false);
    $mail->Subject = $emailSubject;
    $mail->Body    = $emailBody;

    $mail->send();
    $mailSent = true;
    
} catch (Exception $e) {
    // Fallback to alternative method
    $mailSent = sendEmailAlternative($config['admin_email'], $emailSubject, $emailBody, $data['email']);
}
```

## 🔑 **Gmail SMTP Setup**

### **1. Enable 2-Factor Authentication**
- Πηγαίνετε στο Google Account Settings
- Ενεργοποιήστε το 2FA

### **2. Generate App Password**
- Πηγαίνετε στο [Google App Passwords](https://myaccount.google.com/apppasswords)
- Δημιουργήστε App Password για "Mail"
- Χρησιμοποιήστε αυτό το password (όχι το κανονικό)

### **3. Gmail Settings**
```php
$mail->Host       = 'smtp.gmail.com';
$mail->Username   = 'your-email@gmail.com';
$mail->Password   = 'your-16-char-app-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;
```

## 🏢 **Άλλα SMTP Providers**

### **Outlook/Hotmail**
```php
$mail->Host       = 'smtp-mail.outlook.com';
$mail->Port       = 587;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
```

### **Yahoo**
```php
$mail->Host       = 'smtp.mail.yahoo.com';
$mail->Port       = 587;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
```

### **Custom SMTP (Hosting Provider)**
```php
$mail->Host       = 'mail.yourdomain.com';
$mail->Port       = 587; // ή 465 για SSL
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Username   = 'noreply@yourdomain.com';
$mail->Password   = 'your-email-password';
```

## 🎯 **Τρέχουσα Λύση (Χωρίς PHPMailer)**

Αν δεν θέλετε να εγκαταστήσετε PHPMailer, το τρέχον σύστημα:

1. **Προσπαθεί** να στείλει με `mail()`
2. **Αν αποτύχει**, αποθηκεύει το email σε `pending_emails.txt`
3. **Επιστρέφει success** στον χρήστη
4. **Εσείς** μπορείτε να διαβάσετε τα emails από το αρχείο

## 📁 **Αρχεία που Δημιουργούνται**

### **pending_emails.txt**
```json
{"to":"samalaigkonstantinos@gmail.com","subject":"Νέο Μήνυμα","body":"...","timestamp":"2025-01-15 10:30:00"}
```

### **email_fallback_log.txt**
```json
{"timestamp":"2025-01-15 10:30:00","method":"curl_fallback","status":"logged_for_manual_send"}
```

## 🔍 **Πώς να Ελέγξετε**

### **1. Test τη Φόρμα**
- Συμπληρώστε τη φόρμα
- Ελέγξτε αν εμφανίζεται success message

### **2. Ελέγξτε τα Logs**
```bash
# Ελέγξτε τα pending emails
cat pending_emails.txt

# Ελέγξτε τα fallback logs
cat email_fallback_log.txt
```

### **3. Ελέγξτε τα Contact Logs**
```bash
# Ελέγξτε τα contact logs
cat logs/contact_logs.txt
```

## ⚠️ **Σημαντικά**

### **Αν χρησιμοποιείτε PHPMailer:**
- **Μην κάνετε commit** τα credentials στο Git
- **Χρησιμοποιήστε environment variables**
- **Test** πρώτα με test email

### **Αν χρησιμοποιείτε την τρέχουσα λύση:**
- **Ελέγξτε τα pending_emails.txt** καθημερινά
- **Διαβάστε τα emails** και απαντήστε
- **Διαγράψτε** τα αρχεία μετά την επεξεργασία

## 🎯 **Συνιστώμενη Προσέγγιση**

1. **Δοκιμάστε** πρώτα την τρέχουσα λύση
2. **Αν δεν δουλεύει**, εγκαταστήστε PHPMailer
3. **Ρυθμίστε SMTP** με Gmail ή hosting provider
4. **Test** εκτενώς πριν το production

---

**💡 Tip:** Η τρέχουσα λύση θα λειτουργήσει για τις περισσότερες περιπτώσεις, αλλά το PHPMailer είναι πιο αξιόπιστο για production.
