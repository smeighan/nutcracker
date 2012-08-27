create table 20120826_audit_log as select * from audit_log;
create table 20120826_effects_dtl as select * from effects_dtl;
create table 20120826_effects_hdr as select * from effects_hdr;
create table 20120826_effects_user_dtl as select * from effects_user_dtl;
create table 20120826_effects_user_hdr as select * from effects_user_hdr;
create table 20120826_library_dtl as select * from library_dtl;
create table 20120826_library_hdr as select * from library_hdr;
create table 20120826_gallery as select * from gallery;
create table 20120826_members as select * from members;
create table 20120826_models as select * from models;
create table 20120826_snowstorm as select * from snowstorm;
create table 20120826_music_object_hdr as select * from music_object_hdr;
create table 20120826_music_object_dtl as select * from music_object_dtl;

-- GRANT ALL ON *.* TO '$DB_USER'@$DB_HOST IDENTIFIED BY '$DB_PASSWORD'"

-- GRANT ALL ON *.* TO 'nc_user'@localhost IDENTIFIED BY 'nutcracker123'"
--define('DB_HOST',     'localhost');
--define('DB_USER',     'nc_user');
--define('DB_PASSWORD', 'nutcracker123');
--define('DB_DATABASE', 'nutcracker');
