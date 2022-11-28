--
-- PostgreSQL database cluster dump
--

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;



CREATE OR REPLACE function public.schema_bulk_drop()
    RETURNS boolean
    LANGUAGE plpgsql
AS $body$
DECLARE
    cur1 CURSOR FOR
        SELECT nspname FROM pg_namespace where nspname NOT LIKE '%pg_%' and nspname NOT IN ('public','information_schema','version');
    nsname_rec RECORD;
    dropsql text;
BEGIN
    OPEN cur1;
    LOOP
        FETCH cur1 INTO nsname_rec;
        EXIT WHEN NOT FOUND;
        RAISE INFO '%',nsname_rec.nspname;
        dropsql := 'DROP SCHEMA IF EXISTS '||nsname_rec.nspname|| '  CASCADE;';
        EXECUTE dropsql;
    END LOOP;
    CLOSE cur1;
    RETURN true;
END;
$body$
    VOLATILE
    COST 100;

SELECT public.schema_bulk_drop();
DROP FUNCTION public.schema_bulk_drop();
--
-- Roles
--
DROP SCHEMA IF EXISTS sample CASCADE;

ALTER ROLE sample WITH SUPERUSER INHERIT NOCREATEROLE NOCREATEDB LOGIN NOREPLICATION NOBYPASSRLS;
ALTER ROLE sample SET search_path TO 'sample', 'public';


--
-- Role memberships
--




DROP SCHEMA IF EXISTS sample CASCADE;

CREATE SCHEMA sample;


-- master
DROP TABLE IF EXISTS sample.master;
CREATE TABLE sample.master (
  master_type CHAR(1) NOT NULL CHECK(master_type IN ('1', '2')),
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_user VARCHAR(10) NOT NULL DEFAULT '',
  CONSTRAINT sample_master_pkey PRIMARY KEY (master_type)
);

COMMENT ON TABLE sample.master IS 'sample-master';
COMMENT ON COLUMN sample.master.master_type IS 'master-type';
COMMENT ON COLUMN sample.master.start_date IS '開始日';
COMMENT ON COLUMN sample.master.end_date IS '終了日';
COMMENT ON COLUMN sample.master.created_at IS '登録日時';
COMMENT ON COLUMN sample.master.created_user IS '登録者';

-- exam 試験
DROP TABLE IF EXISTS sample.grants;
CREATE TABLE sample.grants (
    grant_id INTEGER NOT NULL,
    name     VARCHAR(10) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_user VARCHAR(10) NOT NULL DEFAULT '',
    CONSTRAINT sample_grants_pkey PRIMARY KEY (grant_id)
);

COMMENT ON TABLE sample.grants IS 'sample-master';
COMMENT ON COLUMN sample.grants.grant_id IS 'master-type';
COMMENT ON COLUMN sample.grants.name IS '開始日';
COMMENT ON COLUMN sample.grants.start_date IS '開始日';
COMMENT ON COLUMN sample.grants.end_date IS '終了日';
COMMENT ON COLUMN sample.grants.created_at IS '登録日時';
COMMENT ON COLUMN sample.grants.created_user IS '登録者';

-- user sampleユーザー
DROP TABLE IF EXISTS sample.users;
CREATE TABLE sample.users (
  user_id INTEGER NOT NULL,
  access_id VARCHAR(7) NOT NULL,
  password VARCHAR(256) NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  readonly_flag BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_user VARCHAR(10) NOT NULL DEFAULT '',
  CONSTRAINT users_pkey PRIMARY KEY (user_id),
  CONSTRAINT users_ukey UNIQUE (access_id)
);

COMMENT ON TABLE sample.users IS 'sampleユーザー';
COMMENT ON COLUMN sample.users.user_id IS 'sampleユーザーID';
COMMENT ON COLUMN sample.users.access_id IS 'sample認証用ID';
COMMENT ON COLUMN sample.users.password IS 'パスワード';
COMMENT ON COLUMN sample.users.expires_at IS '有効日時';
COMMENT ON COLUMN sample.users.readonly_flag IS '読み取り専用フラグ';
COMMENT ON COLUMN sample.users.created_at IS '登録日時';
COMMENT ON COLUMN sample.users.created_user IS '登録者';


