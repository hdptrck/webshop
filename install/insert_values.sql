USE `webShop` ;

-- -----------------------------------------------------
-- webShopUser
-- -----------------------------------------------------

INSERT INTO webShopUser (userToken, firstname, lastname, email, password, role_idRole)
	VALUES
		('f671e9f785c0a655837f07610a5e1a1d3a6ecc2e8f1b8248eaabe2d11109208dfa2295ccec4a02497bf74f5902e76b30f802430046fbb4974c189925bbda16d7d7697eed5df5a8318ac1a3a86a9d0a3ccc4f0e89c3fdb0180294e48a9ba22e8f643868b9bac5493f9f64fa3d154f0a864ac2f4a52f8e298a54931df6e842b9c1',
		'Test',
		'Tester',
		'a@a.a',
		'$2y$10$AXNhJXjrubMu0/exfRQCz.jbKG7EfpR9Y8laPjXbXyJRIoZo25Yf2', 0);

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