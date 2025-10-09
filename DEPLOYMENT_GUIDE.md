# 🚀 Οδηγίες Deployment - WorkRoom W Website

## 📋 Προαπαιτούμενα

### 1. **Web Hosting Requirements**
- ✅ **PHP 7.4+** (συνιστώμενη PHP 8.1+)
- ✅ **Apache/Nginx** web server
- ✅ **SSL Certificate** (HTTPS)
- ✅ **Email functionality** (mail() function ή SMTP)
- ✅ **File permissions** (755 για φακέλους, 644 για αρχεία)

### 2. **Domain & DNS**
- ✅ **Domain name** (π.χ. workroom.gr)
- ✅ **DNS settings** ρυθμισμένα
- ✅ **SSL certificate** ενεργοποιημένο

## 🔧 Βήμα 1: Ρύθμιση Hosting

### **Αν χρησιμοποιείτε cPanel:**
1. **Πηγαίνετε στο File Manager**
2. **Δημιουργήστε φάκελο** για το site (π.χ. `public_html`)
3. **Ανεβάστε όλα τα αρχεία** εκτός από τα logs

### **Αν χρησιμοποιείτε VPS/Dedicated:**
```bash
# Δημιουργία φακέλου
mkdir -p /var/www/workroom
cd /var/www/workroom

# Αντιγραφή αρχείων
scp -r user@local:/path/to/project/* ./
```

## ⚙️ Βήμα 2: Ρύθμιση Αρχείων

### **1. Ενημέρωση config.php**
```php
// Ανοίξτε το config.php και αλλάξτε:
'admin_email' => 'your-email@workroom.gr', // Το email σας
'site_url' => 'https://workroom.gr', // Το domain σας
```

### **2. Δημιουργία φακέλου logs**
```bash
mkdir logs
chmod 755 logs
[loipon fitaxneis ena arxeio logs sto fakelo pou exeis to project soy kai meta to push sto git kai apo ekei sto server pou exeis to site]
``` 

### **3. Ρύθμιση permissions**
```bash
# Στο terminal του server:
chmod 755 logs/
chmod 644 *.php
chmod 644 *.html
chmod 644 .htaccess
```

## 📧 Βήμα 3: Ρύθμιση Email

### **Επιλογή Α: PHP mail() (Απλή)**
```php
// Στο process-contact.php, η function mail() θα λειτουργήσει αυτόματα
// Ελέγξτε ότι το hosting σας υποστηρίζει PHP mail()
```

### **Επιλογή Β: SMTP (Συνιστώμενη)**
```php
// Προσθέστε στο process-contact.php:
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com'; // ή το SMTP server σας
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
```

## 🔒 Βήμα 4: Ασφάλεια

### **1. Ενημέρωση .htaccess**
```apache
# Αλλάξτε το yourdomain.com με το domain σας:
RewriteCond %{HTTP_REFERER} !^https?://(www\.)?workroom\.gr [NC]
```

### **2. SSL Certificate**
- **Ενεργοποιήστε HTTPS** στο hosting control panel
- **Force HTTPS** redirect
- **HSTS headers** (ήδη ρυθμισμένα στο .htaccess)

### **3. Firewall Rules**
```bash
# Block suspicious IPs (προαιρετικό)
iptables -A INPUT -s 1.2.3.4 -j DROP
```

## 🧪 Βήμα 5: Testing

### **1. Test τη φόρμα:**
1. **Πηγαίνετε στο** `https://workroom.gr/news.html`
2. **Συμπληρώστε τη φόρμα** με test δεδομένα
3. **Στείλτε το μήνυμα**
4. **Ελέγξτε το email** σας

### **2. Test τα logs:**
```bash
# Ελέγξτε τα logs:
tail -f logs/contact_logs.txt
```

### **3. Test την ασφάλεια:**
- **Δοκιμάστε spam** (θα πρέπει να απορριφθεί)
- **Δοκιμάστε rate limiting** (πολλά μηνύματα γρήγορα)
- **Ελέγξτε τα security headers**

## 📊 Βήμα 6: Monitoring

### **1. Log Monitoring**
```bash
# Ελέγξτε τα logs καθημερινά:
grep "ERROR" logs/contact_logs.txt
grep "spam" logs/contact_logs.txt
```

### **2. Email Monitoring**
- **Ελέγξτε το spam folder** του email σας
- **Ρυθμίστε email alerts** για errors

### **3. Performance Monitoring**
```bash
# Ελέγξτε το μέγεθος των logs:
du -sh logs/
```

## 🔄 Βήμα 7: Maintenance

### **Εβδομαδιαία:**
- ✅ **Ελέγξτε τα logs** για errors
- ✅ **Ελέγξτε το email** για spam attempts
- ✅ **Ενημερώστε τα backups**

### **Μηνιαία:**
- ✅ **Καθαρίστε τα παλιά logs**
- ✅ **Ελέγξτε τα security updates**
- ✅ **Test τη φόρμα** ξανά

### **Ετήσια:**
- ✅ **Ενημερώστε το SSL certificate**
- ✅ **Ελέγξτε τα GDPR compliance**
- ✅ **Backup όλων των δεδομένων**

## 🚨 Troubleshooting

### **Φόρμα δεν στέλνει email:**
```bash
# Ελέγξτε τα PHP logs:
tail -f /var/log/apache2/error.log

# Ελέγξτε τα permissions:
ls -la process-contact.php
```

### **403 Forbidden Error:**
```bash
# Ελέγξτε το .htaccess:
apache2ctl configtest
```

### **500 Internal Server Error:**
```bash
# Ελέγξτε τα PHP errors:
php -l process-contact.php
```

### **Email δεν φτάνει:**
1. **Ελέγξτε το spam folder**
2. **Ελέγξτε τα SMTP settings**
3. **Test με διαφορετικό email**

## 📞 Support

### **Αν χρειάζεστε βοήθεια:**
1. **Ελέγξτε τα logs** πρώτα
2. **Test με απλά δεδομένα**
3. **Ελέγξτε τα permissions**
4. **Επικοινωνήστε με το hosting support**

## ✅ Checklist Deployment

- [ ] **Hosting ρυθμισμένο** με PHP 7.4+
- [ ] **Domain** δουλεύει με HTTPS
- [ ] **config.php** ενημερωμένο
- [ ] **logs/** φάκελος δημιουργημένος
- [ ] **Permissions** ρυθμισμένα (755/644)
- [ ] **Email** δουλεύει
- [ ] **Φόρμα** test και λειτουργεί
- [ ] **Logs** δημιουργούνται
- [ ] **Security headers** ενεργοποιημένα
- [ ] **Backup** ρυθμισμένο

## 🎯 Production Tips

### **Performance:**
- **Enable Gzip** compression (ήδη στο .htaccess)
- **Set proper cache headers** (ήδη ρυθμισμένα)
- **Use CDN** για static files (προαιρετικό)

### **Security:**
- **Regular security updates**
- **Monitor logs** καθημερινά
- **Backup** τακτικά
- **Test** τη φόρμα περιοδικά

### **GDPR Compliance:**
- **Log retention** 1 χρόνος (ήδη ρυθμισμένο)
- **Data deletion** όταν ζητηθεί
- **Privacy policy** ενημερωμένη
- **Consent** καταγράφεται

---

**🎉 Συγχαρητήρια! Το site σας είναι έτοιμο για production!**
