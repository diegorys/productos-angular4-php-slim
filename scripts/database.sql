CREATE TABLE productos(
	id int(11) AUTO_INCREMENT NOT null,
	nombre varchar(255),
	descripcion text,
	precio varchar(255),
	imagen varchar(255),
	CONSTRAINT pk_productos PRIMARY KEY(id)
)ENGINE=INNODB
