# Instructions pour Codex — Génération de la spécification fonctionnelle complète d'une application de gestion commerciale / POS pour boutique d'électroménager

## Rôle à endosser

Tu agis comme **un analyste fonctionnel senior, un architecte logiciel et un expert des logiciels de gestion commerciale / POS**. Ton travail n'est PAS de coder. Ton travail est de produire une **spécification fonctionnelle complète et structurée**, précise au point de pouvoir servir de cahier des charges à un développeur.

## Contexte du projet

Application web complète de gestion commerciale et de vente pour une **boutique d'électroménager**, pensée pour le contexte **Afrique de l'Ouest** (paiement en espèces, Mobile Money, paiement mixte, vente à crédit, devise FCFA/XOF, imprimantes thermiques, connexion Internet parfois instable, évolutivité vers plusieurs boutiques/agences).

Objectifs principaux :
- gérer les articles et leurs caractéristiques
- enregistrer les approvisionnements
- gérer les entrées et sorties de stock
- enregistrer les ventes
- générer automatiquement des reçus/tickets de caisse imprimables
- suivre les paiements
- gérer les clients et les fournisseurs
- suivre la caisse
- produire des statistiques et rapports
- permettre plusieurs utilisateurs avec niveaux d'accès différents
- assurer la traçabilité de toutes les opérations

## Consigne générale

Ne te limite pas aux fonctionnalités listées ci-dessous : identifie aussi celles qui manquent pour obtenir une application professionnelle, moderne et évolutive. **Ne commence pas à coder.** Livre uniquement l'analyse fonctionnelle.

Pour chaque fonctionnalité, indique un niveau de priorité :
- 🔴 CRITIQUE : indispensable au fonctionnement
- 🟠 IMPORTANT : fortement recommandé
- 🟢 AVANCÉ : fonctionnalité évoluée pour une V2

## Structure attendue de la réponse (à suivre dans cet ordre)

1. **Types d'utilisateurs** — Analyser et détailler les rôles : Super Administrateur, Administrateur/Gérant, Vendeur/Caissier, Gestionnaire de stock/Magasinier, Comptable, et proposer d'autres rôles si pertinent. Pour chaque rôle : responsabilités, fonctionnalités accessibles, fonctionnalités interdites, permissions nécessaires.

2. **Dashboard administrateur** — Détailler widgets, cartes, graphiques et tableaux : indicateurs (CA jour/semaine/mois, nombre de ventes, panier moyen, bénéfice estimé, valeur du stock, ruptures, créances, dépenses, solde caisse...), graphiques (évolution CA/ventes, ventes par catégorie/produit/mode de paiement...), tableaux (dernières ventes, derniers approvisionnements, produits en rupture...). Préciser ce qui doit être visible immédiatement après connexion.

3. **Gestion des produits** — Attributs du produit (SKU, code-barres, nom, marque, modèle, catégorie, prix d'achat/vente/promo, stock, seuil d'alerte, garantie, numéro de série, caractéristiques techniques...). Analyser produits sérialisés/non sérialisés, variantes, marques, catégories, unités, codes-barres, promotions.

4. **Gestion des approvisionnements** — Commande fournisseur, réception (totale/partielle), mise à jour du stock, factures et paiements fournisseurs, dettes fournisseurs, historique. Décrire le workflow : Commande → Réception → Entrée en stock → Facture → Paiement.

5. **Gestion du stock** — Consultation, mouvements, entrées/sorties, transferts, ajustements, inventaire physique, correction des écarts, seuils et alertes, valorisation du stock. Expliquer la mise à jour automatique lors d'une vente/d'un approvisionnement.

6. **Gestion des ventes / POS** — Parcours de vente complet (recherche produit, scan code-barres, panier, remise, calcul du total, mode de paiement, enregistrement, reçu, impression). Modes de paiement : espèces, carte, Mobile Money, virement, mixte, crédit. Analyser annulation, modification, retour, échange, remboursement, remise, vente à crédit, acompte, solde restant.

7. **Reçu / ticket de caisse** — Contenu détaillé (logo, boutique, adresse, téléphone, n° reçu, date/heure, vendeur, client, produits, quantités, prix, remises, sous-total, taxes, total, montant payé, monnaie rendue, mode de paiement, garantie, remerciement). Formats : thermique 58 mm et 80 mm, PDF, réimpression, envoi WhatsApp/email.

8. **Gestion des clients** — Création/modification/désactivation, historique d'achats, montant total dépensé, crédit, dettes, fidélité, factures associées, spécificités clients professionnels.

9. **Gestion des fournisseurs** — Coordonnées, produits fournis, historique d'approvisionnement, factures, paiements, dettes, échéances, statistiques.

10. **Gestion de caisse** — Ouverture, fonds initial, ventes, entrées/sorties d'argent, dépenses, retraits, clôture, solde théorique vs réel, différence de caisse, historique, gestion multi-caisses/multi-vendeurs.

11. **Gestion des dépenses** — Catégories (transport, électricité, internet, salaire, entretien, loyer, fournitures, réparation, autres), montant, catégorie, date, description, utilisateur, justificatif.

