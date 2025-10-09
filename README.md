# 🏗️ WorkRoom W - Αρχιτεκτονικό Γραφείο

## 📋 Περιγραφή Project

Επαγγελματικό website για αρχιτεκτονικό γραφείο με επαγγελματικό σύστημα επικοινωνίας, πλήρη ασφάλεια και GDPR compliance.

## ✨ Χαρακτηριστικά

### 🔒 **Ασφάλεια**
- **CSRF Protection** με tokens
- **Honeypot fields** για spam protection
- **Rate limiting** (5/ώρα, 20/ημέρα)
- **Input validation** και sanitization
- **Security headers** (XSS, CSRF, Clickjacking)
- **IP blocking** και suspicious user agent detection

### 📧 **Email System**
- **Professional email templates**
- **SMTP support** για αξιόπιστη αποστολή
- **Auto-reply** functionality (προαιρετικό)
- **Email logging** και monitoring
- **Spam detection** με keywords

### ⚖️ **GDPR Compliance**
- **Explicit consent** για επεξεργασία δεδομένων
- **Optional marketing consent**
- **Data retention** policies (1 χρόνος)
- **Right to deletion** implementation
- **Privacy policy** integration
- **Audit trail** για όλες τις ενέργειες

### 📊 **Logging & Monitoring**
- **Comprehensive logging** όλων των submissions
- **Statistics** και analytics
- **Error tracking** και debugging
- **Log rotation** και cleanup
- **Performance monitoring**

## 🗂️ Αρχεία Project

```
📁 WorkRoom W/
├── 📄 index.html                 # Αρχική σελίδα
├── 📄 news.html                  # Σελίδα νέων με φόρμα επικοινωνίας
├── 📄 projects.html              # Σελίδα έργων
├── 📄 privacy.html               # Πολιτική απορρήτου
├── 📄 cookies_policy.html        # Πολιτική cookies
├── 📄 process-contact.php        # PHP handler για τη φόρμα
├── 📄 config.php                 # Ρυθμίσεις συστήματος
├── 📄 log-manager.php            # Διαχείριση logs
├── 📄 .htaccess                  # Apache security rules
├── 📄 DEPLOYMENT_GUIDE.md        # Οδηγίες deployment
├── 📄 README.md                  # Αυτό το αρχείο
├── 📁 images/                    # Εικόνες website
├── 📁 js/                        # JavaScript files
├── 📁 style.css                  # CSS styles
└── 📁 logs/                      # Log files (δημιουργείται αυτόματα)
```

## 🚀 Γρήγορη Εγκατάσταση

### **1. Ρυθμίσεις**
```bash
# Αντιγράψτε τα αρχεία στον server
# Δημιουργήστε φάκελο logs
mkdir logs
chmod 755 logs

# Ενημερώστε το config.php
nano config.php
```

### **2. Email Configuration**
```php
// Στο config.php:
'admin_email' => 'your-email@workroom.gr',
'site_url' => 'https://workroom.gr',
```

### **3. Test**
1. Πηγαίνετε στο `news.html`
2. Συμπληρώστε τη φόρμα
3. Ελέγξτε το email σας

## ⚙️ Ρυθμίσεις

### **config.php - Κύριες Ρυθμίσεις**
```php
'admin_email' => 'workroomarchdesign@gmail.com',    // Email για notifications
'max_submissions_per_hour' => 5,                     // Rate limiting
'max_submissions_per_day' => 20,                     // Rate limiting
'data_retention_days' => 365,                        // GDPR compliance
'enable_spam_detection' => true,                     // Spam protection
```

### **Security Settings**
- **CSRF Protection**: Ενεργοποιημένο
- **Honeypot**: Ενεργοποιημένο
- **Rate Limiting**: 5/ώρα, 20/ημέρα
- **Spam Detection**: Keywords + patterns
- **Security Headers**: Πλήρης προστασία

## 📧 Email Templates

### **Admin Notification**
```
=== ΝΕΟ ΜΗΝΥΜΑ ΕΠΙΚΟΙΝΩΝΙΑΣ ===

Στοιχεία Επικοινωνίας:
Όνομα: [Name]
Email: [Email]
Τηλέφωνο: [Phone]
Θέμα: [Subject]

Μήνυμα:
[Message]

=== GDPR & ΝΟΜΙΚΑ ===
Συμφωνία GDPR: [Consent]
Marketing Συμφωνία: [Marketing]

=== ΤΕΧΝΙΚΑ ΣΤΟΙΧΕΙΑ ===
Ημερομηνία: [Date]
IP Address: [IP]
User Agent: [UserAgent]
```

