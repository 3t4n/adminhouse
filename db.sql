
CREATE TABLE IF NOT EXISTS Modules
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	route  varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Roles
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Rol_Modules
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	rol_id mediumint(255) NOT NULL,
	module_id mediumint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(rol_id) REFERENCES Roles(id),
	FOREIGN KEY(module_id) REFERENCES Modules(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Users
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	boss_id mediumint(255),	
	rol_id mediumint(255) NOT NULL,	
	email varchar(255) NOT NULL,
	name varchar(255) NOT NULL,
	lastname varchar(255) NOT NULL,
	nick varchar(255) NOT NULL,
	pass varchar(255) NOT NULL,
	airbnb varchar(255),
	airpass varchar(255),
	PRIMARY KEY(id),
	FOREIGN KEY(rol_id) REFERENCES Roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Currencies(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	abbreviation varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Houses
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	airbnb_listing_id mediumint(255),
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Earnings 
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	id_house mediumint(255) NOT NULL,
	earnings double(10,2) NOT NULL,
	earning_date date NOT NULL,
	currency_id mediumint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(currency_id) REFERENCES Currencies(id),
	FOREIGN KEY(id_house) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS PaymentMethods
(	
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)	
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS AirBNBres
(
	earning_id mediumint(255) NOT NULL,
	thread_id mediumint(255) NOT NULL,
	start_date date NOT NULL,
	end_date date NOT NULL,
	booked_date date NOT NULL,
	nights mediumint(255) NOT NULL,
	confirmation_code varchar(255) NOT NULL,
	PRIMARY KEY(thread_id),
	FOREIGN KEY(earning_id) REFERENCES Earnings(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS Rents
(
	earning_id mediumint(255) NOT NULL,
	house_id mediumint(255) NOT NULL,
	start_date date NOT NULL,
	end_date date NOT NULL,	
	FOREIGN KEY(earning_id) REFERENCES Earnings(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Products
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)	
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS OutGoings
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,	
	house_id mediumint(255) NOT NULL,
	start_date date NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS ProductsOutGoings
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	product_id mediumint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(product_id) REFERENCES Products(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS Incidents
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	house_id mediumint(255) NOT NULL,
	description TEXT NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS AttachmentTypes
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Attachments
(
	id mediumint(255) NOT NULL AUTO_INCREMENT,
	object_id mediumint(255) NOT NULL,
	type_id mediumint(255) NOT NULL,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(type_id) REFERENCES AttachmentTypes(id)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS getHouseID;
DELIMITER $$
CREATE PROCEDURE getHouseID(IN listing_name varchar(255), IN listing_id mediumint(255),OUT i mediumint(255))
BEGIN
	SET @RES = (SELECT id FROM Houses WHERE Houses.airbnb_listing_id=listing_id);
	IF(FOUND_ROWS()>0) THEN
		SELECT @RES INTO i;
		UPDATE Houses SET name=listing_name;
	ELSE
		INSERT INTO Houses (name,airbnb_listing_id) VALUES(listing_name,listing_id);
		SELECT LAST_INSERT_ID() INTO i;
	END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS getAirBNBEarningID;
DELIMITER $$
CREATE PROCEDURE getAirBNBEarningID(IN thread_id mediumint(255), IN start_date DATE,IN end_date DATE, IN booked_date DATE, IN nights mediumint(255), IN confirmation_code varchar(255), IN listing_name varchar(255),IN listing_id mediumint(255),IN earnings double(10,2), IN currency_id mediumint(255),OUT id mediumint(255))
BEGIN
	
	SET @RES = (SELECT confirmation_code FROM AirBNBres WHERE AirBNBres.confirmation_code=confirmation_code);
	IF(FOUND_ROWS()>0) THEN
		SELECT @RES INTO id;
	ELSE
		CALL getHouseID(listing_name, listing_id, @house_id); 
		INSERT INTO Earnings (house_id,earnings,currency_id) VALUES(@house_id,earnings,currency_id);
		SET @earn = (SELECT LAST_INSERT_ID());
		INSERT INTO AirBNBres (earning_id,thread_id,start_date,end_date,booked_date,nights,confirmation_code) VALUES(@earn,thread_id,start_date,end_date,booked_date,nights,confirmation_code);
		SELECT LAST_INSERT_ID() INTO id;	
	END IF;
END$$
DELIMITER ;


