import 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.umd.js';

CookieConsent.run({
  guiOptions: {
    consentModal: {
      layout: 'cloud',               // bar, box, cloud
      position: 'bottom center',   // θέση: top, bottom, left, right, center
      flipButtons: false
    },
    preferencesModal: {
      layout: 'box',               // box ή bar
      position: 'right'            // left ή right
    }
  },

  categories: {
    necessary: {
      enabled: true,
      readOnly: true
    },
    analytics: {}
  },

  language: {
    default: 'el',
    translations: {
      el: {
        consentModal: {
          title: 'Χρησιμοποιούμε cookies',
          description: 'Τα cookies μάς βοηθούν να βελτιώσουμε την εμπειρία σας. Μπορείτε να αποδεχτείτε όλα ή να επιλέξετε τις προτιμήσεις σας.',
          acceptAllBtn: 'Αποδοχή όλων',
          acceptNecessaryBtn: 'Απόρριψη μη απαραίτητων',
          showPreferencesBtn: 'Διαχείριση προτιμήσεων'
        },
        preferencesModal: {
          title: 'Διαχείριση προτιμήσεων cookies',
          acceptAllBtn: 'Αποδοχή όλων',
          acceptNecessaryBtn: 'Απόρριψη μη απαραίτητων',
          savePreferencesBtn: 'Αποθήκευση επιλογών',
          closeIconLabel: 'Κλείσιμο παραθύρου',
          sections: [
            {
              title: 'Απαραίτητα cookies',
              description: 'Αυτά τα cookies είναι αναγκαία για τη σωστή λειτουργία του ιστότοπου και δεν μπορούν να απενεργοποιηθούν.',
              linkedCategory: 'necessary'
            },
            {
              title: 'Στατιστικά & Analytics',
              description: 'Τα cookies αυτά μας επιτρέπουν να κατανοούμε πώς χρησιμοποιείται ο ιστότοπος και να βελτιώνουμε τις υπηρεσίες μας.',
              linkedCategory: 'analytics'
            },
            {
              title: 'Περισσότερες πληροφορίες',
              description: 'Για απορίες σχετικά με την πολιτική cookies, παρακαλούμε <a href="#contact-page">επικοινωνήστε μαζί μας</a>.'
            }
          ]
        }
      }
    }
  }
});
