USE `webShop` ;

-- -----------------------------------------------------
-- webShopUser
-- -----------------------------------------------------

INSERT INTO webShopUser (userToken, firstname, lastname, email, password, role_idRole)
	VALUES
		('f671e9f785c0a655837f07610a5e1a1d3a6ecc2e8f1b8248eaabe2d11109208dfa2295ccec4a02497bf74f5902e76b30f802430046fbb4974c189925bbda16d7d7697eed5df5a8318ac1a3a86a9d0a3ccc4f0e89c3fdb0180294e48a9ba22e8f643868b9bac5493f9f64fa3d154f0a864ac2f4a52f8e298a54931df6e842b9c1',
		'Root',
		'Tester',
		'a@a.a',
		'$2y$10$AXNhJXjrubMu0/exfRQCz.jbKG7EfpR9Y8laPjXbXyJRIoZo25Yf2', 2),
		('397b85caada355d5d877a96230c38e2c5781215ec5bf22dd2a06156a47e367c091e1c8f22a85417b21daaea4213e80af60d4bed04a5dba762193be25d9920d1e4b31cbb14412381f3847613e2720fe13f800d4f2b4ab44887c54691fe847a2f531e45a920998a5667626bc95465a2a7ccdc3e0ce7e44f945050785c19080f98e',
		'Administrator',
		'Tester',
		'b@b.b',
		'$2y$10$W9bkMf0YvPEFzwkIiPvrC.vsve7J5HXlYR8AroVNiJ0UWb3KG00mu', 1),
		('95c46a995ccea565284c1b7784f1d11fd18ed5fb54a3cf4db319a8610222d0fa97047ebfb1533428eeb3bb964e8c89cd5dda8e4730279d36726fa2719563340e17f99fb41ac4e703e184f1a92f9ff61a0099b051ed9103b5a75cd1b7f0d5c7c528e24a7bbb3eeefea9dcad9e61ec6735329de38608852b503b718c31daf48f86',
		'Default',
		'Tester',
		'c@c.c',
		'$2y$10$jhzjKBF00SIKC2AJimtjk.aWG7BsjuATrbQcwj9zdpwTuzbBURCK6', 0);

-- -----------------------------------------------------
-- Products
-- -----------------------------------------------------

INSERT INTO item(`count`, `title`, `description`)
	VALUES 
	   (5, 'Lorem Ipsum 1', 'Lorem ipsum dolor sit amet'),
	   (11, 'Lorem Ipsum 2', 'Lorem ipsum dolor sit amet'),
	   (2, 'Lorem Ipsum 3', 'Lorem ipsum dolor sit amet'),
	   (28, 'Lorem Ipsum 4', 'Lorem ipsum dolor sit amet'),
	   (121, 'Lorem Ipsum 5', 'Lorem ipsum dolor sit amet');

-- -----------------------------------------------------
-- orderLocation
-- -----------------------------------------------------

INSERT INTO orderLocation (name) VALUES ('Test');

-- -----------------------------------------------------
-- order
-- -----------------------------------------------------

INSERT INTO tbl_order (webShopUser_idWebShopUser, eventName, eventPlace, pickUpDatetime, returnDatetime, orderLocation_idOrderLocation, isReady, isReturned)
	VALUES
		(1, 'TestAnlass', 'Baseloderso', '2020-12-07 12:00', '2020-12-08 12:00', 1, FALSE, FALSE);

-- -----------------------------------------------------
-- order_has_item
-- -----------------------------------------------------

INSERT INTO order_has_item VALUES (1, 1, 3);