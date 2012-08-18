insert into effects_dtl select 'Y','bars',param_name,param_prompt, param_desc,param_range,now(),now(),sequence 
	   from effects_dtl where effect_class='pictures'