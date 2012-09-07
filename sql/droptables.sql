CREATE DATABASE IF NOT EXISTS nutcracker;

	DROP TABLE IF EXISTS audit_log;
	DROP TABLE IF EXISTS effects_dtl;
	DROP TABLE IF EXISTS effects_hdr;
	DROP TABLE IF EXISTS effects_user_dtl;
	DROP TABLE IF EXISTS effects_user_hdr;
	DROP TABLE IF EXISTS gallery;
	DROP TABLE IF EXISTS library_dtl;
	DROP TABLE IF EXISTS library_hdr;
	DROP TABLE IF EXISTS members;
	DROP TABLE IF EXISTS models;
	DROP TABLE IF EXISTS models_strands;
	DROP TABLE IF EXISTS models_strand_segments;
	DROP TABLE IF EXISTS music_object_dtl;
	DROP TABLE IF EXISTS music_object_hdr;
	DROP TABLE IF EXISTS music_object_votes;
	DROP TABLE IF EXISTS music_object_votes_count;
	DROP TABLE IF EXISTS snowstorm;