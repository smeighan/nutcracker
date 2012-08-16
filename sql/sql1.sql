SELECT * FROM effects_user_hdr a, effects_user_dtl b, effects_dtl c
where a.username=b.username
and a.effect_name=b.effect_name
and a.effect_class=c.effect_class
and a.effect_class='butterfly'
 and a.username='f' 
order by a.username,a.effect_name,c.sequence