-- sample-user-grants
DROP TABLE IF EXISTS sample.user_grants;
CREATE TABLE sample.user_grants (
    user_id INTEGER NOT NULL,
    grant_id INTEGER NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_user VARCHAR(10) NOT NULL DEFAULT '',
    CONSTRAINT user_grants_ukey UNIQUE (user_id,grant_id),
    CONSTRAINT user_grants_fkey FOREIGN KEY (user_id)
        REFERENCES sample.users (user_id)
        ON UPDATE NO ACTION ON DELETE CASCADE,
    CONSTRAINT user_grants_fkey2 FOREIGN KEY (grant_id)
        REFERENCES sample.grants (grant_id)
        ON UPDATE NO ACTION ON DELETE CASCADE
);

COMMENT ON TABLE sample.user_grants IS 'sampleユーザー';
COMMENT ON COLUMN sample.user_grants.user_id IS 'sampleユーザーID';
COMMENT ON COLUMN sample.user_grants.grant_id IS 'ユーザー権限';
COMMENT ON COLUMN sample.user_grants.created_at IS '登録日時';
COMMENT ON COLUMN sample.user_grants.created_user IS '登録者';


-- userが持つアドレス
DROP TABLE IF EXISTS sample.user_addresses;
CREATE TABLE sample.user_addresses (
    user_id INTEGER NOT NULL,
    zip     CHAR(8) NOT NULL,
    pref_code VARCHAR(2) NOT NULL,
    address text NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_user VARCHAR(10) NOT NULL DEFAULT '',
    CONSTRAINT user_addresses_ukey UNIQUE (user_id,address),
    CONSTRAINT user_addresses_fkey FOREIGN KEY (user_id)
        REFERENCES sample.users (user_id)
        ON UPDATE NO ACTION ON DELETE CASCADE
);

COMMENT ON TABLE sample.user_addresses IS 'sampleユーザー';
COMMENT ON COLUMN sample.user_addresses.user_id IS 'sampleユーザーID';
COMMENT ON COLUMN sample.user_addresses.zip IS '郵便番号';
COMMENT ON COLUMN sample.user_addresses.pref_code IS '県コード';
COMMENT ON COLUMN sample.user_addresses.address IS '県名市町村含む住所';
COMMENT ON COLUMN sample.user_addresses.created_at IS '登録日時';
COMMENT ON COLUMN sample.user_addresses.created_user IS '登録者';


DROP TABLE IF EXISTS sample.user_refresh_tokens;
CREATE TABLE sample.user_refresh_tokens (
  refresh_token VARCHAR(256) NOT NULL,
  user_id INTEGER NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  signs_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_user VARCHAR(10) NOT NULL DEFAULT '',
  CONSTRAINT user_refresh_tokens_pkey PRIMARY KEY (refresh_token),
  CONSTRAINT user_refresh_tokens_fkey FOREIGN KEY (user_id)
    REFERENCES sample.users (user_id)
      ON UPDATE NO ACTION ON DELETE CASCADE
);

COMMENT ON TABLE sample.user_refresh_tokens IS 'sampleユーザーリフレッシュトークン';
COMMENT ON COLUMN sample.user_refresh_tokens.refresh_token IS 'リフレッシュトークン';
COMMENT ON COLUMN sample.user_refresh_tokens.user_id IS 'sampleユーザーID';
COMMENT ON COLUMN sample.user_refresh_tokens.expires_at IS '有効日時';
COMMENT ON COLUMN sample.user_refresh_tokens.signs_at IS '最終ログイン日時';
COMMENT ON COLUMN sample.user_refresh_tokens.created_at IS '登録日時';
COMMENT ON COLUMN sample.user_refresh_tokens.created_user IS '登録者';

