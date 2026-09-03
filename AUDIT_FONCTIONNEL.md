# Audit fonctionnel PIOS

Date de l'audit : 26 aout 2026  
Reference : `instruction.md`

## Conclusion

Toutes les fonctionnalites demandees ne sont pas implementees. Le projet constitue un MVP avance : les principaux ecrans et tables existent, mais plusieurs workflows ne sont pas termines et certaines regles metier critiques ne sont pas encore protegees cote serveur.

Legende : **Implemente** = parcours principal utilisable ; **Partiel** = base presente mais exigences incompletes ; **Absent** = aucun parcours exploitable identifie.

## Couverture des 27 sections

| # | Domaine | Etat | Elements constates | Principaux ecarts |
|---:|---|---|---|---|
| 1 | Types d'utilisateurs | Partiel | 10 roles sont seeds, utilisateurs activables/desactivables | Seuls admin/proprietaire recoivent des permissions ; dashboards et restrictions detailles des autres roles non configures |
| 2 | Dashboard administrateur | Partiel | CA, ventes, panier, depenses, marge estimee, stock, creances, activite, top produits | Pas de CA semaine, solde caisse, dettes fournisseurs, paiements par mode, derniers approvisionnements ni alertes completes |
| 3 | Produits | Partiel | SKU, code-barres, categorie, marque, prix, promo, stock, seuil, unite, garantie, indicateur serialise | Aucun registre de numeros de serie, variantes, caracteristiques techniques structurees, historique de prix ou promotions planifiees |
| 4 | Approvisionnements | Partiel | Commande, lignes, reception partielle/totale, fournisseur, dette calculee | Pas de facture fournisseur, echeancier/paiements ulterieurs, validation, annulation robuste ni mouvement de stock cree a la reception |
| 5 | Stock | Partiel | Consultation, mouvements manuels, ajustement, inventaire, alertes faibles, entrepots | Transfert entre entrepots non implemente, stock non ventile par entrepot, ventes/receptions sans journal de mouvement, valorisation limitee |
| 6 | Ventes / POS | Partiel | Recherche/scan, panier, remise, taxes, especes/carte/Mobile Money/credit/mixte, annulation | Prix fourni par le navigateur, absence de controle/verrouillage du stock, pas d'echange, paiement ulterieur du credit, autorisation des remises ou virement de vente |
| 7 | Recu | Partiel | Recu HTML imprimable avec donnees principales et reimpression | Pas de formats 58/80 mm parametrables, PDF, WhatsApp/e-mail, QR/code-barres ni politique de garantie detaillee |
| 8 | Clients | Partiel | CRUD, activation, professionnel, plafond de credit, dette, depense totale, fidelite | Pas de fiche detaillee/historique d'achats, encaissement de creance, releve client ni controle effectif du plafond |
| 9 | Fournisseurs | Partiel | CRUD, activation, conditions, achats/dette/agence | Pas de fiche historique, paiements fournisseurs, factures, echeances gerees ni statistiques dediees |
| 10 | Caisse | Partiel | Ouverture, cloture, fonds, attendu/reel, ecart, totaux | Les ventes/depenses/paiements ne mettent pas clairement a jour la caisse ; pas d'entrees/sorties manuelles detaillees ni controle d'une caisse ouverte avant vente |
| 11 | Depenses | Partiel | Creation, liste, categories, justificatif en base, suppression | Pas de modification/validation, televersement verifie du justificatif ni workflow comptable |
| 12 | Retours/remboursements | Partiel | Retour total/partiel, motif, remboursement, remise en stock | Condition/restock forces a `good/true`, quantites retournables non securisees, aucun paiement sortant/caisse, echange et approbation absents |
| 13 | Garantie/SAV | Partiel | Garantie, numero de serie textuel, client, dates, probleme, statut, resolution, cout | Pas d'historique d'interventions, technicien, pieces, retour fournisseur, notifications d'expiration ni remplacement lie au stock |
| 14 | Statistiques/rapports | Partiel | CA, cout approximatif, depenses, marges, top produits/categories/clients/vendeurs, CSV | Cout calcule depuis les achats de la periode et non le cout des ventes ; pas de PDF/Excel, rotation, marques, fournisseurs, creances/detail multi-boutiques |
| 15 | Notifications | Partiel | Table, liste, lecture individuelle/globale | Aucun moteur constate pour generer automatiquement les alertes metier ou les envoyer hors application |
| 16 | Utilisateurs/securite | Partiel | Connexion, 2FA e-mail, roles/permissions, activation, audit partiel, isolation entreprise | Pas de modification utilisateur, reinitialisation/changement de mot de passe, journal de connexions, matrice fine CRUD/validation/export ; textes encodes incorrectement |
| 17 | Parametres | Partiel | Entreprise, coordonnees et table de reglages | Pas d'interface complete pour devise, taxe, recus, numerotation, impression, garanties, modes de paiement, logo et seuils globaux |
| 18 | Navigation | Implemente | Modules principaux accessibles dans la disposition admin | Visibilite a valider par role une fois les permissions non-admin configurees |
| 19 | Matrice de permissions | Absent | Permissions globales de type `manage ...` | Pas de droits consulter/creer/modifier/supprimer/valider/exporter par fonctionnalite et role |
| 20 | Workflows metier | Partiel | Les six domaines possedent des fragments de parcours | Dette client sans encaissement, SAV incomplet, caisse non integree, journal de stock incomplet |
| 21 | Regles metier | Partiel | Numeros uniques en base, transactions sur plusieurs ecritures, soft deletes | Vente au-dela du stock possible, prix modifiable par client, doubles retours/surreceptions possibles, concurrence sur numeros, annulations repetables |
| 22 | Base de donnees | Partiel | Environ 30 tables couvrent le coeur du domaine | Manquent notamment numeros de serie, variantes, paiements de creances/dettes complets, transferts, lignes de caisse, interventions SAV, historique de connexion |
| 23 | Dashboards par role | Absent | Un dashboard admin commun existe | Aucun dashboard specialise caissier, magasinier, comptable, manager ou super-admin |
| 24 | Experience utilisateur | Partiel | Interface Bootstrap responsive, POS et tableaux filtres | Tests d'usage, accessibilite, mode clavier POS, etats hors-ligne, retours d'erreurs metier et optimisation mobile a completer |
| 25 | Contexte africain | Partiel | XOF, Mobile Money, especes, mixte, credit, multi-boutiques/entrepots | Mode connexion instable/hors-ligne absent, integration operateurs Mobile Money et impression thermique non finalisees |
| 26 | Architecture fonctionnelle | Partiel | Modules et relations principales presents | MVP/V2 non formalises dans l'application et dependances metier encore incoherentes |
| 27 | Ordre de developpement | Partiel | Socle, catalogue, stock, ventes puis modules secondaires deja crees | Prioriser maintenant securisation des transactions, permissions, paiements/caisse, puis fonctions avancees |

