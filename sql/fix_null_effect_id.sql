-- first get rid of any entires with blank effect_name's
delete FROM effects_user_hdr
where length(`effect_name`)<1;


delete FROM effects_user_dtl
where length(`effect_name`)<1;

-- fix bad frame delay values
update `effects_user_dtl` 
set param_value=100
 WHERE param_name='frame_delay'
and (param_value+0) < 1



update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\/', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\&', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\'', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\:', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\>', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\+', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , '\?', '_');



update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\&', '_');
update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\'', '_');
update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\:', '_');
update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\>', '_');
update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\+', '_');
update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\?', '_');






update `effects_user_segment`
SET effect_name = REPLACE (effect_name , '\/', '_');
update `effects_user_segment`
SET effect_name = REPLACE (effect_name , '\&', '_');
update `effects_user_segment`
SET effect_name = REPLACE (effect_name , '\'', '_');
update `effects_user_segment`
SET effect_name = REPLACE (effect_name , '\:', '_');
update `effects_user_segment`
SET effect_name = REPLACE (effect_name , '\>', '_');

update `gallery`
SET effect_name = REPLACE (effect_name , '\/', '_');
update `gallery`
SET effect_name = REPLACE (effect_name , '\&', '_');
update `gallery`
SET effect_name = REPLACE (effect_name , '\'', '_');
update `gallery`
SET effect_name = REPLACE (effect_name , '\:', '_');
update `gallery`
SET effect_name = REPLACE (effect_name , '\>', '_');
update `gallery`
SET effect_name = REPLACE (effect_name , '\+', '_');
update `gallery`
SET effect_name = REPLACE (effect_name , '\?', '_');




update `project_dtl`
SET effect_name = REPLACE (effect_name , '\/', '_');

update `project_dtl`
SET effect_name = REPLACE (effect_name , '\&', '_');

update `project_dtl`
SET effect_name = REPLACE (effect_name , '\'', '_');

update `project_dtl`
SET effect_name = REPLACE (effect_name , '\:', '_');

-- fix null effect_id's in user_effects_id

update `effects_user_dtl` a, effects_user_hdr b
 set a.effect_id=b.effect_id
 WHERE a.username=b.username
and a.effect_name=b.effect_name
and a.effect_id is null;




-- fix null effect_id's iun PROJECT_DTL
UPDATE `project_dtl` a, project b, effects_user_hdr c
 set a.effect_id = c.effect_id
 WHERE a.project_id = b.project_id
 and b.username = c.username
 and a.effect_name=c.effect_name
and a.effect_id is null;

-- Fix null effect name in projects
update project_dtl a, `effects_user_hdr` b
set a.effect_name = b.effect_name
 WHERE b.effect_id = a.effect_id
and length(a.effect_name) < 1;

-- show null effect_id's iun PROJECT_DTL

SELECT b.username,a.*,c.effect_id effect_id2
 FROM `project_dtl` a, project b,
effects_user_hdr c
 WHERE a.project_id = b.project_id
 and b.username = c.username
 and a.effect_name=c.effect_name
and a.effect_id is null;

-- show null effect_id in project_dtl
SELECT * FROM `project_dtl` a,
project b 
WHERE a.project_id = b.project_id
and a.effect_id is null;



update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , '\/', '_');


update `effects_user_dtl`
SET effect_name = REPLACE (effect_name , ' ', '_');
update `effects_user_hdr`
SET effect_name = REPLACE (effect_name , ' ', '_');
update `effects_user_segment`
SET effect_name = REPLACE (effect_name , ' ', '_');

update effects_user_dtl
set param_value = effect_name
where param_name='effect_name'
and param_value <> effect_name;

