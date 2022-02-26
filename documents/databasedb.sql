-- #######################################################
-- auto generated ddl-script ###########################
-- generated sql creation script for ER model
-- database-#############################################
-- drop database if exists databasedb;
-- create database databasedb;
-- use databasedb;
-- switch autocommit off
-- set autocommit=0;
-- tables-#################################################
-- table h_hasaccess
create table sl_h_hasaccess(
     h_l_id varchar(100) not null,
     h_u_id varchar(100) not null,
     h_p_id varchar(100) not null,
     h_priviledge varchar(100) not null,
     primary key(h_l_id,h_u_id,h_p_id)
);

-- table l_list
create table sl_l_list(
     l_id varchar(100) not null,
     l_createdat varchar(100) not null,
     l_title varchar(100) not null,
     primary key(l_id)
);

-- table l_listitem
create table sl_li_listitem(
     li_id int AUTO_INCREMENT,
     li_l_id varchar(100) not null,
     li_lastupdated varchar(100) not null,
     li_content varchar(100) not null,
     primary key(li_id,li_l_id)
);

-- table p_priviledge
create table sl_p_priviledge(
     p_id int not null,
     p_description varchar(100) not null,
     primary key(p_id)
);

-- table t_token
create table sl_t_token(
     t_id int primary key AUTO_INCREMENT,
     t_u_id varchar(100) not null,
     t_token varchar(100) not null,
     t_expiration datetime not null,
);

-- table u_user
create table sl_u_user(
     u_id int primary key AUTO_INCREMENT,
     u_email varchar(100) not null,
     u_password varchar(255) not null,
     u_emailverified tinyint default 0,
     u_emailcode varchar(100),
     u_renewpwd varchar(100),
     u_renewpwdexpirationdate datetimel,
);


-- foreign keys-#################################################
alter table sl_h_hasaccess
add foreign key (h_p_id) references sl_p_priviledge(p_id) on delete restrict on update restrict,
add foreign key (h_l_id) references sl_l_list(l_id) on delete restrict on update restrict,
add foreign key (h_u_id) references sl_u_user(u_id) on delete restrict on update restrict;
alter table sl_li_listitem
add foreign key (li_l_id) references sl_l_list(l_id) on delete restrict on update restrict;
alter table sl_t_token
add foreign key (t_u_id) references sl_u_user(u_id) on delete restrict on update restrict;
-- commit all changes
-- commit;