DROP TABLE IF EXISTS sample.user_profiles;
CREATE TABLE sample.user_profiles (
    user_profile_id SERIAL,
    user_id INTEGER NOT NULL,
    name    VARCHAR(256) NOT NULL,
    tel     VARCHAR(15) NOT NULL,
    mail    VARCHAR(256) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_user VARCHAR(10) NOT NULL DEFAULT '',
    CONSTRAINT user_profiles_pkey PRIMARY KEY (user_profile_id),
    CONSTRAINT user_profiles_fkey FOREIGN KEY (user_id)
        REFERENCES sample.users (user_id)
        ON UPDATE NO ACTION ON DELETE CASCADE
);

COMMENT ON TABLE sample.user_profiles IS 'ユーザー詳細情報';
COMMENT ON COLUMN sample.user_profiles.user_profile_id IS 'サロゲート';
COMMENT ON COLUMN sample.user_profiles.user_id IS 'sampleユーザーID';
COMMENT ON COLUMN sample.user_profiles.name IS '名前';
COMMENT ON COLUMN sample.user_profiles.tel IS '電話番号';
COMMENT ON COLUMN sample.user_profiles.mail IS 'メールアアドレス';
COMMENT ON COLUMN sample.user_profiles.created_at IS '登録日時';
COMMENT ON COLUMN sample.user_profiles.created_user IS '登録者';

DROP TABLE IF EXISTS sample.merchants;
CREATE TABLE sample.merchants (
    merchant_id INTEGER NOT NULL,
    password    VARCHAR(256) NOT NULL,
    name        VARCHAR(256) NOT NULL,
    tel         VARCHAR(15) NOT NULL,
    mail        VARCHAR(256) NOT NULL,
    zip         CHAR(8) NOT NULL,
    pref_code   VARCHAR(2) NOT NULL,
    address     text NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_user VARCHAR(10) NOT NULL DEFAULT '',
    CONSTRAINT user_merchants_pkey PRIMARY KEY (merchant_id)
);

COMMENT ON TABLE sample.merchants IS 'ユーザー詳細情報';
COMMENT ON COLUMN sample.merchants.merchant_id IS 'サロゲート';
COMMENT ON COLUMN sample.merchants.name IS '名前';
COMMENT ON COLUMN sample.merchants.password IS 'パスワード';
COMMENT ON COLUMN sample.merchants.tel IS '電話番号';
COMMENT ON COLUMN sample.merchants.mail IS 'メールアアドレス';
COMMENT ON COLUMN sample.merchants.zip IS '郵便番号';
COMMENT ON COLUMN sample.merchants.pref_code IS '県コード';
COMMENT ON COLUMN sample.merchants.address IS '県名市町村含む住所';
COMMENT ON COLUMN sample.merchants.created_at IS '登録日時';
COMMENT ON COLUMN sample.merchants.created_user IS '登録者';

-- 企業-ユーザーリレーション
DROP TABLE IF EXISTS sample.merchant_x_users;
CREATE TABLE sample.merchant_x_users (
    merchant_id INTEGER NOT NULL,
    user_id     INTEGER NOT NULL,
    is_admin    BOOLEAN NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_user VARCHAR(10) NOT NULL DEFAULT '',
    CONSTRAINT merchant_x_users_fkey FOREIGN KEY (merchant_id)
        REFERENCES sample.merchants (merchant_id)
        ON UPDATE NO ACTION ON DELETE CASCADE,
    CONSTRAINT merchant_x_users_fkey2 FOREIGN KEY (user_id)
        REFERENCES sample.users (user_id)
        ON UPDATE NO ACTION ON DELETE CASCADE
);

COMMENT ON TABLE sample.merchant_x_users IS '企業-ユーザーリレーション';
COMMENT ON COLUMN sample.merchant_x_users.merchant_id IS '企業ID';
COMMENT ON COLUMN sample.merchant_x_users.user_id IS 'ユーザーID';
COMMENT ON COLUMN sample.merchant_x_users.is_admin IS '管理者権限';
COMMENT ON COLUMN sample.merchant_x_users.created_at IS '登録日時';
COMMENT ON COLUMN sample.merchant_x_users.created_user IS '登録者';



