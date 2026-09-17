
# 🛒 E-commerce - Gestion de Stock et de Vente

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="350" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Plateforme e-commerce et gestion commerciale</strong>
</p>

<p align="center">
  Gestion des stocks, création de produits, vente directe en magasin et vente en ligne.
</p>

---

## 📌 À propos du projet

Cette application e-commerce est une plateforme de gestion de stock et de vente développée avec **Laravel et Vue.js**.

Elle permet de centraliser la gestion des stocks, des produits, des ventes et des commandes dans une seule application.

Le projet propose deux interfaces principales :

- **Interface administrateur :** développée avec Laravel pour gérer les stocks, les produits, les ventes et les commandes.
- **Interface client :** développée avec Vue.js pour permettre aux clients de consulter les produits et de passer des commandes en ligne.

L'objectif est de faciliter la gestion commerciale en combinant les ventes physiques en magasin et les ventes en ligne.

> 🚧 Projet en cours de développement

---

## 🎯 Objectifs du projet

- Centraliser la gestion des stocks et des ventes.
- Faciliter la création et la gestion des produits.
- Permettre la vente directe au magasin.
- Permettre la vente indirecte via le site e-commerce.
- Gérer les commandes des clients.
- Améliorer le suivi des activités commerciales.

---

## 🛠️ Technologies utilisées

| Technologie | Utilisation |
|---|---|
| Laravel | Backend et interface administrateur |
| Vue.js | Interface client et interactions frontend |
| PHP | Logique serveur |
| MySQL | Gestion de la base de données |
| HTML / CSS | Structure et mise en forme |
| JavaScript | Interactivité |
| Git / GitHub | Versionnement du projet |

---

## ⚙️ Fonctionnalités principales

### 👨‍💼 1. Interface administrateur (Laravel)

L'interface administrateur permet de gérer l'ensemble des activités commerciales.

- Gestion des stocks.
- Insertion des stocks.
- Création et gestion des produits.
- Gestion des catégories et des unités.
- Gestion des ventes directes en magasin.
- Gestion des commandes en ligne.
- Suivi des ventes et des activités.
- Gestion des utilisateurs selon les fonctionnalités disponibles.

### 📦 2. Gestion des stocks

- Ajouter et gérer les stocks.
- Suivre les quantités disponibles.
- Gérer les unités de mesure.
- Organiser les catégories.
- Utiliser les stocks pour la création de produits.
- Mettre à jour les quantités lors des ventes.

### 🛍️ 3. Création et gestion des produits

- Créer des produits.
- Enregistrer les informations des produits.
- Gérer les prix d'achat et de vente.
- Associer les produits aux stocks.
- Gérer les produits finis selon les besoins de l'application.

### 🧾 4. Vente directe en magasin

Cette fonctionnalité permet d'enregistrer les ventes réalisées directement dans le magasin.

- Sélectionner les produits à vendre.
- Choisir les quantités et les unités disponibles.
- Calculer les montants.
- Enregistrer les ventes.
- Mettre à jour les stocks.
- Gérer les modes de paiement disponibles.

### 🌐 5. Vente en ligne (Vue.js)

L'interface client permet aux utilisateurs de consulter les produits et de passer des commandes sur le site e-commerce.

- Consulter les produits.
- Consulter les informations et les prix.
- Ajouter des produits au panier selon les fonctionnalités disponibles.
- Passer une commande.
- Enregistrer les informations nécessaires à la commande.
- Consulter le suivi des commandes selon les fonctionnalités disponibles.

### 📋 6. Gestion des commandes

La gestion des commandes permet à l'administrateur de traiter les demandes effectuées par les clients.

- Enregistrer les commandes.
- Consulter les commandes clients.
- Suivre les statuts des commandes.
- Traiter les commandes.
- Organiser le processus de vente en ligne.

---

## 🖥️ Captures d'écran

### 👨‍💼 Interface administrateur - Laravel

Les captures suivantes présentent l'interface de gestion administrateur.

### 1. Tableau de bord administrateur

