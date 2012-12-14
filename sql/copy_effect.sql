insert into effects_dtl select 'Y','tree',param_name,
param_prompt, param_desc,param_range,'',now(),now(),sequence 
	   from effects_dtl where effect_class='twinkle'
	and sequence >= 8 and sequence <=15

