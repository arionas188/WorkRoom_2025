-- ================================================================
-- SUPABASE DATABASE SETUP FOR WORKROOM W ADMIN SYSTEM
-- ================================================================
-- Τρέξτε αυτό το script στο Supabase SQL Editor
-- ================================================================

-- 1. CONTACT MESSAGES TABLE
-- Αποθηκεύει όλα τα μηνύματα από τη φόρμα επικοινωνίας
CREATE TABLE IF NOT EXISTS contact_messages (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    gdpr_consent BOOLEAN DEFAULT FALSE,
    marketing_consent BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    is_archived BOOLEAN DEFAULT FALSE,
    admin_notes TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    read_at TIMESTAMP WITH TIME ZONE,
    archived_at TIMESTAMP WITH TIME ZONE
);

-- 2. ADMIN USERS TABLE
-- Αποθηκεύει τους διαχειριστές (μέχρι 5)
CREATE TABLE IF NOT EXISTS admin_users (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    auth_id UUID REFERENCES auth.users(id) ON DELETE CASCADE,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(20) CHECK (role IN ('super_admin', 'admin')) DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL,
    last_login TIMESTAMP WITH TIME ZONE
);

-- 3. ADMIN ACTIVITY LOG TABLE
-- Καταγράφει όλες τις ενέργειες των διαχειριστών
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    admin_id UUID REFERENCES admin_users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id UUID,
    details JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT TIMEZONE('utc'::text, NOW()) NOT NULL
);

-- 4. INDEXES για καλύτερη απόδοση
CREATE INDEX IF NOT EXISTS idx_contact_messages_email ON contact_messages(email);
CREATE INDEX IF NOT EXISTS idx_contact_messages_created_at ON contact_messages(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_contact_messages_is_read ON contact_messages(is_read);
CREATE INDEX IF NOT EXISTS idx_contact_messages_is_archived ON contact_messages(is_archived);
CREATE INDEX IF NOT EXISTS idx_admin_users_email ON admin_users(email);
CREATE INDEX IF NOT EXISTS idx_admin_users_auth_id ON admin_users(auth_id);
CREATE INDEX IF NOT EXISTS idx_admin_activity_log_admin_id ON admin_activity_log(admin_id);
CREATE INDEX IF NOT EXISTS idx_admin_activity_log_created_at ON admin_activity_log(created_at DESC);

-- 5. AUTO-UPDATE TIMESTAMPS
-- Function για αυτόματη ενημέρωση του updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = TIMEZONE('utc'::text, NOW());
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Triggers για auto-update
CREATE TRIGGER update_contact_messages_updated_at BEFORE UPDATE ON contact_messages
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_admin_users_updated_at BEFORE UPDATE ON admin_users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 6. ROW LEVEL SECURITY (RLS) POLICIES
-- Ενεργοποίηση RLS
ALTER TABLE contact_messages ENABLE ROW LEVEL SECURITY;
ALTER TABLE admin_users ENABLE ROW LEVEL SECURITY;
ALTER TABLE admin_activity_log ENABLE ROW LEVEL SECURITY;

-- Policy: Όλοι μπορούν να δημιουργήσουν contact message (για τη φόρμα)
CREATE POLICY "Anyone can insert contact messages"
    ON contact_messages FOR INSERT
    WITH CHECK (true);

-- Policy: Μόνο authenticated admins μπορούν να διαβάσουν messages
CREATE POLICY "Authenticated admins can view messages"
    ON contact_messages FOR SELECT
    USING (
        EXISTS (
            SELECT 1 FROM admin_users
            WHERE admin_users.auth_id = auth.uid()
            AND admin_users.is_active = true
        )
    );

-- Policy: Μόνο authenticated admins μπορούν να ενημερώσουν messages
CREATE POLICY "Authenticated admins can update messages"
    ON contact_messages FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM admin_users
            WHERE admin_users.auth_id = auth.uid()
            AND admin_users.is_active = true
        )
    );

-- Policy: Μόνο super_admin μπορεί να διαγράψει messages
CREATE POLICY "Super admin can delete messages"
    ON contact_messages FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM admin_users
            WHERE admin_users.auth_id = auth.uid()
            AND admin_users.role = 'super_admin'
            AND admin_users.is_active = true
        )
    );

-- Policy: Admins μπορούν να δουν το δικό τους profile
CREATE POLICY "Admins can view their own profile"
    ON admin_users FOR SELECT
    USING (auth_id = auth.uid() OR is_active = true);

-- Policy: Super admin μπορεί να διαχειριστεί admins
CREATE POLICY "Super admin can manage admins"
    ON admin_users FOR ALL
    USING (
        EXISTS (
            SELECT 1 FROM admin_users AS au
            WHERE au.auth_id = auth.uid()
            AND au.role = 'super_admin'
            AND au.is_active = true
        )
    );

-- Policy: Admins μπορούν να δουν τα δικά τους logs
CREATE POLICY "Admins can view their activity logs"
    ON admin_activity_log FOR SELECT
    USING (
        admin_id IN (
            SELECT id FROM admin_users WHERE auth_id = auth.uid()
        )
    );