## 🔍 Monitoring & Logs

### **Log Files**
- `logs/contact_logs.txt` - Όλα τα submissions
- `logs/rate_limit.json` - Rate limiting data

### **Log Format**
```json
{
  "timestamp": "2025-01-15 10:30:00",
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0...",
  "data": {
    "name": "John Doe",
    "email": "john@example.com",
    "success": true,
    "error": ""
  }
}
```

### **Statistics Available**
- Συνολικά submissions
- Επιτυχημένα/Αποτυχημένα
- Spam attempts
- Rate limit hits
- Submissions ανά ημέρα/ώρα
- Top IPs και User Agents

## 🛡️ Security Features

### **Input Validation**
- Email format validation
- Phone number validation
- Required field checking
- Length limits
- XSS prevention

### **Spam Protection**
- Honeypot fields
- Keyword detection
- Link counting
- Suspicious pattern detection
- Rate limiting

### **Security Headers**
```apache
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Content-Security-Policy: [configured]
Strict-Transport-Security: max-age=31536000
```

## ⚖️ GDPR Compliance

### **Data Processing**
- **Legal Basis**: Explicit consent
- **Purpose**: Communication and service provision
- **Retention**: 1 year (configurable)
- **Rights**: Access, rectification, erasure, portability

### **Consent Management**
- **Required**: GDPR consent for data processing
- **Optional**: Marketing communications
- **Withdrawal**: Right to withdraw consent
- **Documentation**: All consents logged

### **Data Protection**
- **Minimization**: Only necessary data collected
- **Security**: Encrypted transmission and storage
- **Access Control**: Restricted access to logs
- **Breach Notification**: Automated logging

## 🔧 Maintenance

### **Εβδομαδιαία**
- [ ] Check logs for errors
- [ ] Monitor spam attempts
- [ ] Verify email delivery
- [ ] Check disk space

### **Μηνιαία**
- [ ] Clean old logs
- [ ] Update security patches
- [ ] Test form functionality
- [ ] Review statistics

### **Ετήσια**
- [ ] SSL certificate renewal
- [ ] GDPR compliance review
- [ ] Full system backup
- [ ] Security audit

## 🚨 Troubleshooting

### **Common Issues**

**Φόρμα δεν στέλνει:**
```bash
# Check PHP logs
tail -f /var/log/apache2/error.log

# Check file permissions
ls -la process-contact.php
```

**Email δεν φτάνει:**
1. Check spam folder
2. Verify SMTP settings
3. Test with different email
4. Check hosting email limits

**403 Forbidden:**
```bash
# Check .htaccess syntax
apache2ctl configtest

# Check file permissions
chmod 644 .htaccess
```

**Rate Limiting Issues:**
```bash
# Clear rate limit data
rm logs/rate_limit.json
```

## 📞 Support

### **Documentation**
- `DEPLOYMENT_GUIDE.md` - Detailed deployment instructions
- `config.php` - Configuration options
- Code comments - Inline documentation

### **Logs**
- Check `logs/contact_logs.txt` for errors
- Monitor `logs/rate_limit.json` for rate limiting
- Review Apache/PHP error logs

### **Testing**
- Test form with valid data
- Test spam detection
- Test rate limiting
- Verify email delivery

## 📈 Performance

### **Optimizations**
- Gzip compression enabled
- Browser caching configured
- Minified CSS/JS
- Optimized images
- CDN ready

### **Monitoring**
- Log file sizes
- Memory usage
- Response times
- Error rates

## 🔄 Updates

### **Version History**
- **v1.0** - Initial release with PHP email system
- **v1.1** - Added comprehensive logging
- **v1.2** - Enhanced security features
- **v1.3** - GDPR compliance implementation

### **Future Enhancements**
- Database integration option
- Advanced analytics dashboard
- Multi-language support
- API endpoints

---

## 📝 License

This project is proprietary software for WorkRoom W Architecture & Design.

## 👥 Credits

Developed for WorkRoom W - Αρχιτεκτονικό Γραφείο
Contact: workroomarchdesign@gmail.com

---

**🎉 Thank you for using WorkRoom W Contact System!**