## Risques critiques a corriger avant mise en production

1. Recalculer les prix exclusivement depuis la base et appliquer les remises selon permission.
2. Verifier et verrouiller le stock dans la transaction avant toute vente ; interdire un stock negatif par defaut.
3. Creer un `stock_movement` pour chaque vente, annulation, reception, retour, ajustement et transfert.
4. Empecher la surreception d'une commande et le retour cumule au-dela de la quantite vendue.
5. Rendre annulation et remboursement idempotents et enregistrer leurs paiements/caisse.
6. Remplacer les numeros bases sur `count() + 1` par une sequence resistante a la concurrence.
7. Verifier l'appartenance a l'entreprise pour chaque identifiant valide et chaque modele lie par route.
8. Completer les permissions de chaque role avec des actions distinctes et des politiques Laravel.

## Ordre recommande pour la suite

1. Integrite ventes/stock/prix et tests metier.
2. Integration caisse/paiements/credits clients/dettes fournisseurs.
3. Matrice de permissions et dashboards par role.
4. Numeros de serie, transferts d'entrepot et historique complet des mouvements.
5. Retours, remboursements, echanges et SAV complets.
6. Parametres de recus, thermique/PDF et envoi numerique.
7. Rapports comptables fiables et exports avances.
8. Notifications automatiques et fonctionnement en connexion instable.
