create database if not exists wearit_2 character set utf8mb4 collate utf8mb4_unicode_ci;
use wearit_2;

-- character set utf8mb4 -Allows the database to store many types of characters, such as Chinese, English, and emojis.
-- collate utf8mb4_unicode_ci -Defines how text is compared and sorted.

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS favorite2;
DROP TABLE IF EXISTS outfit_item2;
DROP TABLE IF EXISTS outfit2;
DROP TABLE IF EXISTS clothing_item2;
DROP TABLE IF EXISTS subcategory2;
DROP TABLE IF EXISTS category2;
DROP TABLE IF EXISTS user2;
SET FOREIGN_KEY_CHECKS = 1;

create table user2(
id int auto_increment primary key,
name varchar(255) not null,
email varchar(150) not null unique,
password_hash varchar(255) not null,
role ENUM('admin', 'user', 'guest') not null default 'user',
created_at timestamp default current_timestamp
);

create table category2 (
id int auto_increment primary key,
name varchar(50) not null unique
);

create table clothing_item2(
id int auto_increment primary key,
user2_id int not null,
category2_id int not null,
name varchar(255) not null,
color varchar(50),
material varchar(50),
pattern varchar(50),
image_path varchar(255),
created_at timestamp default current_timestamp,
foreign key (user2_id) references user2(id) on delete cascade,
foreign key (category2_id) references category2(id)
);

create table outfit2 (
id int auto_increment primary key,
user2_id int not null,
is_public tinyint(1) not null default 0,
created_at timestamp default current_timestamp,
foreign key (user2_id) references user2(id) on delete cascade
);

create table outfit_item2(
id int auto_increment primary key,
outfit2_id int not null,
clothing_item2_id int not null,
foreign key (outfit2_id) references outfit2(id) on delete cascade,
foreign key (clothing_item2_id) references clothing_item2(id) on delete cascade
);

create table favorite2(
id int auto_increment primary key,
user2_id int not null,
outfit2_id int not null,
foreign key (user2_id) references user2(id) on delete cascade,
foreign key (outfit2_id) references outfit2(id) on delete cascade
);

insert into category2 (name) values ('Top'), ('Bottom'), ('Shoes'), ('Outerwear');

INSERT INTO user2 (name, email, password_hash, role) VALUES
('Admin', 'admin@wearit.com', '$2b$12$jLWSksHyXWj.DtjaZLGdT.TtenudlyArAjhRp3v9W1v38c7Q6ZExi', 'admin');