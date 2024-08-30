-- DB作成
CREATE DATABASE localtestdb;

-- 使用スキーマの設定
USE localtestdb;

-- 都道府県マスタの作成
CREATE TABLE prefecture_mst (
	prefecture_id TINYINT NOT NULL PRIMARY KEY comment '都道府県ID',
    prefecture_name VARCHAR(8) NOT NULL comment '都道府県名')
default charset=utf8
comment='都道府県マスタ';

-- 職種マスタの作成
CREATE TABLE job_mst (
	job_id TINYINT NOT NULL PRIMARY KEY comment '職種ID',
    job_name VARCHAR(20) NOT NULL comment '職種名')
default charset=utf8
comment='職種マスタ';

-- 学歴マスタの作成
CREATE TABLE education_mst (
	education_id TINYINT NOT NULL PRIMARY KEY comment '学歴ID',
    education_name VARCHAR(20) NOT NULL comment '学歴名')
default charset=utf8
comment='学歴マスタ';

-- 都道府県マスタのデータ挿入
INSERT INTO `prefecture_mst` (prefecture_id, prefecture_name) VALUES
  (1,'北海道'),
  (2,'青森県'),
  (3,'岩手県'),
  (4,'宮城県'),
  (5,'秋田県'),
  (6,'山形県'),
  (7,'福島県'),
  (8,'茨城県'),
  (9,'栃木県'),
  (10,'群馬県'),
  (11,'埼玉県'),
  (12,'千葉県'),
  (13,'東京都'),
  (14,'神奈川県'),
  (15,'新潟県'),
  (16,'富山県'),
  (17,'石川県'),
  (18,'福井県'),
  (19,'山梨県'),
  (20,'長野県'),
  (21,'岐阜県'),
  (22,'静岡県'),
  (23,'愛知県'),
  (24,'三重県'),
  (25,'滋賀県'),
  (26,'京都府'),
  (27,'大阪府'),
  (28,'兵庫県'),
  (29,'奈良県'),
  (30,'和歌山県'),
  (31,'鳥取県'),
  (32,'島根県'),
  (33,'岡山県'),
  (34,'広島県'),
  (35,'山口県'),
  (36,'徳島県'),
  (37,'香川県'),
  (38,'愛媛県'),
  (39,'高知県'),
  (40,'福岡県'),
  (41,'佐賀県'),
  (42,'長崎県'),
  (43,'熊本県'),
  (44,'大分県'),
  (45,'宮崎県'),
  (46,'鹿児島県'),
  (47,'沖縄県');
  
-- 職種マスタのデータ挿入
INSERT INTO `job_mst` (job_id, job_name) VALUES
  (1,'総合職'),
  (2,'事務職'),
  (3,'技術職');
  
-- 学歴マスタのデータ挿入
INSERT INTO `education_mst` (education_id, education_name) VALUES
  (1,'大卒'),
  (2,'院卒'),
  (3,'高卒');
  
-- 応募情報管理の作成
CREATE TABLE oubo_info (
	oubo_id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT comment '応募ID',
    username VARCHAR(60) NOT NULL comment '名前',
    username_kana VARCHAR(60) NOT NULL comment 'フリガナ',
    gender ENUM('man','woman') NOT NULL comment '性別',
    birthday DATE NOT NULL comment '生年月日',
    education VARCHAR(20) NOT NULL comment '最終学歴',
    postcode MEDIUMINT NOT NULL comment '郵便番号',
    prefecture VARCHAR(8) NOT NULL comment '都道府県ID',
    city VARCHAR(100) NOT NULL comment '市区町村',
    email VARCHAR(100) NOT NULL comment 'メールアドレス',
    job VARCHAR(60) NOT NULL comment '職種ID',
    userfile TEXT(2000) NOT NULL comment '履歴書',
    etc VARCHAR(2000) comment 'その他要望など'
    )
default charset=utf8
comment='応募情報管理';