-- Policy: System μπορεί να γράψει activity logs
CREATE POLICY "System can insert activity logs"
    ON admin_activity_log FOR INSERT
    WITH CHECK (true);

-- 7. FUNCTIONS για business logic

-- Function: Mark message as read
CREATE OR REPLACE FUNCTION mark_message_as_read(message_id UUID)
RETURNS void AS $$
BEGIN
    UPDATE contact_messages
    SET is_read = true,
        read_at = TIMEZONE('utc'::text, NOW())
    WHERE id = message_id;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Function: Archive message
CREATE OR REPLACE FUNCTION archive_message(message_id UUID)
RETURNS void AS $$
BEGIN
    UPDATE contact_messages
    SET is_archived = true,
        archived_at = TIMEZONE('utc'::text, NOW())
    WHERE id = message_id;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Function: Get unread messages count
CREATE OR REPLACE FUNCTION get_unread_messages_count()
RETURNS INTEGER AS $$
BEGIN
    RETURN (SELECT COUNT(*) FROM contact_messages WHERE is_read = false AND is_archived = false);
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- 8. INSERT SUPER ADMIN
-- Email: arionaskonstantinos@me.com (Super Admin)
-- Αυτό θα δημιουργηθεί μετά το πρώτο signup στο Supabase Auth
-- Για τώρα, απλά δημιουργούμε ένα placeholder entry

-- Αυτό θα ενημερωθεί μέσω trigger όταν κάνετε signup
-- Δείτε το section 9 παρακάτω

-- 9. TRIGGER: Auto-create admin_user entry after auth signup
CREATE OR REPLACE FUNCTION handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
    -- Ελέγχουμε αν το email είναι στη λίστα των επιτρεπόμενων admins
    IF NEW.email IN (
        'arionaskonstantinos@me.com'
        -- Προσθέστε μέχρι 4 ακόμα emails εδώ
        -- , 'admin2@example.com'
        -- , 'admin3@example.com'
        -- , 'admin4@example.com'
        -- , 'admin5@example.com'
    ) THEN
        INSERT INTO admin_users (auth_id, email, name, role, is_active)
        VALUES (
            NEW.id,
            NEW.email,
            COALESCE(NEW.raw_user_meta_data->>'name', 'Admin User'),
            CASE 
                WHEN NEW.email = 'arionaskonstantinos@me.com' THEN 'super_admin'
                ELSE 'admin'
            END,
            true
        );
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Create trigger for new user signup
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
    AFTER INSERT ON auth.users
    FOR EACH ROW EXECUTE FUNCTION handle_new_user();

-- 10. FUNCTION: Log admin activity
CREATE OR REPLACE FUNCTION log_admin_activity(
    p_action VARCHAR,
    p_entity_type VARCHAR DEFAULT NULL,
    p_entity_id UUID DEFAULT NULL,
    p_details JSONB DEFAULT NULL
)
RETURNS void AS $$
DECLARE
    v_admin_id UUID;
BEGIN
    -- Get admin_id from auth.uid()
    SELECT id INTO v_admin_id
    FROM admin_users
    WHERE auth_id = auth.uid();
    
    IF v_admin_id IS NOT NULL THEN
        INSERT INTO admin_activity_log (admin_id, action, entity_type, entity_id, details)
        VALUES (v_admin_id, p_action, p_entity_type, p_entity_id, p_details);
    END IF;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- 11. VIEW: Dashboard statistics
CREATE OR REPLACE VIEW admin_dashboard_stats AS
SELECT
    (SELECT COUNT(*) FROM contact_messages WHERE is_archived = false) as total_messages,
    (SELECT COUNT(*) FROM contact_messages WHERE is_read = false AND is_archived = false) as unread_messages,
    (SELECT COUNT(*) FROM contact_messages WHERE is_archived = true) as archived_messages,
    (SELECT COUNT(*) FROM contact_messages WHERE created_at >= CURRENT_DATE) as today_messages,
    (SELECT COUNT(*) FROM contact_messages WHERE created_at >= CURRENT_DATE - INTERVAL '7 days') as week_messages,
    (SELECT COUNT(*) FROM admin_users WHERE is_active = true) as active_admins;

-- Grant access to the view
GRANT SELECT ON admin_dashboard_stats TO authenticated;

-- ================================================================
-- SETUP COMPLETE!
-- ================================================================
-- 
-- ΕΠΟΜΕΝΑ ΒΗΜΑΤΑ:
-- 
-- 1. Τρέξτε αυτό το script στο Supabase SQL Editor
-- 
-- 2. Πηγαίνετε στο Authentication > Settings και:
--    - Ενεργοποιήστε Email provider
--    - Απενεργοποιήστε "Enable email confirmations" (για testing)
--    - Ή ρυθμίστε SMTP για production
-- 
-- 3. Δημιουργήστε τον πρώτο admin από το Supabase Dashboard:
--    Authentication > Users > Invite User
--    Email: arionaskonstantinos@me.com
--    (Ή χρησιμοποιήστε το admin signup που θα φτιάξουμε)
-- 
-- 4. Ελέγξτε τα RLS policies στο Table Editor
-- 
-- ================================================================

