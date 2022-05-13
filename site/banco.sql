
CREATE TABLE CA_PRODUTOS(

    id serial not null,
	
	descricao varchar(400),
	modelo varchar(10),
	
	preco_custo DECIMAL not null,
	preco_venda DECIMAL not null,
	
    cd_barras INT not null,
	cd_referencia INT not null,
      
    unidade VARCHAR(2) not null,
    ncm INT not null
	
	id_marca INT not null

);

/****** CORES *****/

CREATE TABLE CA_CORES(

    id serial not null,
    nome varchar (18) not null,
    cd_hex varchar(7) not null

);

alter table CA_CORES
add constraint pk_cor
primary key(id);


/****** MARCAS *****/

CREATE TABLE CA_MARCAS(

    id serial not null,
    nome varchar (20) not null

);

alter table CA_MARCAS
add constraint pk_marca
primary key(id);

/****** CATEGORIAS *****/

CREATE TABLE CA_CATEGORIAS(

    id serial not null,
    nome varchar (25) not null

);

alter table CA_CATEGORIAS
add constraint pk_categoria
primary key(id);

/****** SUBCATEGORIAS *****/

CREATE TABLE CA_SUBCATEGORIAS(

    id serial not null,
    nome varchar (25) not null,
    id_categoria INT not null

);

alter table CA_SUBCATEGORIAS
add constraint pk_subcategoria
primary key(id);


/****** REL *****/

CREATE TABLE REL_PRODUTO_COR(

    id serial not null,
    id_produto INT not null,
    id_cor INT not null,

);

alter table REL_PRODUTO_COR
add constraint pk_produto_cor
primary key(id);

/****** IMAGENS *****/

CREATE TABLE CA_IMAGENS(

    id serial not null,
    caminho varchar (60) not null, 
    categoria varchar(10) not null,
    id_produto INT not null

);

alter table IMAGENS
add constraint pk_imagem
primary key(id);

/****** CONSULTAS *****/

insert into CA_CORES(nome, cd_hex) values ('Vermelho','#FF0000');
insert into CA_CORES(nome, cd_hex) values ('Vermelho Escuro','#8B0000');
insert into CA_CORES(nome, cd_hex) values ('Vermelho Claro','#d33a3a');

insert into CA_CORES(nome, cd_hex) values ('Azul','#0000FF');
insert into CA_CORES(nome, cd_hex) values ('Azul Escuro','#00008B');
insert into CA_CORES(nome, cd_hex) values ('Azul Claro','#BFEFFF');

insert into CA_CORES(nome, cd_hex) values ('Verde','#00FF00');
insert into CA_CORES(nome, cd_hex) values ('Verde Escuro','#006400');
insert into CA_CORES(nome, cd_hex) values ('Verde Claro','#90EE90');

insert into CA_CORES(nome, cd_hex) values ('Amarelo','#FFFF00');
insert into CA_CORES(nome, cd_hex) values ('Amarelo Queimado','#FFD700');
insert into CA_CORES(nome, cd_hex) values ('Amarelo Claro','#EEE8AA');

insert into CA_CORES(nome, cd_hex) values ('Rosa','#FF69B4');
insert into CA_CORES(nome, cd_hex) values ('Rosa Escuro','#8B0A50');
insert into CA_CORES(nome, cd_hex) values ('Rosa Claro','#FF82AB');

insert into CA_CORES(nome, cd_hex) values ('Marrom','#CD661D');
insert into CA_CORES(nome, cd_hex) values ('Marrom Escuro','#8B4513');
insert into CA_CORES(nome, cd_hex) values ('Marrom Claro','#CD853F');

insert into CA_CORES(nome, cd_hex) values ('Marrom','#CD661D');
insert into CA_CORES(nome, cd_hex) values ('Marrom Escuro','#8B4513');
insert into CA_CORES(nome, cd_hex) values ('Marrom Claro','#CD853F');

insert into CA_CORES(nome, cd_hex) values ('Laranja','#FF7F00');
insert into CA_CORES(nome, cd_hex) values ('Laranja Escuro','#EE7621');
insert into CA_CORES(nome, cd_hex) values ('Laranja Claro','#EE7942');

insert into CA_CORES(nome, cd_hex) values ('Roxo','#A020F0');
insert into CA_CORES(nome, cd_hex) values ('Roxo Escuro','#551A8B');
insert into CA_CORES(nome, cd_hex) values ('Roxo Claro','#AB82FF');

insert into CA_CORES(nome, cd_hex) values ('Cinza','#BEBEBE');
insert into CA_CORES(nome, cd_hex) values ('Cinza Escuro','#696969');
insert into CA_CORES(nome, cd_hex) values ('Cinza Claro','#EEE9E9');

insert into CA_CORES(nome, cd_hex) values ('Branco','#000000');
insert into CA_CORES(nome, cd_hex) values ('Preto','#FFFFFF');

SELECT * FROM CA_CORES

insert into CA_MARCAS(nome) values ('NVIDIA');
insert into CA_MARCAS(nome) values ('AMD Radeon');

insert into CA_MARCAS(nome) values ('Philips');
insert into CA_MARCAS(nome) values ('DELL');

insert into CA_MARCAS(nome) values ('Lacoste');
insert into CA_MARCAS(nome) values ('Hering');

insert into CA_MARCAS(nome) values ('Levis');
insert into CA_MARCAS(nome) values ('Dolce & Gabbana');

insert into CA_MARCAS(nome) values ('Nike');
insert into CA_MARCAS(nome) values ('Adidas');

insert into CA_MARCAS(nome) values ('Levis');
insert into CA_MARCAS(nome) values ('Dolce & Gabbana');

insert into CA_MARCAS(nome) values ('Nike');
insert into CA_MARCAS(nome) values ('Adidas');


insert into CA_MARCAS(nome) values ('Brastemp');
insert into CA_MARCAS(nome) values ('Consul');

SELECT * FROM CA_MARCAS

insert into CA_CATEGORIAS(nome) values ('Informática');
insert into CA_CATEGORIAS(nome) values ('Vestuário');
insert into CA_CATEGORIAS(nome) values ('Eletrodomésticos');

SELECT * FROM CA_CATEGORIAS

insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Placa de Vídeo', 1);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Monitor', 1);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Teclado', 1);

insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Camiseta', 2);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Camisa', 2);

insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Calça', 2);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Bermuda', 2);

insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Sapatênis', 2);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Sapato', 2);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Tênis', 2);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Meia', 2);

insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Geladeira', 3);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Micro-Ondas', 3);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Fogão', 3);
insert into CA_SUBCATEGORIAS(nome, id_categoria) values ('Máquina de Lavar', 3);

SELECT * FROM CA_SUBCATEGORIAS

SELECT * FROM CA_IMAGENS

SELECT * FROM CA_PRODUTOS

/*

update CA_CORES set nome = 'Branco' WHERE id = 28

*/