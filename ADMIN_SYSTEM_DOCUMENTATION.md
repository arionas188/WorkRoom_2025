# 📚 WorkRoom W - Admin System Documentation

## 🎯 Περιεχόμενα

1. [Επισκόπηση Συστήματος](#επισκόπηση-συστήματος)
2. [Εγκατάσταση & Setup](#εγκατάσταση--setup)
3. [Αρχιτεκτονική & Ασφάλεια](#αρχιτεκτονική--ασφάλεια)
4. [Χρήση του Συστήματος](#χρήση-του-συστήματος)
5. [Αρχεία & Δομή](#αρχεία--δομή)
6. [Troubleshooting](#troubleshooting)

---

## 📋 Επισκόπηση Συστήματος

Το Admin System της WorkRoom W είναι ένα πλήρες, ασφαλές σύστημα διαχείρισης μηνυμάτων επικοινωνίας που χρησιμοποιεί:

### ✨ Χαρακτηριστικά

- **Secure Authentication**: Ολοκληρωμένο σύστημα login με Supabase Auth
- **Role-Based Access**: Super Admin & Admin roles
- **Real-time Updates**: Άμεση ενημέρωση για νέα μηνύματα
- **Complete CRUD**: Create, Read, Update, Delete operations
- **Advanced Filtering**: Φιλτράρισμα μηνυμάτων κατά status και θέμα
- **CSV Export**: Εξαγωγή δεδομένων σε CSV
- **Email Integration**: Απάντηση απευθείας μέσω email client
- **Admin Notes**: Προσθήκη σημειώσεων σε κάθε μήνυμα
- **Audit Logging**: Καταγραφή όλων των ενεργειών των admins

### 🔒 Security Features

- **Row Level Security (RLS)**: Database-level security policies
- **Password Hashing**: Bcrypt hashing μέσω Supabase
- **CSRF Protection**: Honeypot fields για spam detection
- **Input Validation**: Client & server-side validation
- **XSS Protection**: HTML escaping
- **IP Logging**: Καταγραφή IP για audit trail
- **Session Management**: Secure session handling
- **Access Control**: Έλεγχος πρόσβασης σε κάθε endpoint

---

## 🚀 Εγκατάσταση & Setup

### Βήμα 1: Supabase Setup

1. **Πηγαίνετε στο Supabase Dashboard**: https://app.supabase.com
2. **Ανοίξτε το SQL Editor**
3. **Αντιγράψτε και τρέξτε το `supabase_setup.sql`**

```sql
-- Το αρχείο περιέχει:
-- ✅ Database tables (contact_messages, admin_users, admin_activity_log)
-- ✅ Indexes για performance
-- ✅ Row Level Security policies
-- ✅ Triggers για auto-updates
-- ✅ Functions για business logic
-- ✅ Views για statistics
```

4. **Ελέγξτε ότι όλα τα tables δημιουργήθηκαν επιτυχώς**

### Βήμα 2: Authentication Setup

1. **Πηγαίνετε στο Authentication > Settings**
2. **Ενεργοποιήστε Email Provider**
3. **Για Development**:
   - Απενεργοποιήστε "Enable email confirmations"
   - Αυτό επιτρέπει instant signup
4. **Για Production**:
   - Ρυθμίστε SMTP settings
   - Ενεργοποιήστε email confirmations
   - Προσθέστε custom email templates

### Βήμα 3: Δημιουργία Super Admin

**Επιλογή A: Μέσω Supabase Dashboard**
```
1. Πηγαίνετε στο Authentication > Users
2. Κάντε κλικ "Invite User"
3. Email: arionaskonstantinos@me.com
4. Το system θα δημιουργήσει αυτόματα admin record (μέσω trigger)
```

**Επιλογή B: Μέσω Admin Login Page**
```
1. Ανοίξτε το admin-login.html
2. Κάντε κλικ "Create account"
3. Εισάγετε: arionaskonstantinos@me.com
4. Ορίστε ισχυρό password (min 8 χαρακτήρες)
5. Το system θα δημιουργήσει τον Super Admin
```

### Βήμα 4: Προσθήκη Επιπλέον Admins (έως 4)

1. **Ανοίξτε το `supabase_setup.sql`**
2. **Βρείτε το section 9 (Handle new user trigger)**
3. **Προσθέστε emails στη λίστα**:

```sql
IF NEW.email IN (
    'arionaskonstantinos@me.com',
    'admin2@example.com',      -- Προσθέστε εδώ
    'admin3@example.com',      -- Προσθέστε εδώ
    'admin4@example.com',      -- Προσθέστε εδώ
    'admin5@example.com'       -- Προσθέστε εδώ
) THEN
```

4. **Ενημερώστε και τα HTML αρχεία**:

**admin-login.html** (γραμμή ~182):
```javascript
const ALLOWED_ADMINS = [
    'arionaskonstantinos@me.com',
    'admin2@example.com',  // Προσθέστε εδώ
    // κλπ...
];
```

5. **Τρέξτε ξανά το trigger στη Supabase**
6. **Δημιουργήστε τους admins μέσω signup**

### Βήμα 5: Upload Files στον Server

**Ανεβάστε τα παρακάτω αρχεία**:
```
✅ news.html (updated με Supabase φόρμα)
✅ admin-login.html
✅ admin-dashboard.html
✅ supabase_setup.sql (για reference)
✅ ADMIN_SYSTEM_DOCUMENTATION.md (αυτό το αρχείο)
```

**ΠΡΟΣΟΧΗ**: Μην ανεβάσετε τα Supabase credentials σε public repository!

---

## 🏗️ Αρχιτεκτονική & Ασφάλεια

### Database Schema

```
┌─────────────────────────┐
│   contact_messages      │
├─────────────────────────┤
│ id (UUID, PK)          │
│ name (VARCHAR)         │
│ email (VARCHAR)        │
│ phone (VARCHAR)        │
│ subject (VARCHAR)      │
│ message (TEXT)         │
│ gdpr_consent (BOOL)    │
│ marketing_consent      │
│ is_read (BOOL)         │
│ is_archived (BOOL)     │
│ admin_notes (TEXT)     │
│ ip_address (VARCHAR)   │
│ user_agent (TEXT)      │
│ created_at (TIMESTAMP) │
│ updated_at (TIMESTAMP) │
└─────────────────────────┘

┌─────────────────────────┐
│     admin_users         │
├─────────────────────────┤
│ id (UUID, PK)          │
│ auth_id (UUID, FK)     │
│ email (VARCHAR)        │
│ name (VARCHAR)         │
│ role (ENUM)            │
│ is_active (BOOL)       │
│ created_at (TIMESTAMP) │
│ last_login (TIMESTAMP) │
└─────────────────────────┘

┌─────────────────────────┐
│  admin_activity_log     │
├─────────────────────────┤
│ id (UUID, PK)          │
│ admin_id (UUID, FK)    │
│ action (VARCHAR)       │
│ entity_type (VARCHAR)  │
│ entity_id (UUID)       │
│ details (JSONB)        │
│ ip_address (VARCHAR)   │
│ created_at (TIMESTAMP) │
└─────────────────────────┘
```

### Security Layers

1. **Frontend Security**
   - Input validation
   - XSS prevention (HTML escaping)
   - CSRF tokens
   - Honeypot spam detection

2. **Authentication Layer**
   - Supabase Auth (industry-standard)
   - Password hashing (bcrypt)
   - Session management
   - Allowed emails whitelist

3. **Database Security**
   - Row Level Security (RLS)
   - Parameterized queries (SQL injection prevention)
   - Role-based access policies
   - Audit logging

4. **Network Security**
   - HTTPS required for production
   - IP logging
   - Rate limiting (via Supabase)

### Row Level Security Policies

```sql
-- Όλοι μπορούν να δημιουργήσουν contact message
CREATE POLICY "Anyone can insert contact messages"
    ON contact_messages FOR INSERT
    WITH CHECK (true);

-- Μόνο authenticated admins διαβάζουν messages
CREATE POLICY "Authenticated admins can view messages"
    ON contact_messages FOR SELECT
    USING (EXISTS (SELECT 1 FROM admin_users WHERE auth_id = auth.uid()));

-- Μόνο super_admin διαγράφει messages
CREATE POLICY "Super admin can delete messages"
    ON contact_messages FOR DELETE
    USING (EXISTS (
        SELECT 1 FROM admin_users 
        WHERE auth_id = auth.uid() 
        AND role = 'super_admin'
    ));
```

---

## 💻 Χρήση του Συστήματος

### Για τους Επισκέπτες (news.html)

1. **Υπάρχουσα Φόρμα (PHP)**
   - Στέλνει email στο admin
   - Αποθηκεύει σε logs
   - Χρησιμοποιεί το `process-contact-simple.php`

2. **Νέα Φόρμα (Supabase)**
   - Τίτλος: "Με αυτή στέλνουμε μήνυμα στη Supabase"
   - Αποθηκεύει απευθείας στη database
   - Real-time updates στο dashboard
   - Πλήρης GDPR compliance

### Για τους Admins

#### 1. Login (admin-login.html)

**Features**:
- ✅ Email/Password authentication
- ✅ Remember me functionality
- ✅ Password visibility toggle
- ✅ Forgot password recovery
- ✅ First-time signup
- ✅ Access control (only allowed emails)

**Login Flow**:
```
1. Εισάγετε email & password
2. System ελέγχει αν το email είναι στη λίστα
3. Supabase authenticates
4. System ελέγχει admin_users record
5. Ενημερώνει last_login
6. Καταγράφει login activity
7. Redirect στο dashboard
```

#### 2. Dashboard (admin-dashboard.html)

**Statistics Dashboard**:
- 📊 Total Messages: Σύνολο μηνυμάτων
- 🔔 Unread: Μη αναγνωσμένα
- 📅 Today: Σημερινά μηνύματα
- 📦 Archived: Αρχειοθετημένα

**Filters**:
- **Status**: All / Unread / Read / Archived
- **Subject**: All / Γενικές Πληροφορίες / Νέο Έργο / κλπ

**Actions**:
- 🔄 **Refresh**: Ανανέωση λίστας
- 📥 **Export CSV**: Εξαγωγή σε CSV

**Message List**:
- View: Προβολή λεπτομερειών
- Mark Read: Σήμανση ως διαβασμένο
- Delete: Διαγραφή (μόνο super_admin)

**Message Detail Modal**:
- 📧 Πλήρη στοιχεία μηνύματος
- 📝 Admin notes (αποθήκευση σημειώσεων)
- 📦 Archive: Αρχειοθέτηση
- 🗑️ Delete: Διαγραφή (super_admin)
- ✉️ Reply: Απάντηση μέσω email client

#### 3. Real-time Updates

Το dashboard ακούει για real-time changes:
```javascript
supabase
    .channel('contact_messages_changes')
    .on('postgres_changes', { event: '*', ... })
    .subscribe();
```

Όταν έρθει νέο μήνυμα, το dashboard ενημερώνεται αυτόματα!

---

## 📁 Αρχεία & Δομή

### Δημιουργημένα Αρχεία

```
📦 Project Root
├── 📄 news.html (UPDATED)
│   ├── Υπάρχουσα PHP φόρμα
│   └── ➕ Νέα Supabase φόρμα
│
├── 📄 admin-login.html (NEW)
│   ├── Secure authentication
│   ├── Signup functionality
│   ├── Password recovery
│   └── Access control
│
├── 📄 admin-dashboard.html (NEW)
│   ├── Statistics dashboard
│   ├── Message list with filters
│   ├── Message detail modal
│   ├── CRUD operations
│   ├── CSV export
│   └── Real-time updates
│
├── 📄 supabase_setup.sql (NEW)
│   ├── Database schema
│   ├── RLS policies
│   ├── Functions & triggers
│   └── Initial data
│
└── 📄 ADMIN_SYSTEM_DOCUMENTATION.md (NEW)
    └── Αυτό το αρχείο!
```

### Code Structure

**news.html** - Supabase Form Section:
```html
<!-- Lines 366-502: New Supabase Contact Form -->
<div class="bg-white py-24 sm:py-32 border-t-4 border-indigo-600">
  <form id="supabase-contact-form">
    <!-- Form fields -->
  </form>
</div>

<!-- Lines 791-991: Supabase JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script>
  const supabase = window.supabase.createClient(...);
  // Form handling logic
</script>
```

**admin-login.html**:
```html
<!DOCTYPE html>
<html lang="el">
  <head>...</head>
  <body>
    <!-- Login Form -->
    <form id="login-form">...</form>
    
    <!-- Supabase Auth Logic -->
    <script>
      // Authentication handling
      // Signup logic
      // Password recovery
    </script>
  </body>
</html>
```

**admin-dashboard.html**:
```html
<!DOCTYPE html>
<html lang="el">
  <head>...</head>
  <body>
    <!-- Statistics Cards -->
    <!-- Filters & Actions -->
    <!-- Messages Table -->
    <!-- Message Detail Modal -->
    
    <!-- Dashboard Logic -->
    <script>
      // Load messages
      // Render UI
      // CRUD operations
      // Real-time subscriptions
      // CSV export
    </script>
  </body>
</html>
```

---

## 🔧 Troubleshooting

### Πρόβλημα 1: "Access denied" στο login

**Αιτία**: Το email δεν είναι στη λίστα ALLOWED_ADMINS

**Λύση**:
1. Ανοίξτε το `admin-login.html`
2. Βρείτε το `ALLOWED_ADMINS` array (γραμμή ~182)
3. Προσθέστε το email σας
4. Ενημερώστε και το trigger στη Supabase (supabase_setup.sql, section 9)

### Πρόβλημα 2: "No admin access found"

**Αιτία**: Δεν υπάρχει admin_users record

**Λύση**:
1. Ελέγξτε ότι το trigger "on_auth_user_created" τρέχει
2. Ελέγξτε το admin_users table στη Supabase
3. Αν δεν υπάρχει record, δημιουργήστε manually:

```sql
INSERT INTO admin_users (auth_id, email, name, role)
SELECT id, email, 'Admin Name', 'super_admin'
FROM auth.users
WHERE email = 'arionaskonstantinos@me.com';
```

### Πρόβλημα 3: Dashboard δεν φορτώνει μηνύματα

**Αιτία**: RLS policies block access

**Λύση**:
1. Ελέγξτε ότι τα RLS policies δημιουργήθηκαν
2. Ελέγξτε το browser console για errors
3. Ελέγξτε ότι ο admin έχει is_active = true:

```sql
SELECT * FROM admin_users WHERE email = 'your@email.com';
-- Αν is_active = false:
UPDATE admin_users SET is_active = true WHERE email = 'your@email.com';
```

### Πρόβλημα 4: Φόρμα δεν στέλνει μηνύματα

**Αιτία**: Supabase credentials λάθος ή RLS policy

**Λύση**:
1. Ελέγξτε το browser console
2. Βεβαιωθείτε ότι το SUPABASE_URL και SUPABASE_ANON_KEY είναι σωστά
3. Ελέγξτε το "Anyone can insert" policy:

```sql
-- Ελέγξτε αν υπάρχει:
SELECT * FROM pg_policies WHERE tablename = 'contact_messages' AND policyname = 'Anyone can insert contact messages';
```

### Πρόβλημα 5: CSV Export δεν λειτουργεί

**Αιτία**: Browser block ή JavaScript error

**Λύση**:
1. Ελέγξτε browser console
2. Δοκιμάστε σε άλλο browser
3. Ελέγξτε popup blockers

### Πρόβλημα 6: Real-time updates δεν δουλεύουν

**Αιτία**: Supabase Realtime δεν είναι enabled

**Λύση**:
1. Πηγαίνετε στο Supabase Dashboard
2. Database > Replication
3. Enable replication για το `contact_messages` table

### Πρόβλημα 7: Email recovery δεν στέλνει email

**Αιτία**: SMTP δεν είναι ρυθμισμένο

**Λύση**:
1. Supabase Dashboard > Authentication > Settings
2. Scroll down στο "SMTP Settings"
3. Ρυθμίστε το SMTP server σας ή χρησιμοποιήστε Supabase's default για testing

---

## 📊 Performance Optimization

### Database Indexes

Το SQL script δημιουργεί indexes για:
- email lookups (contact_messages, admin_users)
- date sorting (created_at DESC)
- status filtering (is_read, is_archived)
- activity log queries

### Frontend Optimization

- **Lazy Loading**: Messages φορτώνουν μόνο όταν χρειάζονται
- **Debouncing**: Filters με debounce για λιγότερα queries
- **Caching**: Local cache για admin data
- **Real-time**: WebSocket connection για instant updates

### Supabase Connection Pooling

Το Supabase χειρίζεται αυτόματα:
- Connection pooling
- Query optimization
- Auto-scaling

---

## 🔐 Security Best Practices

### ✅ DO's

1. **Χρησιμοποιήστε HTTPS στο production**
2. **Ενημερώνετε τακτικά τα Supabase policies**
3. **Κρατήστε audit logs για 1+ χρόνο**
4. **Χρησιμοποιήστε strong passwords (12+ χαρακτήρες)**
5. **Enable 2FA στο Supabase Dashboard**
6. **Backup τη database τακτικά**
7. **Review activity logs περιοδικά**

### ❌ DON'Ts

1. **ΜΗΝ** αποθηκεύετε credentials σε public repos
2. **ΜΗΝ** απενεργοποιείτε RLS policies
3. **ΜΗΝ** χρησιμοποιείτε το service_role key στο frontend
4. **ΜΗΝ** δίνετε super_admin σε όλους
5. **ΜΗΝ** αποθηκεύετε sensitive data unencrypted

---

## 📞 Support & Maintenance

### Τακτική Συντήρηση

**Εβδομαδιαία**:
- ✅ Review activity logs
- ✅ Check unread messages
- ✅ Monitor error rates

**Μηνιαία**:
- ✅ Database cleanup (old logs)
- ✅ Backup export
- ✅ Security review
- ✅ Performance check

**Ετήσια**:
- ✅ Full security audit
- ✅ Update dependencies
- ✅ Review user access
- ✅ Archive old messages

### Updates & Upgrades

Για να προσθέσετε νέα features:

1. **Test στο Supabase staging project**
2. **Update database schema**
3. **Update HTML/JavaScript**
4. **Test thoroughly**
5. **Deploy σε production**
6. **Monitor για errors**

---

## 🎉 Conclusion

Συγχαρητήρια! Έχετε ένα πλήρες, ασφαλές admin system!

### Τι Φτιάξαμε

✅ Secure contact form με Supabase storage  
✅ Complete admin authentication system  
✅ Feature-rich admin dashboard  
✅ Real-time updates  
✅ CSV export functionality  
✅ Role-based access control  
✅ Comprehensive audit logging  
✅ GDPR compliant data handling  

### Next Steps

1. ✅ Τρέξτε το `supabase_setup.sql`
2. ✅ Δημιουργήστε τον super admin
3. ✅ Upload τα αρχεία στον server
4. ✅ Test όλες τις λειτουργίες
5. ✅ Προσθέστε επιπλέον admins αν χρειάζεται

---

## 📝 Change Log

### Version 1.0 (Initial Release)

**Created Files**:
- `news.html` (updated με Supabase φόρμα)
- `admin-login.html` (new)
- `admin-dashboard.html` (new)
- `supabase_setup.sql` (new)
- `ADMIN_SYSTEM_DOCUMENTATION.md` (new)

**Features**:
- ✅ Supabase integration
- ✅ Secure authentication
- ✅ Admin dashboard με CRUD
- ✅ CSV export
- ✅ Real-time updates
- ✅ Row Level Security
- ✅ Audit logging

**Security**:
- ✅ RLS policies
- ✅ Password hashing
- ✅ Input validation
- ✅ XSS protection
- ✅ CSRF protection
- ✅ Access control

---

**Created with ❤️ for WorkRoom W**  
**Powered by Supabase 🚀**

© 2025 WorkRoom W - Αρχιτεκτονικό Γραφείο

---

## 📞 Quick Reference

### Supabase URLs

- **Dashboard**: https://app.supabase.com
- **Project URL**: https://nhocpqwzxxbuggerslwp.supabase.co
- **SQL Editor**: Dashboard > SQL Editor
- **Auth Settings**: Dashboard > Authentication > Settings
- **Database**: Dashboard > Table Editor

### Admin URLs (Your Server)

- **Contact Form**: https://arionaskonstantinostest.xyz/news.html
- **Admin Login**: https://arionaskonstantinostest.xyz/admin-login.html
- **Dashboard**: https://arionaskonstantinostest.xyz/admin-dashboard.html

### Super Admin Credentials

- **Email**: arionaskonstantinos@me.com
- **Password**: (όπως το ορίσατε κατά το signup)

### Important SQL Queries

**View all admins**:
```sql
SELECT * FROM admin_users;
```

**View recent messages**:
```sql
SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10;
```

**View activity log**:
```sql
SELECT * FROM admin_activity_log ORDER BY created_at DESC LIMIT 20;
```

**Get statistics**:
```sql
SELECT * FROM admin_dashboard_stats;
```

**Reset admin password** (μέσω Supabase Dashboard):
```
Authentication > Users > [User] > Reset Password
```

---

**End of Documentation**

