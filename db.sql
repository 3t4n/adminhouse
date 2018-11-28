
CREATE TABLE IF NOT EXISTS Modules
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	route  varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Roles
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Rol_Modules
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	rol_id bigint(255) NOT NULL,
	module_id bigint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(rol_id) REFERENCES Roles(id),
	FOREIGN KEY(module_id) REFERENCES Modules(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Users
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	boss_id bigint(255),	
	rol_id bigint(255) NOT NULL,	
	email varchar(255) NOT NULL,
	name varchar(255) NOT NULL,
	lastname varchar(255) NOT NULL,
	nick varchar(255) NOT NULL,
	pass varchar(255) NOT NULL,
	token varchar(255) NOT NULL,
	airbnb varchar(255),
	airpass varchar(255),
	PRIMARY KEY(id),
	FOREIGN KEY(rol_id) REFERENCES Roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Currencies(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	abbreviation varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Houses
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	airbnb_listing_id bigint(255),
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS HousesUsers(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	house_id bigint(255) NOT NULL, 
	user_id bigint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(user_id) REFERENCES Users(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Earnings 
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	house_id bigint(255) NOT NULL,
	earnings double(10,2) NOT NULL,
	earning_date date NOT NULL,
	currency_id bigint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(currency_id) REFERENCES Currencies(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS PaymentMethods
(	
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)	
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS AirBNBres
(
	earning_id bigint(255) NOT NULL,
	thread_id bigint(255) NOT NULL,
	host_review bigint(255),
	number_of_adults bigint(255) DEFAULT 0,
	number_of_children bigint(255) DEFAULT 0,
	number_of_infants bigint(255) DEFAULT 0,
	start_date date NOT NULL,
	end_date date NOT NULL,
	booked_date date NOT NULL,
	nights bigint(255) NOT NULL,
	confirmation_code varchar(255) NOT NULL,
	PRIMARY KEY(thread_id,earning_id),
	FOREIGN KEY(earning_id) REFERENCES Earnings(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS Rents
(
	earning_id bigint(255) NOT NULL,
	house_id bigint(255) NOT NULL,
	start_date date NOT NULL,
	end_date date NOT NULL,	
	FOREIGN KEY(earning_id) REFERENCES Earnings(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Products
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)	
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS OutGoings
(
	id bigint(255) NOT NULL AUTO_INCREMENT,	
	house_id bigint(255) NOT NULL,
	ddate date NOT NULL,
	outgoing double(10,2) NOT NULL,
	concept TEXT NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS ProductsOutGoings
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	product_id bigint(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(product_id) REFERENCES Products(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS Incidents
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	house_id bigint(255) NOT NULL,
	description TEXT NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(house_id) REFERENCES Houses(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS AttachmentTypes
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Attachments
(
	id bigint(255) NOT NULL AUTO_INCREMENT,
	object_id bigint(255) NOT NULL,
	type_id bigint(255) NOT NULL,
	name varchar(255) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY(type_id) REFERENCES AttachmentTypes(id)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP PROCEDURE IF EXISTS getHouseID;
DELIMITER $$

CREATE PROCEDURE getHouseID(IN listing_name varchar(255), IN listing_id bigint(255),OUT i bigint(255))
BEGIN
	SET @RES = (SELECT id FROM Houses WHERE Houses.airbnb_listing_id=listing_id);
	IF(FOUND_ROWS()>0) THEN
		SELECT @RES INTO i;
		UPDATE Houses SET name=listing_name WHERE id=@RES;
	ELSE
		INSERT INTO Houses (name,airbnb_listing_id) VALUES(listing_name,listing_id);
		SELECT LAST_INSERT_ID() INTO i;
	END IF;
END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS getAirBNBEarningID;
DELIMITER $$
CREATE PROCEDURE getAirBNBEarningID(IN house_id bigint(255), IN confirmation_code varchar(255),IN booked_date DATE, IN earnings double(10,2),IN end_date DATE, IN number_of_adults bigint(255), IN number_of_children bigint(255), IN number_of_infants bigint(255), IN listing_id bigint(255), IN listing_name VARCHAR(255), IN nights bigint(255),IN start_date DATE ,IN thread_id bigint(255), OUT id bigint(255))
BEGIN
	
	SET @RES = (SELECT confirmation_code FROM AirBNBres WHERE AirBNBres.confirmation_code=confirmation_code);
	IF(FOUND_ROWS()>0) THEN
		SELECT @RES INTO id;
	ELSE
		INSERT INTO Earnings (house_id,earnings,currency_id, earning_date) VALUES(house_id,earnings,1,start_date);
		SET @earn = (SELECT LAST_INSERT_ID());
		INSERT INTO AirBNBres (earning_id,thread_id,number_of_adults,number_of_children,number_of_infants,start_date,end_date,booked_date,nights,confirmation_code) VALUES(@earn,thread_id,number_of_adults,number_of_children,number_of_infants,start_date,end_date,booked_date,nights,confirmation_code);
		SELECT LAST_INSERT_ID() INTO id;	
	END IF;
END$$
DELIMITER ;


