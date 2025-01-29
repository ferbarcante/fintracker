CREATE TABLE tipo_conta (
	id_tipoconta SERIAL PRIMARY KEY,
	nm_tipoconta VARCHAR NOT NULL
)

CREATE TABLE conta_bancaria (
	id_conta SERIAL PRIMARY KEY,
	nm_conta VARCHAR NOT NULL,
	vl_saldo VARCHAR NOT NULL, 
	id_tipoconta INT NOT NULL,
	CONSTRAINT fk_tipoconta FOREIGN KEY (id_tipoconta) REFERENCES tipo_conta(id_tipoconta)
)

CREATE TABLE evento_calendario (
	id_eventocalendario SERIAL PRIMARY KEY,
	nu_tempoinicio VARCHAR NOT NULL,
	nu_tempofim VARCHAR NOT NULL,
	nm_titulo VARCHAR NOT NULL,
	nm_cor VARCHAR NOT NULL,
	ds_descricao VARCHAR NOT NULL
)

drop table evento_calendario