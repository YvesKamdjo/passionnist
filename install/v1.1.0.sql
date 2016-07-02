INSERT INTO AccountType (
	idAccountType,
    `key`
)
VALUES (
	1,
    'Employé'
), (
	2,
    'Gérant'
), (
	3,
    'Indépendant'
), (
	4,
    'Client'
), (
	5,
    'Administrateur'
);

INSERT INTO Referral (
	idReferral,
    label
)
VALUES (
	1,
    'Publicité'
), (
	2,
    'Newsletter'
), (
	3,
    'Téléphone'
), (
	4,
    'Commercial'
), (
	5,
    'Connaissance'
), (
	6,
    'Autre'
);

INSERT INTO CustomerCharacteristic (
	idCustomerCharacteristic,
    name
)
VALUES (
	1,
    'Courts'
), (
	2,
    'Mi-longs'
), (
	3,
    'Longs'
);

INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (1, "Shampooing & Coiffage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (2, "Shampooing & Coiffage & Coupe");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (3, "Shampooing & Coiffage & Coloration");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (4, "Coupe homme");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (5, "Coupe enfant");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (6, "Coiffage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (7, "Coupe enfant");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (8, "Shampooing");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (9, "Soin");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (10, "Coloration");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (11, "Balayage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (12, "Brushing");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (13, "Lissage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (14, "Soin capillaire");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (15, "Lissage brésilien");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (16, "Défrisage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (17, "Permanente");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (18, "Chignon");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (19, "Extension afro");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (20, "Barbier");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (21, "Coiffure de mariage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (22, "Tresse afro");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (23, "Mèche");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (24, "Tissage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (25, "Moins de 25 ans");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (26, "Etudiant");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (27, "Forfait mariage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (28, "Mariage");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (29, "Lissage japonais");
INSERT IGNORE INTO JobServiceType (idJobServiceType, name) VALUES (30, "Lissage français");