--　
CREATE TABLE sample.prefectures (
    id integer NOT NULL,
    name text
);


-- 拠点
CREATE SEQUENCE sample.seq_work_place_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 9223372036854770000
    CACHE 1;
CREATE TABLE sample.work_places (
    work_place_id integer DEFAULT nextval('sample.seq_work_place_id'::regclass) NOT NULL,
    work_place_name text NOT NULL,
    CONSTRAINT work_places_pkey PRIMARY KEY (work_place_id)
);

-- 拠点が保有する端末情報
CREATE SEQUENCE sample.seq_device_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 9223372036854770000
    CACHE 1;
CREATE TABLE sample.devices (
    id integer DEFAULT nextval('sample.seq_device_id'::regclass) NOT NULL,
    name text NOT NULL,
    ip_address text NOT NULL,
    work_place_id integer NOT NULL,
    user_id integer NOT NULL,
    CONSTRAINT devices_pkey PRIMARY KEY (id),
    CONSTRAINT devices_ukey UNIQUE (ip_address),
    CONSTRAINT devices_fkey1 FOREIGN KEY (work_place_id)
        REFERENCES sample.work_places (work_place_id)
        ON UPDATE NO ACTION ON DELETE CASCADE,
    CONSTRAINT devices_fkey2 FOREIGN KEY (user_id)
        REFERENCES sample.users (user_id)
        ON UPDATE NO ACTION ON DELETE CASCADE
);



create table sample.table_batch_audit (
    id serial
    , target_table_name text not null
    , record_cnt integer not null
    , before_record_cnt integer not null
    , diff_cnt integer not null
    , status CHAR(1) not null CHECK(status IN ('1', '2', '3'))
    , apply_date timestamp with time zone not null
    , implementation_date timestamp with time zone
    , user_id text not null default CURRENT_USER
    , create_date timestamp with time zone not null
    , audit_date timestamp with time zone default now()
    , constraint table_batch_audit_pkey primary key (id)
) ;

CREATE TABLE sample.yuseiooguchijigyoushoyubinbangous (
    jis character(5) NOT NULL,
    ooguchijigyoushotou_kana character varying(100) NOT NULL,
    ooguchijigyoushotou_na_kanji character varying(160) NOT NULL,
    todoufuken_kanji character varying(8) NOT NULL,
    shikuchouson_kanji character varying(24) NOT NULL,
    chouiki_kanji character varying(24) NOT NULL,
    banchi_kanji character varying(124) NOT NULL,
    ooguchijigyoushotou_kobetsu_bangou character(7) NOT NULL,
    genkou_yubinbangou character(5) NOT NULL,
    toriatsukai_yubinkyokumei_kanji character varying(40) NOT NULL,
    kobetsubangoushubetsu_kbn character(1) NOT NULL,
    fukusubangouumu_kbn character(1) NOT NULL,
    shusei_kbn character(11) NOT NULL,
    program_name text
);

CREATE TABLE sample.yuseiyubinbangous (
    jis character(5) NOT NULL,
    kyu_yubinbangou character(5) NOT NULL,
    yubinbangou character(7) NOT NULL,
    todoufuken_kana character varying(10) NOT NULL,
    shikuchouson_kana character varying(100) NOT NULL,
    chouiki_kana character varying(100) NOT NULL,
    todoufuken_kanji character varying(8) NOT NULL,
    shikuchouson_kanji character varying(100) NOT NULL,
    chouiki_kanji character varying(100) NOT NULL,
    fukusu_yubinbangou_kbn character(1) NOT NULL,
    koazagoto_banchi_kbn character(1) NOT NULL,
    choumu_yuchouiki_kbn character(1) NOT NULL,
    fukusu_chouiki_kbn character(1) NOT NULL,
    koushin_hyoji_kbn character(1) NOT NULL,
    henkou_riyu_kbn character(1) NOT NULL,
    program_name text
);

