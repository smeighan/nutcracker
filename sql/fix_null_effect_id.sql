// fix null effect_id's in user_effects_id

update `effects_user_dtl` a, effects_user_hdr b
 set a.effect_id=b.effect_id
 WHERE a.username=b.username
and a.effect_name=b.effect_name
and a.effect_id is null;


// show null effect_id's iun PROJECT_DTL

SELECT b.username,a.*,c.effect_id effect_id2
 FROM `project_dtl` a, project b,
effects_user_hdr c
 WHERE a.project_id = b.project_id
 and b.username = c.username
 and a.effect_name=c.effect_name
and a.effect_id is null

// fix null effect_id's iun PROJECT_DTL
UPDATE `project_dtl` a, project b, effects_user_hdr c
 set a.effect_id = c.effect_id
 WHERE a.project_id = b.project_id
 and b.username = c.username
 and a.effect_name=c.effect_name
and a.effect_id is null

// show null effect_id in project_dtl
SELECT * FROM `project_dtl` a,
project b 
WHERE a.project_id = b.project_id
and a.effect_id is null