12. **Retours et remboursements** — Retour total/partiel, motif, produit défectueux, échange, remboursement, remise en stock, produit endommagé, historique. Impact sur stock, caisse, CA, statistiques, bénéfices.

13. **Garantie et SAV** — Produit vendu, numéro de série, client, date d'achat, durée et fin de garantie, problème signalé, statut SAV, réparation, remplacement, retour fournisseur, historique des interventions.

14. **Statistiques et rapports** — Ventes (CA par période, panier moyen, ventes par vendeur/produit/catégorie/marque), produits (plus/moins vendus, plus rentables, faible rotation, ruptures), rentabilité (CA, coût d'achat, marge, bénéfice brut/net), clients (meilleurs clients, actifs/inactifs, crédit), fournisseurs (les plus utilisés, volumes, montants). Export PDF/Excel/CSV.

15. **Notifications et alertes** — Stock faible, rupture, nouveau stock reçu, vente importante, crédit client à échéance, dette fournisseur, garantie proche d'expiration, caisse non clôturée, anomalie de stock, etc.

16. **Gestion des utilisateurs et sécurité** — Utilisateurs, rôles, permissions, authentification, mot de passe (changement/réinitialisation), activation/désactivation de compte, journal des connexions, journal des activités, historique des modifications, traçabilité des suppressions. Préciser quelles actions doivent être loguées.

17. **Paramètres de l'application** — Informations boutique (logo, adresse, téléphone, email), devise, format des reçus, taxes, numérotation des reçus, seuils de stock, modes de paiement, paramètres de garantie/caisse/impression.

18. **Menu de navigation** — Proposer/améliorer la structure du menu (Dashboard, Ventes, Produits, Stock, Approvisionnements, Clients, Fournisseurs, Caisse, Dépenses, Garantie/SAV, Rapports, Utilisateurs, Journal d'activité, Paramètres).

19. **Matrice de permissions** — Tableau complet fonctionnalité × rôle (Super Admin, Admin, Caissier, Magasinier, Comptable), avec pour chaque fonctionnalité : consulter, créer, modifier, supprimer, valider, exporter.

20. **Workflows métier** — Détailler au minimum :
    - Approvisionnement → Réception → Stock
    - Produit → Panier → Vente → Paiement → Reçu → Mise à jour du stock
    - Vente → Retour → Remboursement → Stock
    - Ouverture caisse → Ventes → Dépenses → Clôture caisse
    - Vente → Garantie → SAV
    - Client → Achat à crédit → Paiement → Solde
    Expliquer les règles métier propres à chaque workflow.

21. **Règles métier** — Lister toutes les règles importantes (ex. : pas de vente supérieure au stock disponible sauf autorisation explicite, numéro de vente unique, traçabilité de chaque mouvement de stock, restrictions de modification/suppression selon le rôle, conservation des clôtures de caisse, lien retour↔vente, etc.).

22. **Base de données (conception fonctionnelle, pas de SQL)** — Lister les principales tables (users, roles, permissions, products, categories, brands, suppliers, customers, purchases, purchase_items, sales, sale_items, payments, stock_movements, cash_registers, expenses, returns, warranties, notifications, activity_logs, etc.) avec pour chacune : nom, rôle, principales colonnes, relations.

23. **Dashboards par rôle** — Détailler les widgets/informations spécifiques pour : Super Admin (vision globale), Administrateur (commercial/stock/caisse/rentabilité), Caissier (ventes du jour/caisse/panier/reçus), Magasinier (stock/mouvements/approvisionnements/alertes), Comptable (CA/paiements/dépenses/créances/dettes/bénéfices).

24. **Expérience utilisateur** — Exigences (simplicité, rapidité, responsive, desktop/tablette/smartphone, optimisation caisse, prise en main facile). Bonnes pratiques UX pour la page de vente, la recherche produit, le panier, l'impression du reçu, les tableaux, les formulaires, les alertes.

25. **Adaptation au contexte africain** — Espèces, Mobile Money, paiement mixte, vente à crédit, clients professionnels, devise FCFA/XOF, imprimantes thermiques, connexion instable, simplicité, évolutivité multi-boutiques. Ajouter toute autre contrainte pertinente.

26. **Synthèse finale (architecture fonctionnelle)** — Récapituler : modules principaux, sous-modules, rôles, permissions, dashboard de chaque rôle, workflows, règles métier, tables principales, relations, rapports, notifications, sécurité, fonctionnalités MVP vs fonctionnalités V2.

27. **Ordre de développement proposé** — Proposer un ordre de développement des modules, du plus prioritaire au moins prioritaire, en évitant les dépendances incohérentes (ex. : utilisateurs/rôles → produits/catégories → stock → ventes/POS → caisse → clients/fournisseurs → approvisionnements → retours → garantie/SAV → statistiques → notifications → paramètres avancés).

## Format de sortie attendu

- Réponse structurée en sections numérotées correspondant aux points ci-dessus.
- Utiliser des tableaux là où c'est pertinent (rôles, permissions, tables de base de données).
- Étiqueter chaque fonctionnalité avec son niveau de priorité (🔴/🟠/🟢).
- Pas de code, pas de SQL, pas de structure Laravel à ce stade — uniquement l'analyse fonctionnelle et la conception de données de haut niveau.