-- 郵政郵便番号監視
--* RestoreFromTempTable
create table sample.yuseiyubinbangous_audit (
     jis character(5) not null
    , kyu_yubinbangou character(5) not null
    , yubinbangou character(7) not null
    , todoufuken_kana character varying(10) not null
    , shikuchouson_kana character varying(100) not null
    , chouiki_kana character varying(100) not null
    , todoufuken_kanji character varying(8) not null
    , shikuchouson_kanji character varying(100) not null
    , chouiki_kanji character varying(100) not null
    , fukusu_yubinbangou_kbn character(1) not null
    , koazagoto_banchi_kbn character(1) not null
    , choumu_yuchouiki_kbn character(1) not null
    , fukusu_chouiki_kbn character(1) not null
    , koushin_hyoji_kbn character(1) not null
    , henkou_riyu_kbn character(1) not null
    , program_name text
    , action_type CHAR(1) NOT NULL CHECK(action_type IN ('1', '2','3'))
    , audit_date timestamp with time zone default now() not null
) ;



/**
  * 履歴更新ストアド
 */

CREATE OR REPLACE FUNCTION sample.fnc_logged_audit()
    RETURNS trigger
    LANGUAGE plpgsql
AS
$body$
DECLARE
    audit_table_name text;
    new_pivot record;
    t_column_text text;
    t_value_text text;
    audit_sql text;
    target_rec record;
    action_type char(1);
BEGIN
    --履歴トリガー関数
    BEGIN
        IF(TG_OP = 'INSERT') THEN action_type := 1;
        ELSEIF(TG_OP = 'UPDATE') THEN action_type := 2;
        ELSE action_type := 3;
        END IF;
        audit_table_name :=  TG_TABLE_NAME||'_audit';
        IF (TG_OP = 'INSERT' OR TG_OP = 'UPDATE'  ) THEN
            target_rec := NEW;
        ELSE
            target_rec := OLD;
        END IF;
        t_column_text  := '';
        t_value_text  := '';
        -- NEWまたはOLDをjson成形後、pivotしたものをLOOP、column,valueを利用してSQLを生成する
        FOR new_pivot in SELECT * FROM json_each_text((SELECT to_json(target_rec)))as a
            LOOP
            --RAISE WARNING 'test:%',new_pivot.key;
            --RAISE WARNING 'test:%',new_pivot.value;
                t_column_text := t_column_text||','||new_pivot.key;
                IF new_pivot.value IS NULL THEN
                    t_value_text := t_value_text||',null';
                ELSE
                    t_value_text := t_value_text||','||''''||new_pivot.value||'''';
                END IF;
            END LOOP;
        t_column_text := t_column_text||',action_type';
        t_value_text := t_value_text||','||''''||action_type||'''';
        SELECT  INTO t_column_text (SELECT substr(t_column_text,2));
        SELECT  INTO t_value_text (SELECT substr(t_value_text,2));
        audit_sql := 'INSERT INTO  sample.'||audit_table_name|| ' ('||t_column_text||') VALUES('||t_value_text||')';
        --RAISE WARNING 'test:%', audit_sql;
        EXECUTE audit_sql;
    EXCEPTION
        WHEN division_by_zero THEN
            RAISE WARNING 'caught division_by_zero';
            RETURN NULL;
        WHEN OTHERS THEN
            RAISE WARNING  'fnc_logged_audit ERROR:%',sqlstate;
            RETURN NULL;
    END;
    RETURN NULL;
END;
$body$
    VOLATILE
    COST 100;

ALTER FUNCTION            sample.fnc_logged_audit() OWNER TO sample;
GRANT EXECUTE ON FUNCTION sample.fnc_logged_audit() TO PUBLIC;




/**
  * yuseiyubinbangous更新履歴
  */
CREATE TRIGGER trg_yuseiyubinbangous_z AFTER INSERT OR UPDATE OR DELETE
    ON sample.yuseiyubinbangous FOR EACH ROW
EXECUTE PROCEDURE sample.fnc_logged_audit();


INSERT INTO sample.prefectures
VALUES
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


