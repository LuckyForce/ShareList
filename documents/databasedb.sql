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
create table sl_a_access(
     a_l_id varchar(255) not null,
     a_u_id int not null,
     a_write tinyint,
     primary key(a_l_id,a_u_id)
);

-- table l_list
create table sl_l_list(
     l_id varchar(255) not null,
     l_created datetime not null,
     l_name varchar(100) not null,
     l_description varchar(255) not null,
     l_u_id int not null,
     primary key(l_id)
);

-- table l_listitem
create table sl_i_item(
     i_id varchar(255) not null,
     i_l_id varchar(255) not null,
     i_lastupdated datetime not null,
     i_content varchar(255) not null,
     i_checked tinyint default false,
     primary key(i_id,i_l_id)
);

-- table t_token
create table sl_t_token(
     t_u_id int not null,
     t_token varchar(255) not null,
     t_expires datetime not null,
     primary key (t_token)
);

-- table u_user
create table sl_u_user(
     u_id int primary key AUTO_INCREMENT,
     u_email varchar(100) not null,
     u_password varchar(255) not null,
     u_lastlogin datetime,
     u_verified tinyint default 0,
     u_verifytoken varchar(255),
     u_resetpwd varchar(100),
     u_resetpwdexpirationdate datetime
);

-- table in_invites
create table sl_in_invites(
     i_id varchar(255) not null,
     i_l_id varchar(255) not null,
     i_u_id int not null,
     i_invitedby varchar(255) not null,
     i_created datetime not null,
     i_accepted tinyint default 0,
     i_accepteddate datetime,
     i_deleted tinyint default 0,
     i_p_id int not null,
     primary key(i_id)
);

-- foreign keys-#################################################
alter table sl_a_access
add foreign key (a_p_id) references sl_p_priviledge(p_id) on delete restrict on update restrict,
add foreign key (a_l_id) references sl_l_list(l_id) on delete restrict on update restrict,
add foreign key (a_u_id) references sl_u_user(u_id) on delete restrict on update restrict;
alter table sl_i_listitem
add foreign key (i_l_id) references sl_l_list(l_id) on delete cascade on update restrict;
alter table sl_t_token
add foreign key (t_u_id) references sl_u_user(u_id) on delete cascade on update restrict;
alter table sl_l_list
add foreign key (l_u_id) references sl_u_user(u_id) on delete restrict on update restrict;
alter table sl_i_invites
add foreign key (i_l_id) references sl_l_list(l_id) on delete cascade on update restrict,
add foreign key (i_u_id) references sl_u_user(u_id) on delete cascade on update restrict,
add foreign key (i_invitedby) references sl_u_user(u_id) on delete cascade on update restrict,
add foreign key (i_p_id) references sl_p_priviledge(p_id) on delete restrict on update restrict;
-- #######################################################
-- commit all changes
-- commit;