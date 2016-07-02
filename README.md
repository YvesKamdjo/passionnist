Hairlov.fr - Coiffeurs Privés
=============================

Dépendences et librairies frontend
----------------------------------

Les dépendences frontend sont gérées via NPM :

* [jQuery](https://github.com/jquery/jquery)
* [Bootstrap](https://github.com/twbs/bootstrap)
* [Gulp](https://github.com/gulpjs/gulp)
* [LESS](https://github.com/less/less.js)
* [PostCSS](https://github.com/postcss/postcss) ([Autoprefixer](https://github.com/postcss/autoprefixer), [css-mqpacker](https://github.com/hail2u/node-css-mqpacker))

Tâches Gulp
-----------

### default

Tâche par défaut, lancée via la commande `gulp` seule.  
Exécute tous les traitements nécessaires pour générés l'intégralité des fichiers
CSS et JS requis par l'application.

### watch

Surveille les modifications sur les fichiers LESS et JS de l'application, et lance les tâches associées dès qu'une modification est détectée.  
**Note** : Gulp ne prendra pas en compte les fichiers nouvellement créés lorsque
le watcher est en cours d'utilisation.  
Cela signifie qu'il faudra relancer la tâche Gulp lors de l'ajout
de nouveaux fichiers.

### vendor-css

Traitements sur les fichiers CSS externes ("vendor"). Compresse ([clean-css](https://github.com/jakubpawlowicz/clean-css)) et concatène
tous les fichiers dans une seule sortie : `/public/css/vendor.css`.

### vendor-js

Traitements sur les fichiers JS externes ("vendor"). Compresse ([uglify](https://github.com/mishoo/UglifyJS)) et concatène
tous les fichiers dans une seule sortie : `/public/css/vendor.js`.

### Tâches JS et LESS pour chaque module

Pour chaque module, nous avons une tâche dédiée aux fichiers LESS et une autre aux fichiers JS.  
Le nommage est uniformisé selon le modèle suivant : `[nom_du_module]-less` et `[nom_du_module]-js`.  
Exemple pour le module `Application` : `application-less` et `application-js`.

Concernant les watcher, nous en avons 3 pour chaque module, découpés comme suit :

* `[nom_du_module]-watch-less` : surveille uniquement les fichiers LESS du module
* `[nom_du_module]-watch-js` : surveille uniquement les fichiers JS du module
* `[nom_du_module]-watch` : lance les deux watchers LESS et JS en une seule commande

#### Explication des tâches LESS

Ces tâches génèrent les fichiers CSS finaux à partir des sources LESS présentes dans le
dossier du module, comme par exemple `/module/Application/assets/less/`.  
Elles appliquent diverses règles PostCSS. Notamment :

* Autoprefixer : ajout automatique des "vendor prefixes" pour la compatibilité
avec les anciens navigateurs
* css-mqpacker : regroupe toutes les media-queries à la fin d'un fichier, avec
possibilité de les trier (tris effectué sur la propriété `min-width`)

Le résultat est concaténé et compréssé ([clean-css](https://github.com/jakubpawlowicz/clean-css)) dans un seul fichier en sortie :
`/public/css/hairlov-[nom_du_module].css`.

#### Explication des tâches JS

Ces tâches concatènent tous les fichiers JS présents dans le dossier du module (`/module/[nom_du_module]/assets/less/`), puis utilisent
[uglify](https://github.com/mishoo/UglifyJS) pour compresser le contenu.  
Elles créent un unique fichier en sortie: `/public/js/hairlov-[nom_du_module].js`.

#### Exceptions des fichiers globaux

On applique le fonctionnement des tâches par module pour les fichiers globaux, en utilisant le nommage
`global-less`, `global-js`, `global-watch`, etc.  
Cela s'applique aux fichiers LESS et JS nécéssaires à l'application, peu importe le module utilisé.

Les fichiers en sortie sont respectivement `hairlov.css` et `hairlov.js`, on n'ajoute pas le nom du module.