Le tableau de bord permet de consulter les informations principales de l'activité commerciale.

![Tableau de bord administrateur](images/admindashboard.png)

### 2. Gestion des stocks

Cette interface permet de gérer les stocks et les informations associées.

![Gestion des stocks](images/stocks.png)

### 3. Vente directe en magasin

Cette interface permet d'enregistrer les ventes réalisées directement au magasin.

![Vente directe en magasin](images/vente-direct.png)

---

## 🏗️ Architecture de l'application

### Backend - Laravel

Le backend Laravel assure :

- La gestion des données.
- La logique métier.
- La gestion des stocks.
- La gestion des produits.
- La gestion des ventes.
- La gestion des commandes.
- La communication avec la base de données MySQL.

### Frontend - Vue.js

L'interface client Vue.js assure :

- L'affichage des produits.
- Les interactions avec les utilisateurs.
- La consultation des informations.
- Le parcours de commande en ligne.
- La communication avec le backend selon l'architecture de l'application.

---

## 🚀 Installation du projet

### 1. Cloner le dépôt

```bash
git clone https://github.com/DenisTsilavina/test-projet.git
```

### 2. Accéder au projet

```bash
cd test-projet
```

### 3. Installer les dépendances PHP

```bash
composer install
```

### 4. Installer les dépendances JavaScript

```bash
npm install
```

### 5. Configurer l'environnement

Copier le fichier `.env.example` vers `.env`.

Sous Windows PowerShell :

```powershell
Copy-Item .env.example .env
```

Sous Linux :

```bash
cp .env.example .env
```

### 6. Générer la clé de l'application

```bash
php artisan key:generate
```

### 7. Configurer la base de données

Modifier le fichier `.env` et renseigner les informations de connexion à MySQL.

Exemple :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_la_base
DB_USERNAME=root
DB_PASSWORD=
```

Adaptez les valeurs à votre environnement local.

### 8. Exécuter les migrations

```bash
php artisan migrate
```

Si des seeders sont disponibles et nécessaires :

```bash
php artisan db:seed
```

### 9. Compiler les ressources frontend

Pour le développement :

```bash
npm run dev
```

Pour la production :

```bash
npm run build
```

### 10. Lancer l'application

Dans un terminal :

```bash
php artisan serve
```

L'application sera accessible à l'adresse :

```text
http://127.0.0.1:8000
```

---

## 🔐 Configuration

Avant de lancer l'application, vérifier :

- La configuration de la base de données.
- Les dépendances PHP et JavaScript.
- La configuration de l'environnement `.env`.
- Les migrations nécessaires.
- Les permissions et accès utilisateurs selon les fonctionnalités.

---

## 📁 Structure générale du projet

```text
test-projet/
├── app/
│   ├── Http/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── js/
│   ├── css/
│   └── views/
├── routes/
├── images/
│   ├── admindashboard.png
│   ├── stocks.png
│   └── vente-direct.png
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## 🔄 Processus de vente

### Vente directe en magasin

```text
Insertion du stock
       ↓
Création du produit
       ↓
Sélection du produit
       ↓
Vente directe
       ↓
Enregistrement de la vente
       ↓
Mise à jour du stock
```

### Vente en ligne

```text
Consultation des produits
       ↓
Sélection des produits
       ↓
Création de la commande
       ↓
Traitement par l'administrateur
       ↓
Suivi de la commande
```

---

## 📌 État du projet

Le projet est en cours de développement.

Les fonctionnalités et les interfaces sont progressivement améliorées afin de proposer une solution complète de gestion de stock et de vente en magasin et en ligne.

---

## 👨‍💻 Auteur

**Jocyen Tsilavina Denis**

- GitHub : [DenisTsilavina](https://github.com/DenisTsilavina)
- LinkedIn : [Jocyen Tsilavina Denis](https://www.linkedin.com/in/jocyen-tsilavina-denis-536b74309/)

---

## 📄 Licence

Ce projet est développé à des fins d'apprentissage et de développement personnel.

La licence peut être définie selon les besoins du projet.
